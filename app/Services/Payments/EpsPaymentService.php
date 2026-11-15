<?php

namespace App\Services\Payments;

use App\Enums\PaymentStatus;
use App\Exceptions\EpsPaymentException;
use App\Models\Account;
use App\Models\BalancePaymentTransaction;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Throwable;

class EpsPaymentService
{
    public function generateHash(string $payload, ?string $hashKey = null): string
    {
        $key = $hashKey ?? (string) config('eps.credentials.hash_key');

        return base64_encode(hash_hmac('sha512', $payload, $key, true));
    }

    public function runDiagnostics(): void
    {
        $this->assertProductionConfiguration();
        $this->assertDatabaseReady();

        $diagnosticReference = 'EPS'.now()->format('YmdHis').'00000000';
        $diagnosticToken = str_repeat('0', 64);

        foreach (['success', 'fail', 'cancel'] as $type) {
            $this->callbackUrl($type, $diagnosticReference, $diagnosticToken);
        }

        // Authenticates against EPS without initializing or charging a payment.
        $this->getToken(true);
    }

    /**
     * @return array{payment: BalancePaymentTransaction, redirect_url: string}
     */
    public function createPayment(User $user, float $amount, string $ipAddress): array
    {
        $this->assertProductionConfiguration();
        $this->assertDatabaseReady();

        $account = $user->account()->first();
        if (! $account) {
            throw new EpsPaymentException('Your balance account is unavailable. Please contact support.');
        }

        if ((float) (DB::table('app_setups')->where('id', 1)->value('mvr_to_bdt') ?? 0) <= 0) {
            throw new EpsPaymentException('The BDT to MVR exchange rate is unavailable. Please contact support.');
        }

        if (trim((string) $user->phone) === '') {
            throw new EpsPaymentException('Please add a phone number to your profile before paying with EPS.');
        }

        if (trim((string) $user->name) === '') {
            throw new EpsPaymentException('Please add your name to your profile before paying with EPS.');
        }

        $gatewayAmount = round($amount, 2);
        $balanceAmount = 0;

        $transactionId = $this->newTransactionId();
        $customerOrderId = 'BAL'.now()->format('YmdHis').Str::upper(Str::random(6));
        $callbackToken = Str::random(64);

        $payment = BalancePaymentTransaction::query()->create([
            'user_id' => $user->id,
            'account_id' => $account->id,
            'transaction_id' => $transactionId,
            'gateway' => 'eps',
            'amount' => $gatewayAmount,
            'currency' => (string) config('eps.currency', 'MVR'),
            'balance_amount' => $balanceAmount,
            'balance_currency' => (string) config('eps.balance_currency', 'MVR'),
            'status' => PaymentStatus::Pending,
            'merchant_id_hash' => $this->credentialFingerprint('merchant_id'),
            'store_id_hash' => $this->credentialFingerprint('store_id'),
            'callback_token_hash' => hash('sha256', $callbackToken),
            'status_log' => [
                $this->statusEntry(PaymentStatus::Pending, 'created'),
            ],
        ]);

        try {
            $payload = $this->buildInitializePayload(
                $payment,
                $user,
                $customerOrderId,
                $callbackToken,
                $ipAddress,
            );
        } catch (EpsPaymentException $exception) {
            $this->transition(
                $payment,
                PaymentStatus::Failed,
                'configuration_error',
                failedReason: $exception->getMessage(),
            );

            throw $exception;
        }

        $payment->forceFill([
            'request_payload' => $this->sanitizePayload($payload, [$callbackToken]),
        ])->save();

        return $this->initializePayment($payment, $payload);
    }

    /**
     * @param  array<string, mixed>|null  $payload
     * @return array{payment: BalancePaymentTransaction, redirect_url: string}
     */
    public function initializePayment(BalancePaymentTransaction $payment, ?array $payload = null): array
    {
        $this->assertProductionConfiguration();
        $this->assertDatabaseReady();

        $payload ??= $payment->request_payload ?? [];
        $payment = $this->transition($payment, PaymentStatus::Processing, 'initialization_started');

        try {
            $token = $this->getToken();
            $response = $this->baseClient()
                ->withToken($token)
                ->withHeaders(['x-hash' => $this->generateHash($payment->transaction_id)])
                ->post($this->endpoint('initialize'), $payload);

            if (in_array($response->status(), [401, 403], true)) {
                $token = $this->getToken(true);
                $response = $this->baseClient()
                    ->withToken($token)
                    ->withHeaders(['x-hash' => $this->generateHash($payment->transaction_id)])
                    ->post($this->endpoint('initialize'), $payload);
            }
        } catch (EpsPaymentException $exception) {
            $this->transition(
                $payment,
                $exception->retryable ? PaymentStatus::Processing : PaymentStatus::Failed,
                'authentication_failed',
                failedReason: $exception->getMessage(),
            );

            throw $exception;
        } catch (ConnectionException $exception) {
            $this->transition(
                $payment,
                PaymentStatus::Processing,
                'initialization_connection_error',
                failedReason: 'EPS initialization result is unknown because the gateway could not be reached.',
            );

            throw new EpsPaymentException(
                'EPS did not respond in time. No balance has been added; use Check Status before trying another payment.',
                true,
                $exception,
            );
        }

        $body = $this->responseJson($response);
        if (! $response->successful()) {
            $retryable = $response->serverError();
            $gatewayErrorCode = trim((string) ($this->valueFrom($body, ['ErrorCode']) ?? ''));
            $gatewayErrorMessage = trim((string) ($this->valueFrom($body, ['ErrorMessage']) ?? ''));
            $this->transition(
                $payment,
                $retryable ? PaymentStatus::Processing : PaymentStatus::Failed,
                'initialization_rejected',
                array_filter([
                    'http_status' => $response->status(),
                    'gateway_error_code' => $gatewayErrorCode !== '' ? $gatewayErrorCode : null,
                ]),
                'initialize',
                $body,
                $retryable
                    ? 'EPS initialization is awaiting confirmation after a gateway error.'
                    : ($gatewayErrorMessage !== ''
                        ? 'EPS rejected payment initialization: '.$gatewayErrorMessage
                        : 'EPS rejected the payment initialization request.'),
            );

            throw new EpsPaymentException(
                $retryable
                    ? 'EPS is temporarily unavailable. No balance has been added; please check this transaction later.'
                    : 'EPS rejected the payment request'.($gatewayErrorCode !== '' ? " (code {$gatewayErrorCode})" : '').'. Please verify your profile details and try again.',
                $retryable,
            );
        }

        $redirectUrl = (string) ($this->valueFrom($body, ['RedirectURL']) ?? '');
        $gatewaySessionId = (string) ($this->valueFrom($body, ['TransactionId']) ?? '');

        if ($redirectUrl === '' || $gatewaySessionId === '') {
            $this->transition(
                $payment,
                PaymentStatus::Failed,
                'initialization_invalid_response',
                responseKey: 'initialize',
                response: $body,
                failedReason: 'EPS returned an incomplete initialization response.',
            );

            throw new EpsPaymentException('EPS returned an incomplete payment response. Please try again later.');
        }

        try {
            $this->assertProductionRedirect($redirectUrl);
        } catch (EpsPaymentException $exception) {
            $this->transition(
                $payment,
                PaymentStatus::Failed,
                'invalid_redirect_url',
                responseKey: 'initialize',
                response: $body,
                failedReason: $exception->getMessage(),
            );

            throw $exception;
        }

        $payment = $this->recordInitializationSuccess($payment, $gatewaySessionId, $body);

        return [
            'payment' => $payment->fresh(),
            'redirect_url' => $redirectUrl,
        ];
    }

    /** @param array<string, mixed> $response */
    private function recordInitializationSuccess(
        BalancePaymentTransaction $payment,
        string $gatewaySessionId,
        array $response,
    ): BalancePaymentTransaction {
        return DB::transaction(function () use ($payment, $gatewaySessionId, $response) {
            $locked = BalancePaymentTransaction::query()
                ->lockForUpdate()
                ->findOrFail($payment->id);

            $alreadyLinked = BalancePaymentTransaction::query()
                ->where('id', '!=', $locked->id)
                ->where('gateway_session_id', $gatewaySessionId)
                ->exists();

            if ($alreadyLinked) {
                throw new EpsPaymentException('The EPS checkout session is already linked to another payment.');
            }

            $statusLog = $locked->status_log ?? [];
            $statusLog[] = $this->statusEntry(
                PaymentStatus::Processing,
                'initialized',
                ['gateway_session_id' => $gatewaySessionId],
            );

            $responses = $locked->response_payload ?? [];
            $responses['initialize'] = $this->sanitizePayload($response);

            $locked->forceFill([
                'gateway_session_id' => $gatewaySessionId,
                'status' => PaymentStatus::Processing,
                'response_payload' => $responses,
                'status_log' => $statusLog,
                'failed_reason' => null,
            ])->save();

            return $locked->fresh();
        }, 3);
    }

    /**
     * @return array{status: PaymentStatus, gateway_status: string, gateway_transaction_id: string, response: array<string, mixed>}
     */
    public function verifyPayment(BalancePaymentTransaction $payment): array
    {
        $this->assertProductionConfiguration();
        $this->assertDatabaseReady();
        $this->assertCredentialFingerprints($payment);
        $this->assertSupportedPayment($payment);

        $payment = $this->transition($payment, PaymentStatus::Processing, 'verification_started');

        try {
            $token = $this->getToken();
            $response = $this->retryingClient()
                ->withToken($token)
                ->withHeaders(['x-hash' => $this->generateHash($payment->transaction_id)])
                ->get($this->endpoint('verify'), [
                    'merchantTransactionId' => $payment->transaction_id,
                ]);

            if (in_array($response->status(), [401, 403], true)) {
                $token = $this->getToken(true);
                $response = $this->retryingClient()
                    ->withToken($token)
                    ->withHeaders(['x-hash' => $this->generateHash($payment->transaction_id)])
                    ->get($this->endpoint('verify'), [
                        'merchantTransactionId' => $payment->transaction_id,
                    ]);
            }
        } catch (EpsPaymentException $exception) {
            $this->transition(
                $payment,
                PaymentStatus::Processing,
                'verification_authentication_failed',
                failedReason: $exception->getMessage(),
            );

            throw $exception;
        } catch (ConnectionException $exception) {
            $this->transition(
                $payment,
                PaymentStatus::Processing,
                'verification_connection_error',
                failedReason: 'EPS verification is temporarily unavailable.',
            );

            throw new EpsPaymentException(
                'EPS verification is temporarily unavailable. No balance has been added; please check again shortly.',
                true,
                $exception,
            );
        }

        $body = $this->responseJson($response);
        if (! $response->successful()) {
            Log::warning('EPS payment verification request was rejected.', [
                'payment_id' => $payment->id,
                'user_id' => $payment->user_id,
                'transaction_id' => $payment->transaction_id,
                'http_status' => $response->status(),
                'error_code' => $this->valueFrom($body, ['ErrorCode']),
                'error_message' => $this->valueFrom($body, ['ErrorMessage', 'message']),
            ]);

            $this->transition(
                $payment,
                PaymentStatus::Processing,
                'verification_rejected',
                ['http_status' => $response->status()],
                'verification',
                $body,
                'EPS verification did not complete successfully.',
            );

            throw new EpsPaymentException(
                'EPS could not verify this payment yet. No balance has been added; please check again shortly.',
                $response->serverError(),
            );
        }

        $verifiedTransactionId = (string) ($this->valueFrom($body, ['MerchantTransactionId']) ?? '');
        $verifiedGatewayTransactionId = trim((string) ($this->valueFrom($body, [
            'EpsTransactionId',
            'EPSTransactionId',
        ]) ?? ''));
        $verifiedAmount = $this->valueFrom($body, ['TotalAmount']);
        $gatewayStatus = trim((string) ($this->valueFrom($body, ['Status', 'TransactionStatus']) ?? ''));

        Log::info('EPS payment verification response received.', [
            'payment_id' => $payment->id,
            'user_id' => $payment->user_id,
            'transaction_id' => $payment->transaction_id,
            'gateway_transaction_id' => $verifiedGatewayTransactionId ?: null,
            'paid_amount' => $verifiedAmount,
            'currency' => $payment->currency,
            'gateway_status' => $gatewayStatus ?: null,
            'http_status' => $response->status(),
        ]);

        if ($verifiedTransactionId === '' || ! hash_equals($payment->transaction_id, $verifiedTransactionId)) {
            $this->verificationMismatch($payment, $body, 'EPS transaction reference mismatch.');
        }

        if ($verifiedAmount === null || $this->minorUnits($verifiedAmount) !== $this->minorUnits($payment->amount)) {
            $this->verificationMismatch($payment, $body, 'EPS paid amount does not match the requested amount.');
        }

        if ($verifiedGatewayTransactionId === '') {
            $this->verificationMismatch($payment, $body, 'EPS verification response did not contain an EPS transaction reference.');
        }

        if (
            $payment->gateway_transaction_id !== null
            && ! hash_equals($payment->gateway_transaction_id, $verifiedGatewayTransactionId)
        ) {
            $this->verificationMismatch($payment, $body, 'EPS transaction reference mismatch.');
        }

        if ($gatewayStatus === '') {
            $this->verificationMismatch($payment, $body, 'EPS verification response did not contain a payment status.');
        }

        $payment = $this->rememberGatewayTransactionId($payment, $verifiedGatewayTransactionId);

        return [
            'status' => $this->mapGatewayStatus($gatewayStatus),
            'gateway_status' => $gatewayStatus,
            'gateway_transaction_id' => $payment->gateway_transaction_id,
            'response' => $body,
        ];
    }

    private function rememberGatewayTransactionId(
        BalancePaymentTransaction $payment,
        string $gatewayTransactionId,
    ): BalancePaymentTransaction {
        return DB::transaction(function () use ($payment, $gatewayTransactionId) {
            $locked = BalancePaymentTransaction::query()
                ->lockForUpdate()
                ->findOrFail($payment->id);

            if (
                $locked->gateway_transaction_id !== null
                && ! hash_equals($locked->gateway_transaction_id, $gatewayTransactionId)
            ) {
                throw new EpsPaymentException('EPS transaction reference mismatch.');
            }

            $alreadyLinked = BalancePaymentTransaction::query()
                ->where('id', '!=', $locked->id)
                ->where('gateway_transaction_id', $gatewayTransactionId)
                ->exists();

            if ($alreadyLinked) {
                throw new EpsPaymentException('The EPS transaction reference is already linked to another payment.');
            }

            if ($locked->gateway_transaction_id === null) {
                $locked->forceFill(['gateway_transaction_id' => $gatewayTransactionId])->save();
            }

            return $locked->fresh();
        }, 3);
    }

    /** @param array<string, mixed> $callbackPayload */
    public function handleSuccess(
        BalancePaymentTransaction $payment,
        string $callbackToken,
        array $callbackPayload,
    ): BalancePaymentTransaction {
        return $this->handleCallback($payment, $callbackToken, $callbackPayload, 'success');
    }

    /** @param array<string, mixed> $callbackPayload */
    public function handleFailure(
        BalancePaymentTransaction $payment,
        string $callbackToken,
        array $callbackPayload,
    ): BalancePaymentTransaction {
        return $this->handleCallback($payment, $callbackToken, $callbackPayload, 'failure');
    }

    /** @param array<string, mixed> $callbackPayload */
    public function handleCancellation(
        BalancePaymentTransaction $payment,
        string $callbackToken,
        array $callbackPayload,
    ): BalancePaymentTransaction {
        return $this->handleCallback($payment, $callbackToken, $callbackPayload, 'cancellation');
    }

    /** @param array<string, mixed> $requestPayload */
    public function handleIpn(array $requestPayload): BalancePaymentTransaction
    {
        $payload = $this->resolveIpnPayload($requestPayload);
        $payment = $this->findPaymentFromPayload($payload);

        $this->validateGatewayReference($payment, $payload);

        $notifiedAmount = $this->valueFrom($payload, ['amount', 'TotalAmount']);
        if ($notifiedAmount !== null && $this->minorUnits($notifiedAmount) !== $this->minorUnits($payment->amount)) {
            throw new EpsPaymentException('The EPS notification amount did not match the payment record.');
        }

        $this->recordCallback($payment, 'ipn', $payload);

        return $this->verifyAndFinalize($payment);
    }

    public function checkStatus(BalancePaymentTransaction $payment): BalancePaymentTransaction
    {
        return $this->verifyAndFinalize($payment);
    }

    /** @param array<string, mixed> $payload */
    public function findPaymentFromPayload(array $payload): BalancePaymentTransaction
    {
        $transactionId = (string) ($this->valueFrom($payload, [
            'merchant_reference',
            'MerchantTransactionId',
            'merchant_transaction_id',
        ]) ?? '');

        if ($transactionId === '') {
            throw new EpsPaymentException('The EPS callback did not contain a valid transaction reference.');
        }

        $payment = BalancePaymentTransaction::query()
            ->where('gateway', 'eps')
            ->where('transaction_id', $transactionId)
            ->first();

        if (! $payment) {
            throw new EpsPaymentException('The EPS payment transaction could not be found.');
        }

        return $payment;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function handleCallback(
        BalancePaymentTransaction $payment,
        string $callbackToken,
        array $payload,
        string $callbackType,
    ): BalancePaymentTransaction {
        $this->validateCallback($payment, $callbackToken, $payload);
        $this->recordCallback($payment, $callbackType, $payload);

        return $this->verifyAndFinalize($payment);
    }

    private function verifyAndFinalize(BalancePaymentTransaction $payment): BalancePaymentTransaction
    {
        $payment->refresh();
        if ($payment->status === PaymentStatus::Successful) {
            return $payment;
        }

        $verification = $this->verifyPayment($payment);

        return match ($verification['status']) {
            PaymentStatus::Successful => $this->creditVerifiedPayment($payment, $verification),
            PaymentStatus::Failed => $this->transition(
                $payment,
                PaymentStatus::Failed,
                'verified_failed',
                ['gateway_status' => $verification['gateway_status']],
                'verification',
                $verification['response'],
                'EPS verified the payment as failed.',
                true,
            ),
            PaymentStatus::Cancelled => $this->transition(
                $payment,
                PaymentStatus::Cancelled,
                'verified_cancelled',
                ['gateway_status' => $verification['gateway_status']],
                'verification',
                $verification['response'],
                'EPS verified the payment as cancelled.',
                true,
            ),
            PaymentStatus::Expired => $this->transition(
                $payment,
                PaymentStatus::Expired,
                'verified_expired',
                ['gateway_status' => $verification['gateway_status']],
                'verification',
                $verification['response'],
                'EPS verified the payment as expired.',
                true,
            ),
            default => $this->transition(
                $payment,
                PaymentStatus::Processing,
                'verified_pending',
                ['gateway_status' => $verification['gateway_status']],
                'verification',
                $verification['response'],
                null,
                true,
            ),
        };
    }

    /**
     * @param  array{status: PaymentStatus, gateway_status: string, gateway_transaction_id: string, response: array<string, mixed>}  $verification
     */
    private function creditVerifiedPayment(
        BalancePaymentTransaction $payment,
        array $verification,
    ): BalancePaymentTransaction {
        return DB::transaction(function () use ($payment, $verification) {
            $lockedPayment = BalancePaymentTransaction::query()
                ->lockForUpdate()
                ->findOrFail($payment->id);

            if ($lockedPayment->status === PaymentStatus::Successful) {
                Log::info('Duplicate EPS credit was ignored.', [
                    'payment_id' => $lockedPayment->id,
                    'user_id' => $lockedPayment->user_id,
                    'transaction_id' => $lockedPayment->transaction_id,
                    'gateway_transaction_id' => $lockedPayment->gateway_transaction_id,
                ]);

                return $lockedPayment;
            }

            $this->assertCredentialFingerprints($lockedPayment);
            $this->assertSupportedPayment($lockedPayment);

            $exchangeRate = null;
            $balanceAmount = (float) $lockedPayment->amount;

            if (Str::upper(trim($lockedPayment->currency)) === 'BDT') {
                $exchangeRate = (float) (DB::table('app_setups')
                    ->where('id', 1)
                    ->lockForUpdate()
                    ->value('mvr_to_bdt') ?? 0);

                if ($exchangeRate <= 0) {
                    throw new EpsPaymentException('The BDT to MVR exchange rate is unavailable. No balance has been added.');
                }

                $balanceAmount = round((float) $lockedPayment->amount / $exchangeRate, 2);
            }

            $account = Account::query()
                ->lockForUpdate()
                ->findOrFail($lockedPayment->account_id);

            if ($account->user_id !== $lockedPayment->user_id) {
                throw new EpsPaymentException('The EPS payment account does not belong to the correct user.');
            }

            $previousBalance = (string) $account->balance;
            $newBalance = bcadd((string) $account->balance, (string) $balanceAmount, 2);
            $account->forceFill(['balance' => $newBalance])->save();

            $ledger = Transaction::query()->create([
                'account_id' => $account->id,
                'transaction_id' => $lockedPayment->transaction_id,
                'type' => 'deposit',
                'amount' => $balanceAmount,
                'balance_after' => $newBalance,
                'note' => $exchangeRate === null
                    ? sprintf('Verified legacy EPS payment: MVR %.2f', $balanceAmount)
                    : sprintf(
                        'Verified EPS payment: BDT %.2f converted to MVR %.2f at %.6f BDT/MVR',
                        $lockedPayment->amount,
                        $balanceAmount,
                        $exchangeRate,
                    ),
                'status' => 'approved',
            ]);

            $statusLog = $lockedPayment->status_log ?? [];
            $statusLog[] = $this->statusEntry(
                PaymentStatus::Successful,
                'balance_credited',
                [
                    'gateway_status' => $verification['gateway_status'],
                    'ledger_transaction_id' => $ledger->id,
                    'exchange_rate' => $exchangeRate,
                    'balance_amount' => $balanceAmount,
                ],
            );

            $responsePayload = $lockedPayment->response_payload ?? [];
            $responsePayload['verification'] = $this->sanitizePayload($verification['response']);

            $lockedPayment->forceFill([
                'ledger_transaction_id' => $ledger->id,
                'balance_amount' => $balanceAmount,
                'exchange_rate' => $exchangeRate,
                'status' => PaymentStatus::Successful,
                'response_payload' => $responsePayload,
                'status_log' => $statusLog,
                'verified_at' => now(),
                'failed_reason' => null,
            ])->save();

            DB::afterCommit(function () use ($lockedPayment, $ledger, $previousBalance, $newBalance, $balanceAmount, $exchangeRate): void {
                Log::info('EPS payment credited to user balance.', [
                    'payment_id' => $lockedPayment->id,
                    'user_id' => $lockedPayment->user_id,
                    'account_id' => $lockedPayment->account_id,
                    'transaction_id' => $lockedPayment->transaction_id,
                    'gateway_transaction_id' => $lockedPayment->gateway_transaction_id,
                    'paid_amount' => $lockedPayment->amount,
                    'paid_currency' => $lockedPayment->currency,
                    'exchange_rate_bdt_per_mvr' => $exchangeRate,
                    'credited_amount_mvr' => $balanceAmount,
                    'previous_balance_mvr' => $previousBalance,
                    'new_balance_mvr' => $newBalance,
                    'ledger_transaction_id' => $ledger->id,
                ]);
            });

            return $lockedPayment->fresh();
        }, 3);
    }

    /**
     * @return array<string, mixed>
     */
    private function buildInitializePayload(
        BalancePaymentTransaction $payment,
        User $user,
        string $customerOrderId,
        string $callbackToken,
        string $ipAddress,
    ): array {
        $defaults = (array) config('eps.customer_defaults', []);

        return [
            'merchantId' => (string) config('eps.credentials.merchant_id'),
            'storeId' => (string) config('eps.credentials.store_id'),
            'CustomerOrderId' => $customerOrderId,
            'merchantTransactionId' => $payment->transaction_id,
            'transactionTypeId' => 1,
            'financialEntityId' => 0,
            'transitionStatusId' => 0,
            'totalAmount' => (float) $payment->amount,
            'ipAddress' => $ipAddress,
            'version' => '1',
            'successUrl' => $this->callbackUrl('success', $payment->transaction_id, $callbackToken),
            'failUrl' => $this->callbackUrl('fail', $payment->transaction_id, $callbackToken),
            'cancelUrl' => $this->callbackUrl('cancel', $payment->transaction_id, $callbackToken),
            'customerName' => $user->name,
            'customerEmail' => (string) config('eps.receipt_sink_email'),
            'customerAddress' => (string) ($defaults['address'] ?? 'Dhaka, Bangladesh'),
            'customerAddress2' => '',
            'customerCity' => (string) ($defaults['city'] ?? 'Dhaka'),
            'customerState' => (string) ($defaults['state'] ?? 'Dhaka'),
            'customerPostcode' => (string) ($defaults['postcode'] ?? '1000'),
            'customerCountry' => (string) ($defaults['country'] ?? 'BD'),
            'customerPhone' => $user->phone,
            'shipmentName' => '',
            'shipmentAddress' => '',
            'shipmentAddress2' => '',
            'shipmentCity' => '',
            'shipmentState' => '',
            'shipmentPostcode' => '',
            'shipmentCountry' => '',
            'valueA' => (string) $user->id,
            'valueB' => $payment->transaction_id,
            'valueC' => 'balance_addition',
            'valueD' => '',
            'shippingMethod' => 'NO',
            'noOfItem' => '1',
            'productName' => 'EasyXpress Balance',
            'productProfile' => 'general',
            'productCategory' => 'Balance',
        ];
    }

    private function getToken(bool $forceRefresh = false): string
    {
        $username = (string) config('eps.credentials.username');
        $cacheKey = 'eps:access-token:'.hash('sha256', (string) config('eps.base_url').'|'.$username);

        if (! $forceRefresh) {
            $cachedToken = Cache::get($cacheKey);
            if (is_string($cachedToken) && $cachedToken !== '') {
                return $cachedToken;
            }
        }

        Cache::forget($cacheKey);

        try {
            $response = $this->retryingClient()
                ->withHeaders(['x-hash' => $this->generateHash($username)])
                ->post($this->endpoint('token'), [
                    'userName' => $username,
                    'password' => (string) config('eps.credentials.password'),
                ]);
        } catch (ConnectionException $exception) {
            throw new EpsPaymentException('EPS authentication is temporarily unavailable.', true, $exception);
        }

        $token = $response->successful()
            ? (string) ($this->valueFrom($this->responseJson($response), ['token']) ?? '')
            : '';

        if ($token === '') {
            $body = $this->responseJson($response);
            Log::warning('EPS authentication request failed.', [
                'http_status' => $response->status(),
                'error_code' => $this->valueFrom($body, ['ErrorCode']),
                'error_message' => $this->valueFrom($body, ['ErrorMessage', 'message']),
            ]);

            throw new EpsPaymentException('EPS authentication failed. Please contact support if the problem continues.');
        }

        Cache::put(
            $cacheKey,
            $token,
            max(60, (int) config('eps.token_cache_seconds', 3300)),
        );

        return $token;
    }

    private function baseClient(): PendingRequest
    {
        return Http::acceptJson()
            ->asJson()
            ->connectTimeout(max(1, (int) config('eps.connect_timeout', 5)))
            ->timeout(max(1, (int) config('eps.timeout', 15)));
    }

    private function retryingClient(): PendingRequest
    {
        return $this->baseClient()->retry(
            max(1, (int) config('eps.retry_times', 2)),
            max(0, (int) config('eps.retry_sleep_ms', 250)),
            static fn (Throwable $exception): bool => $exception instanceof ConnectionException
                || ($exception instanceof RequestException && $exception->response->serverError()),
            throw: false,
        );
    }

    private function endpoint(string $name): string
    {
        return rtrim((string) config('eps.base_url'), '/')
            .'/'.ltrim((string) config("eps.endpoints.{$name}"), '/');
    }

    private function callbackUrl(string $type, string $transactionId, string $callbackToken): string
    {
        $configured = trim((string) config("eps.callbacks.{$type}"));
        if ($configured === '') {
            $url = route("balance.payment.{$type}", [
                'merchantReference' => $transactionId,
                'callbackToken' => $callbackToken,
            ]);
        } else {
            if (
                ! str_contains($configured, '{transaction_id}')
                || ! str_contains($configured, '{callback_token}')
            ) {
                throw new EpsPaymentException(
                    "EPS {$type} URL must include {transaction_id} and {callback_token} placeholders.",
                );
            }

            $url = strtr($configured, [
                '{transaction_id}' => rawurlencode($transactionId),
                '{callback_token}' => rawurlencode($callbackToken),
            ]);
        }

        $this->assertProductionCallbackUrl($url, $type);

        return $url;
    }

    /** @param array<string, mixed> $payload */
    private function validateCallback(
        BalancePaymentTransaction $payment,
        string $callbackToken,
        array $payload,
    ): void {
        if ($callbackToken === '' || ! hash_equals($payment->callback_token_hash, hash('sha256', $callbackToken))) {
            throw new EpsPaymentException('The EPS callback token is invalid.');
        }

        $callbackTransactionId = (string) ($this->valueFrom($payload, [
            'merchant_reference',
            'MerchantTransactionId',
            'merchant_transaction_id',
        ]) ?? '');

        if ($callbackTransactionId === '' || ! hash_equals($payment->transaction_id, $callbackTransactionId)) {
            throw new EpsPaymentException('The EPS callback transaction reference is invalid.');
        }

        $this->validateGatewayReference($payment, $payload);
    }

    /** @param array<string, mixed> $payload */
    private function validateGatewayReference(BalancePaymentTransaction $payment, array $payload): void
    {
        $gatewayReference = (string) ($this->valueFrom($payload, [
            'EpsTransactionId',
            'EPSTransactionId',
            'EPSTransactionId_',
            'eps_transaction_id',
        ]) ?? '');

        if (
            $gatewayReference !== ''
            && $payment->gateway_transaction_id !== null
            && ! hash_equals($payment->gateway_transaction_id, $gatewayReference)
        ) {
            throw new EpsPaymentException('The EPS gateway transaction reference is invalid.');
        }
    }

    /** @param array<string, mixed> $payload */
    private function recordCallback(
        BalancePaymentTransaction $payment,
        string $callbackType,
        array $payload,
    ): BalancePaymentTransaction {
        $recorded = DB::transaction(function () use ($payment, $callbackType, $payload) {
            $locked = BalancePaymentTransaction::query()->lockForUpdate()->findOrFail($payment->id);
            $statusLog = $locked->status_log ?? [];
            $statusLog[] = $this->statusEntry(
                $locked->status,
                $callbackType.'_callback_received',
                $this->callbackAuditContext($payload),
            );

            $locked->forceFill([
                'callback_count' => $locked->callback_count + 1,
                'last_callback_at' => now(),
                'status_log' => $statusLog,
            ])->save();

            return $locked;
        }, 3);

        Log::info('EPS callback received.', array_merge([
            'payment_id' => $recorded->id,
            'user_id' => $recorded->user_id,
            'account_id' => $recorded->account_id,
            'transaction_id' => $recorded->transaction_id,
            'callback_type' => $callbackType,
            'callback_count' => $recorded->callback_count,
        ], $this->callbackAuditContext($payload)));

        return $recorded;
    }

    /** @param array<string, mixed> $payload */
    private function callbackAuditContext(array $payload): array
    {
        return array_filter([
            'merchant_transaction_id' => $this->valueFrom($payload, [
                'merchant_reference',
                'MerchantTransactionId',
                'merchant_transaction_id',
            ]),
            'gateway_transaction_id' => $this->valueFrom($payload, [
                'EpsTransactionId',
                'EPSTransactionId',
                'EPSTransactionId_',
                'eps_transaction_id',
            ]),
            'reported_status' => $this->valueFrom($payload, ['Status', 'status']),
            'reported_amount' => $this->valueFrom($payload, ['TotalAmount', 'amount']),
        ], static fn ($value) => $value !== null && $value !== '');
    }

    /**
     * @param  array<string, mixed>  $context
     * @param  array<string, mixed>|null  $response
     */
    private function transition(
        BalancePaymentTransaction $payment,
        PaymentStatus $status,
        string $event,
        array $context = [],
        ?string $responseKey = null,
        ?array $response = null,
        ?string $failedReason = null,
        bool $verified = false,
    ): BalancePaymentTransaction {
        return DB::transaction(function () use (
            $payment,
            $status,
            $event,
            $context,
            $responseKey,
            $response,
            $failedReason,
            $verified,
        ) {
            $locked = BalancePaymentTransaction::query()->lockForUpdate()->findOrFail($payment->id);
            $nextStatus = $locked->status === PaymentStatus::Successful
                ? PaymentStatus::Successful
                : $status;

            $statusLog = $locked->status_log ?? [];
            $statusLog[] = $this->statusEntry($nextStatus, $event, $context);

            $attributes = [
                'status' => $nextStatus,
                'status_log' => $statusLog,
                'failed_reason' => $failedReason,
            ];

            if ($responseKey !== null && $response !== null) {
                $responses = $locked->response_payload ?? [];
                $responses[$responseKey] = $this->sanitizePayload($response);
                $attributes['response_payload'] = $responses;
            }

            if ($verified) {
                $attributes['verified_at'] = now();
            }

            $locked->forceFill($attributes)->save();

            return $locked->fresh();
        }, 3);
    }

    /** @param array<string, mixed> $body */
    private function verificationMismatch(
        BalancePaymentTransaction $payment,
        array $body,
        string $reason,
    ): never {
        $this->transition(
            $payment,
            PaymentStatus::Failed,
            'verification_mismatch',
            ['reason' => $reason],
            'verification',
            $body,
            $reason,
            true,
        );

        throw new EpsPaymentException($reason.' No balance has been added.');
    }

    private function mapGatewayStatus(string $status): PaymentStatus
    {
        return match (Str::lower(trim($status))) {
            'success', 'successful', 'completed', 'complete', 'paid' => PaymentStatus::Successful,
            'fail', 'failed', 'failure', 'declined', 'rejected' => PaymentStatus::Failed,
            'cancel', 'cancelled', 'canceled' => PaymentStatus::Cancelled,
            'expired', 'expire' => PaymentStatus::Expired,
            default => PaymentStatus::Processing,
        };
    }

    private function assertProductionConfiguration(): void
    {
        $environment = Str::lower(trim((string) config('eps.environment')));
        $baseUrl = rtrim(trim((string) config('eps.base_url')), '/');
        $productionApiUrl = rtrim((string) config('eps.production_api_url'), '/');

        if (
            $environment !== 'production'
            || ! hash_equals(Str::lower($productionApiUrl), Str::lower($baseUrl))
        ) {
            throw new EpsPaymentException('EPS is locked to the production environment.');
        }

        foreach (['merchant_id', 'store_id', 'username', 'password', 'hash_key'] as $key) {
            if (trim((string) config("eps.credentials.{$key}")) === '') {
                throw new EpsPaymentException('EPS production credentials are incomplete. Please contact support.');
            }
        }

        if (filter_var(config('eps.receipt_sink_email'), FILTER_VALIDATE_EMAIL) === false) {
            throw new EpsPaymentException('EPS receipt suppression email is invalid. Please contact support.');
        }

        if (
            Str::upper(trim((string) config('eps.currency'))) !== 'BDT'
            || Str::upper(trim((string) config('eps.balance_currency'))) !== 'MVR'
        ) {
            throw new EpsPaymentException('EPS must accept BDT and credit balances in MVR.');
        }
    }

    private function assertDatabaseReady(): void
    {
        $requiredColumns = [
            'ledger_transaction_id',
            'gateway_transaction_id',
            'gateway_session_id',
            'balance_amount',
            'balance_currency',
            'exchange_rate',
            'callback_token_hash',
            'status_log',
        ];

        if (
            ! Schema::hasTable('balance_payment_transactions')
            || ! Schema::hasColumns('balance_payment_transactions', $requiredColumns)
            || ! Schema::hasTable('app_setups')
            || ! Schema::hasColumn('app_setups', 'mvr_to_bdt')
        ) {
            throw new EpsPaymentException(
                'EPS payment database update is pending. Please run the latest database migrations.',
            );
        }
    }

    private function assertProductionRedirect(string $url): void
    {
        $host = Str::lower((string) parse_url($url, PHP_URL_HOST));
        $scheme = Str::lower((string) parse_url($url, PHP_URL_SCHEME));
        $allowedHosts = array_map(
            static fn (string $allowed): string => Str::lower($allowed),
            (array) config('eps.production_checkout_hosts', []),
        );

        if ($scheme !== 'https' || ! in_array($host, $allowedHosts, true)) {
            throw new EpsPaymentException('EPS returned an invalid production checkout URL.');
        }
    }

    private function assertProductionCallbackUrl(string $url, string $type): void
    {
        $host = Str::lower((string) parse_url($url, PHP_URL_HOST));
        $scheme = Str::lower((string) parse_url($url, PHP_URL_SCHEME));
        $blockedHosts = ['localhost', '127.0.0.1', '::1'];
        $isPublicIp = filter_var($host, FILTER_VALIDATE_IP) === false
            || filter_var(
                $host,
                FILTER_VALIDATE_IP,
                FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE,
            ) !== false;

        if (
            $scheme !== 'https'
            || $host === ''
            || in_array($host, $blockedHosts, true)
            || Str::endsWith($host, ['.localhost', '.local'])
            || ! $isPublicIp
        ) {
            throw new EpsPaymentException(
                "EPS {$type} URL must use a public HTTPS domain in production.",
            );
        }
    }

    private function assertCredentialFingerprints(BalancePaymentTransaction $payment): void
    {
        if (
            ! hash_equals($payment->merchant_id_hash, $this->credentialFingerprint('merchant_id'))
            || ! hash_equals($payment->store_id_hash, $this->credentialFingerprint('store_id'))
        ) {
            throw new EpsPaymentException('The EPS merchant or store configuration does not match this transaction.');
        }
    }

    private function assertSupportedPayment(BalancePaymentTransaction $payment): void
    {
        $currency = Str::upper(trim($payment->currency));
        $balanceCurrency = Str::upper(trim($payment->balance_currency));
        $isBdtPayment = $currency === 'BDT' && $balanceCurrency === 'MVR';
        $isLegacyMvrPayment = $currency === 'MVR'
            && $balanceCurrency === 'MVR'
            && $this->minorUnits($payment->amount) === $this->minorUnits($payment->balance_amount);

        if (! $isBdtPayment && ! $isLegacyMvrPayment) {
            throw new EpsPaymentException('This EPS transaction has an unsupported currency conversion.');
        }
    }

    private function credentialFingerprint(string $key): string
    {
        return hash('sha256', (string) config("eps.credentials.{$key}"));
    }

    private function newTransactionId(): string
    {
        do {
            $transactionId = 'EPS'.now()->format('YmdHis').random_int(10000000, 99999999);
        } while (BalancePaymentTransaction::query()->where('transaction_id', $transactionId)->exists());

        return $transactionId;
    }

    /** @param array<string, mixed> $context */
    private function statusEntry(PaymentStatus $status, string $event, array $context = []): array
    {
        return array_filter([
            'status' => $status->value,
            'event' => $event,
            'at' => now()->toIso8601String(),
            'context' => $context === [] ? null : $this->sanitizePayload($context),
        ], static fn ($value) => $value !== null);
    }

    private function minorUnits(mixed $amount): int
    {
        return (int) round(((float) $amount) * 100);
    }

    /** @return array<string, mixed> */
    private function responseJson(Response $response): array
    {
        $json = $response->json();

        return is_array($json) ? $json : [];
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  list<string>  $keys
     */
    private function valueFrom(array $payload, array $keys): mixed
    {
        $sources = [$payload];
        if (isset($payload['data']) && is_array($payload['data'])) {
            $sources[] = $payload['data'];
        }

        $wanted = array_map([$this, 'normalizeKey'], $keys);
        foreach ($sources as $source) {
            foreach ($source as $key => $value) {
                if (in_array($this->normalizeKey((string) $key), $wanted, true)) {
                    return $value;
                }
            }
        }

        return null;
    }

    private function normalizeKey(string $key): string
    {
        return preg_replace('/[^a-z0-9]/', '', Str::lower($key)) ?? '';
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  list<string>  $secretValues
     * @return array<string, mixed>
     */
    private function sanitizePayload(array $payload, array $secretValues = []): array
    {
        $sensitiveKeys = [
            'password',
            'token',
            'authorization',
            'xhash',
            'hashkey',
            'merchantid',
            'storeid',
            'callbacktoken',
            'ipnencryptionkey',
        ];

        $sanitized = [];
        foreach ($payload as $key => $value) {
            $normalizedKey = $this->normalizeKey((string) $key);
            if (in_array($normalizedKey, $sensitiveKeys, true)) {
                $sanitized[$key] = '[REDACTED]';

                continue;
            }

            if (is_array($value)) {
                $sanitized[$key] = $this->sanitizePayload($value, $secretValues);

                continue;
            }

            if (is_string($value)) {
                foreach ($secretValues as $secret) {
                    if ($secret !== '') {
                        $value = str_replace($secret, '[REDACTED]', $value);
                    }
                }
            }

            $sanitized[$key] = $value;
        }

        return $sanitized;
    }

    /** @param array<string, mixed> $requestPayload */
    private function resolveIpnPayload(array $requestPayload): array
    {
        $encryptedData = $this->valueFrom($requestPayload, ['Data']);
        if (! is_string($encryptedData) || trim($encryptedData) === '') {
            return $requestPayload;
        }

        $configuredKey = trim((string) config('eps.ipn_encryption_key'));
        if ($configuredKey === '') {
            throw new EpsPaymentException('EPS IPN encryption is not configured.');
        }

        $parts = explode(':', $encryptedData, 2);
        if (count($parts) !== 2) {
            throw new EpsPaymentException('The encrypted EPS IPN payload is invalid.');
        }

        $iv = base64_decode($parts[0], true);
        $ciphertext = base64_decode($parts[1], true);
        $key = $this->decodeIpnKey($configuredKey);

        if ($iv === false || strlen($iv) !== 16 || $ciphertext === false) {
            throw new EpsPaymentException('The encrypted EPS IPN payload is invalid.');
        }

        $json = openssl_decrypt($ciphertext, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
        $payload = is_string($json) ? json_decode($json, true) : null;

        if (! is_array($payload)) {
            throw new EpsPaymentException('The encrypted EPS IPN payload could not be verified.');
        }

        return $payload;
    }

    private function decodeIpnKey(string $configuredKey): string
    {
        $decoded = base64_decode($configuredKey, true);
        if ($decoded !== false && strlen($decoded) === 32) {
            return $decoded;
        }

        if (strlen($configuredKey) === 32) {
            return $configuredKey;
        }

        throw new EpsPaymentException('The EPS IPN encryption key format is invalid.');
    }
}
