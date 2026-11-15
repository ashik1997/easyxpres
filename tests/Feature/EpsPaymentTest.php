<?php

namespace Tests\Feature;

use App\Enums\PaymentStatus;
use App\Models\Account;
use App\Models\BalancePaymentTransaction;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Payments\EpsPaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EpsPaymentTest extends TestCase
{
    use RefreshDatabase;

    /** @var array<string, string> */
    private array $callbackUrls = [];

    private string $verificationStatus = 'Success';

    private ?float $verificationAmount = null;

    private bool $verificationUnavailable = false;

    private bool $authenticationFailsAfterFirstRequest = false;

    private int $authenticationRequestCount = 0;

    private string $redirectUrl = 'https://pg.eps.com.bd/PG?data=gateway-transaction-1';

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();

        config([
            'eps.environment' => 'production',
            'eps.base_url' => 'https://pgapi.eps.com.bd',
            'eps.credentials.merchant_id' => 'merchant-id-for-tests',
            'eps.credentials.store_id' => 'store-id-for-tests',
            'eps.credentials.username' => 'merchant@example.test',
            'eps.credentials.password' => 'test-password',
            'eps.credentials.hash_key' => 'test-hash-key',
            'eps.currency' => 'BDT',
            'eps.balance_currency' => 'MVR',
            'eps.minimum_amount' => 10,
            'eps.maximum_amount' => 500000,
            'eps.retry_times' => 1,
            'eps.receipt_sink_email' => 'no-reply@easyxpres.test',
            'eps.callbacks.success' => 'https://payments.example.com/balance/eps/callback/{transaction_id}/{callback_token}/success',
            'eps.callbacks.fail' => 'https://payments.example.com/balance/eps/callback/{transaction_id}/{callback_token}/fail',
            'eps.callbacks.cancel' => 'https://payments.example.com/balance/eps/callback/{transaction_id}/{callback_token}/cancel',
        ]);

        DB::table('app_setups')->insert([
            'add_balance_content' => '',
            'mvr_to_bdt' => 8,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_approved_user_can_view_the_bdt_eps_payment_page(): void
    {
        [$user] = $this->approvedUserWithAccount();
        $this->actingAs($user)
            ->get(route('balance.payment.index'))
            ->assertOk()
            ->assertSee('Add Balance with EPS')
            ->assertSee('EPS payable amount (BDT)')
            ->assertSee('id="eps-payment-page"', false)
            ->assertSee('id="eps-pay-button"', false)
            ->assertSee('Pay securely with EPS');

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('eps-dashboard-pay-button')
            ->assertSee('Pay with Digital Banking');
    }

    public function test_payment_initialization_creates_a_separate_sanitized_record(): void
    {
        [$user, $account] = $this->approvedUserWithAccount();
        $this->fakeSuccessfulGateway();

        $response = $this->actingAs($user)->post(route('balance.payment.create'), [
            'amount' => 800,
        ]);

        $payment = BalancePaymentTransaction::firstOrFail();

        $response->assertRedirect('https://pg.eps.com.bd/PG?data=gateway-transaction-1');
        $this->assertSame(PaymentStatus::Processing, $payment->status);
        $this->assertSame('800.00', $payment->amount);
        $this->assertSame('BDT', $payment->currency);
        $this->assertSame('0.00', $payment->balance_amount);
        $this->assertNull($payment->exchange_rate);
        $this->assertSame('gateway-transaction-1', $payment->gateway_session_id);
        $this->assertNull($payment->gateway_transaction_id);
        $this->assertSame('[REDACTED]', $payment->request_payload['merchantId']);
        $this->assertSame('[REDACTED]', $payment->request_payload['storeId']);
        $this->assertStringContainsString('/[REDACTED]/success', $payment->request_payload['successUrl']);
        $this->assertEquals(0, $account->fresh()->balance);
        $this->assertDatabaseCount('transactions', 0);

        $service = app(EpsPaymentService::class);
        Http::assertSent(fn (Request $request): bool => str_ends_with($request->url(), '/v1/Auth/GetToken')
            && $request->hasHeader('x-hash', $service->generateHash('merchant@example.test')));
        Http::assertSent(fn (Request $request): bool => str_ends_with($request->url(), '/v1/EPSEngine/InitializeEPS')
            && $request->hasHeader('x-hash', $service->generateHash($payment->transaction_id)));
    }

    public function test_production_doctor_validates_readiness_without_initializing_a_payment(): void
    {
        $this->fakeSuccessfulGateway();

        $this->assertSame(0, Artisan::call('eps:doctor'));
        $this->assertStringContainsString('production preflight passed', Artisan::output());
        $this->assertDatabaseCount('balance_payment_transactions', 0);
        Http::assertSentCount(1);
        Http::assertSent(fn (Request $request): bool => str_ends_with($request->url(), '/v1/Auth/GetToken'));
    }

    public function test_successful_eps_payment_does_not_send_a_confirmation_email_to_the_customer(): void
    {
        [$user, $account] = $this->approvedUserWithAccount();
        $customerEmail = $user->email;
        Mail::fake();
        $this->fakeSuccessfulGateway();

        $this->actingAs($user)->post(route('balance.payment.create'), ['amount' => 800]);

        Http::assertSent(function (Request $request) use ($customerEmail): bool {
            if (! str_ends_with($request->url(), '/v1/EPSEngine/InitializeEPS')) {
                return false;
            }

            return $request->data()['customerEmail'] === 'no-reply@easyxpres.test'
                && $request->data()['customerEmail'] !== $customerEmail;
        });

        $this->actingAs($user)
            ->get($this->callbackUrl('success').'?EPSTransactionId=eps-payment-1')
            ->assertOk()
            ->assertSee('Payment successful');

        Mail::assertNothingSent();
        $this->assertEquals(100, $account->fresh()->balance);
        $this->assertDatabaseCount('transactions', 1);
    }

    public function test_verified_success_credits_balance_only_once_for_duplicate_callbacks(): void
    {
        [$user, $account] = $this->approvedUserWithAccount();
        $this->fakeSuccessfulGateway();
        $this->actingAs($user)->post(route('balance.payment.create'), ['amount' => 800]);

        // V5 returns a checkout-session TransactionId at initialization and a
        // different EpsTransactionId on callback/verification.
        $callbackUrl = $this->callbackUrl('success').'?EPSTransactionId=eps-payment-1';
        $this->actingAs($user)->get($callbackUrl)->assertOk()->assertSee('Payment successful');
        $this->actingAs($user)->get($callbackUrl)->assertOk()->assertSee('Payment successful');

        $payment = BalancePaymentTransaction::firstOrFail()->fresh();
        $this->assertSame(PaymentStatus::Successful, $payment->status);
        $this->assertSame('gateway-transaction-1', $payment->gateway_session_id);
        $this->assertSame('eps-payment-1', $payment->gateway_transaction_id);
        $this->assertSame(2, $payment->callback_count);
        $this->assertNotNull($payment->verified_at);
        $this->assertEquals(100, $account->fresh()->balance);
        $this->assertSame('100.00', $payment->balance_amount);
        $this->assertSame('8.000000', $payment->exchange_rate);
        $this->assertDatabaseCount('transactions', 1);
        $this->assertDatabaseHas('transactions', [
            'transaction_id' => $payment->transaction_id,
            'amount' => 100,
            'status' => 'approved',
        ]);
        $this->assertSame(1, $this->authenticationRequestCount);
    }

    public function test_success_credits_only_the_payment_owner(): void
    {
        [$owner, $ownerAccount] = $this->approvedUserWithAccount('OWNER-ACCOUNT');
        [, $otherAccount] = $this->approvedUserWithAccount('OTHER-ACCOUNT');
        $ownerAccount->update(['balance' => 10]);
        $otherAccount->update(['balance' => 25]);
        $this->fakeSuccessfulGateway();

        $this->actingAs($owner)->post(route('balance.payment.create'), ['amount' => 800]);
        $this->actingAs($owner)->get($this->callbackUrl('success'))->assertOk();

        $this->assertEquals(110, $ownerAccount->fresh()->balance);
        $this->assertEquals(25, $otherAccount->fresh()->balance);
        $this->assertDatabaseHas('transactions', [
            'account_id' => $ownerAccount->id,
            'amount' => 100,
        ]);
    }

    public function test_cached_token_allows_success_callback_when_new_authentication_is_unavailable(): void
    {
        [$user, $account] = $this->approvedUserWithAccount();
        $this->authenticationFailsAfterFirstRequest = true;
        $this->fakeSuccessfulGateway();

        $this->actingAs($user)->post(route('balance.payment.create'), ['amount' => 800]);
        $this->actingAs($user)->get($this->callbackUrl('success'))->assertOk();

        $this->assertEquals(100, $account->fresh()->balance);
        $this->assertSame(1, $this->authenticationRequestCount);
    }

    public function test_credit_failure_rolls_back_balance_status_and_history(): void
    {
        [$user, $account] = $this->approvedUserWithAccount();
        $this->fakeSuccessfulGateway();
        $this->actingAs($user)->post(route('balance.payment.create'), ['amount' => 800]);

        Transaction::creating(function (): void {
            throw new \RuntimeException('Simulated ledger failure.');
        });

        try {
            $this->actingAs($user)->get($this->callbackUrl('success'))->assertOk();
        } finally {
            Transaction::flushEventListeners();
            Transaction::setEventDispatcher(app('events'));
        }

        $payment = BalancePaymentTransaction::firstOrFail()->fresh();
        $this->assertSame(PaymentStatus::Processing, $payment->status);
        $this->assertNull($payment->ledger_transaction_id);
        $this->assertEquals(0, $account->fresh()->balance);
        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_success_uses_the_current_exchange_rate(): void
    {
        [$user, $account] = $this->approvedUserWithAccount();
        $this->verificationStatus = 'Completed';
        $this->fakeSuccessfulGateway();
        $this->actingAs($user)->post(route('balance.payment.create'), ['amount' => 800]);

        DB::table('app_setups')->where('id', 1)->update(['mvr_to_bdt' => 10]);

        $this->actingAs($user)->get($this->callbackUrl('success'))->assertOk();

        $payment = BalancePaymentTransaction::firstOrFail();
        $this->assertEquals(80, $account->fresh()->balance);
        $this->assertSame('80.00', $payment->balance_amount);
        $this->assertSame('10.000000', $payment->exchange_rate);
    }

    public function test_legacy_mvr_payment_can_still_be_verified_and_credited(): void
    {
        [$user, $account] = $this->approvedUserWithAccount();
        $this->fakeSuccessfulGateway();
        $this->actingAs($user)->post(route('balance.payment.create'), ['amount' => 800]);

        BalancePaymentTransaction::query()->firstOrFail()->update([
            'currency' => 'MVR',
            'balance_amount' => 800,
        ]);

        $this->actingAs($user)->get($this->callbackUrl('success'))->assertOk();

        $payment = BalancePaymentTransaction::firstOrFail()->fresh();
        $this->assertSame(PaymentStatus::Successful, $payment->status);
        $this->assertEquals(800, $account->fresh()->balance);
        $this->assertSame('800.00', $payment->balance_amount);
        $this->assertNull($payment->exchange_rate);
    }

    public function test_failed_payment_does_not_credit_balance(): void
    {
        [$user, $account] = $this->approvedUserWithAccount();
        $this->verificationStatus = 'Fail';
        $this->fakeSuccessfulGateway();
        $this->actingAs($user)->post(route('balance.payment.create'), ['amount' => 800]);

        $this->actingAs($user)->get($this->callbackUrl('fail'))->assertOk()->assertSee('Payment not completed');

        $this->assertSame(PaymentStatus::Failed, BalancePaymentTransaction::firstOrFail()->status);
        $this->assertEquals(0, $account->fresh()->balance);
        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_cancelled_payment_does_not_credit_balance(): void
    {
        [$user, $account] = $this->approvedUserWithAccount();
        $this->verificationStatus = 'Cancelled';
        $this->fakeSuccessfulGateway();
        $this->actingAs($user)->post(route('balance.payment.create'), ['amount' => 800]);

        $this->actingAs($user)->get($this->callbackUrl('cancel'))->assertOk()->assertSee('Payment not completed');

        $this->assertSame(PaymentStatus::Cancelled, BalancePaymentTransaction::firstOrFail()->status);
        $this->assertEquals(0, $account->fresh()->balance);
        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_amount_mismatch_is_rejected_without_crediting_balance(): void
    {
        [$user, $account] = $this->approvedUserWithAccount();
        $this->verificationAmount = 799;
        $this->fakeSuccessfulGateway();
        $this->actingAs($user)->post(route('balance.payment.create'), ['amount' => 800]);

        $this->actingAs($user)
            ->get($this->callbackUrl('success'))
            ->assertOk()
            ->assertSee('paid amount does not match');

        $payment = BalancePaymentTransaction::firstOrFail();
        $this->assertSame(PaymentStatus::Failed, $payment->status);
        $this->assertEquals(0, $account->fresh()->balance);
        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_verification_api_failure_leaves_payment_processing_without_credit(): void
    {
        [$user, $account] = $this->approvedUserWithAccount();
        $this->verificationUnavailable = true;
        $this->fakeSuccessfulGateway();
        $this->actingAs($user)->post(route('balance.payment.create'), ['amount' => 800]);

        $this->actingAs($user)
            ->get($this->callbackUrl('success'))
            ->assertOk()
            ->assertSee('Verification pending');

        $this->assertSame(PaymentStatus::Processing, BalancePaymentTransaction::firstOrFail()->status);
        $this->assertEquals(0, $account->fresh()->balance);
        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_plain_ipn_is_always_server_verified_and_is_idempotent(): void
    {
        [$user, $account] = $this->approvedUserWithAccount();
        $this->fakeSuccessfulGateway();
        $this->actingAs($user)->post(route('balance.payment.create'), ['amount' => 800]);
        $payment = BalancePaymentTransaction::firstOrFail();

        $payload = [
            'merchant_transaction_id' => $payment->transaction_id,
            'EPSTransactionId' => 'eps-payment-1',
            'amount' => 800,
            'status' => 'SUCCESS',
        ];

        $this->postJson(route('balance.payment.ipn'), $payload)->assertOk()->assertJson(['accepted' => true]);
        $this->postJson(route('balance.payment.ipn'), $payload)->assertOk()->assertJson(['accepted' => true]);

        $this->assertEquals(100, $account->fresh()->balance);
        $this->assertDatabaseCount('transactions', 1);
    }

    public function test_scheduled_reconciliation_recovers_a_paid_processing_transaction(): void
    {
        [$user, $account] = $this->approvedUserWithAccount();
        $this->fakeSuccessfulGateway();
        $this->actingAs($user)->post(route('balance.payment.create'), ['amount' => 800]);
        $payment = BalancePaymentTransaction::firstOrFail();

        DB::table('balance_payment_transactions')
            ->where('id', $payment->id)
            ->update(['updated_at' => now()->subMinutes(5)]);

        $this->assertSame(0, Artisan::call('eps:reconcile', ['--limit' => 10]));

        $payment->refresh();
        $this->assertSame(PaymentStatus::Successful, $payment->status);
        $this->assertSame('eps-payment-1', $payment->gateway_transaction_id);
        $this->assertEquals(100, $account->fresh()->balance);
        $this->assertDatabaseCount('transactions', 1);
    }

    public function test_invalid_callback_token_is_rejected_without_verification_or_credit(): void
    {
        [$user, $account] = $this->approvedUserWithAccount();
        $this->fakeSuccessfulGateway();
        $this->actingAs($user)->post(route('balance.payment.create'), ['amount' => 800]);
        $payment = BalancePaymentTransaction::firstOrFail();

        $this->actingAs($user)
            ->get(route('balance.payment.success', [
                'merchantReference' => $payment->transaction_id,
                'callbackToken' => 'invalid-callback-token',
            ]))
            ->assertOk()
            ->assertSee('callback token is invalid');

        $payment->refresh();
        $this->assertSame(PaymentStatus::Processing, $payment->status);
        $this->assertSame(0, $payment->callback_count);
        $this->assertEquals(0, $account->fresh()->balance);
        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_another_user_cannot_check_a_payment_status(): void
    {
        [$owner] = $this->approvedUserWithAccount('OWNER-1');
        [$otherUser] = $this->approvedUserWithAccount('OTHER-1');
        $this->fakeSuccessfulGateway();
        $this->actingAs($owner)->post(route('balance.payment.create'), ['amount' => 800]);
        $payment = BalancePaymentTransaction::firstOrFail();

        $this->actingAs($otherUser)
            ->post(route('balance.payment.status', $payment))
            ->assertNotFound();
    }

    public function test_invalid_add_balance_amount_is_rejected_before_calling_eps(): void
    {
        [$user] = $this->approvedUserWithAccount();
        Http::fake();

        $this->actingAs($user)
            ->from(route('balance.payment.index'))
            ->post(route('balance.payment.create'), ['amount' => -1])
            ->assertRedirect(route('balance.payment.index'))
            ->assertSessionHasErrors('amount');

        $this->assertDatabaseCount('balance_payment_transactions', 0);
        Http::assertNothingSent();
    }

    public function test_payment_cannot_start_without_an_exchange_rate(): void
    {
        [$user] = $this->approvedUserWithAccount();
        DB::table('app_setups')->where('id', 1)->update(['mvr_to_bdt' => null]);
        Http::fake();

        $this->actingAs($user)
            ->post(route('balance.payment.create'), ['amount' => 800])
            ->assertSessionHas('error', 'The BDT to MVR exchange rate is unavailable. Please contact support.');

        $this->assertDatabaseCount('balance_payment_transactions', 0);
        Http::assertNothingSent();
    }

    public function test_sandbox_configuration_is_rejected_before_creating_a_payment(): void
    {
        [$user] = $this->approvedUserWithAccount();
        config([
            'eps.environment' => 'sandbox',
            'eps.base_url' => 'https://sandboxpgapi.eps.com.bd',
        ]);
        Http::fake();

        $this->actingAs($user)
            ->from(route('balance.payment.index'))
            ->post(route('balance.payment.create'), ['amount' => 800])
            ->assertRedirect(route('balance.payment.index'))
            ->assertSessionHas('error', 'EPS is locked to the production environment.');

        $this->assertDatabaseCount('balance_payment_transactions', 0);
        Http::assertNothingSent();
    }

    public function test_invalid_currency_configuration_is_rejected_before_creating_a_payment(): void
    {
        [$user] = $this->approvedUserWithAccount();
        config([
            'eps.currency' => 'MVR',
            'eps.balance_currency' => 'MVR',
        ]);
        Http::fake();

        $this->actingAs($user)
            ->from(route('balance.payment.index'))
            ->post(route('balance.payment.create'), ['amount' => 800])
            ->assertRedirect(route('balance.payment.index'))
            ->assertSessionHas('error', 'EPS must accept BDT and credit balances in MVR.');

        $this->assertDatabaseCount('balance_payment_transactions', 0);
        Http::assertNothingSent();
    }

    public function test_non_public_callback_urls_are_rejected_before_contacting_eps(): void
    {
        [$user, $account] = $this->approvedUserWithAccount();
        config([
            'app.url' => 'http://localhost',
            'eps.callbacks.success' => null,
            'eps.callbacks.fail' => null,
            'eps.callbacks.cancel' => null,
        ]);
        Http::fake();

        $this->actingAs($user)
            ->from(route('balance.payment.index'))
            ->post(route('balance.payment.create'), ['amount' => 800])
            ->assertRedirect(route('balance.payment.index'))
            ->assertSessionHas('error');

        $this->assertSame(PaymentStatus::Failed, BalancePaymentTransaction::firstOrFail()->status);
        $this->assertEquals(0, $account->fresh()->balance);
        $this->assertDatabaseCount('transactions', 0);
        Http::assertNothingSent();
    }

    public function test_sandbox_checkout_redirect_is_rejected_without_crediting_balance(): void
    {
        [$user, $account] = $this->approvedUserWithAccount();
        $this->redirectUrl = 'https://sandboxpg.eps.com.bd/PG?data=gateway-transaction-1';
        $this->fakeSuccessfulGateway();

        $this->actingAs($user)
            ->from(route('balance.payment.index'))
            ->post(route('balance.payment.create'), ['amount' => 800])
            ->assertRedirect(route('balance.payment.index'))
            ->assertSessionHas('error', 'EPS returned an invalid production checkout URL.');

        $this->assertSame(PaymentStatus::Failed, BalancePaymentTransaction::firstOrFail()->status);
        $this->assertEquals(0, $account->fresh()->balance);
        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_initialization_timeout_does_not_credit_balance_or_blindly_retry(): void
    {
        [$user, $account] = $this->approvedUserWithAccount();

        Http::fake(function (Request $request) {
            if (str_ends_with($request->url(), '/v1/Auth/GetToken')) {
                return Http::response(['token' => 'production-test-token'], 200);
            }

            return Http::failedConnection('timeout');
        });

        $this->actingAs($user)
            ->from(route('balance.payment.index'))
            ->post(route('balance.payment.create'), ['amount' => 800])
            ->assertRedirect(route('balance.payment.index'))
            ->assertSessionHas('error');

        $this->assertSame(PaymentStatus::Processing, BalancePaymentTransaction::firstOrFail()->status);
        $this->assertEquals(0, $account->fresh()->balance);
        $this->assertDatabaseCount('transactions', 0);

        $initializationRequests = collect(Http::recorded())
            ->filter(fn (array $pair): bool => str_ends_with($pair[0]->url(), '/v1/EPSEngine/InitializeEPS'));
        $this->assertCount(1, $initializationRequests);
    }

    public function test_invalid_authentication_hash_response_fails_safely(): void
    {
        [$user, $account] = $this->approvedUserWithAccount();

        Http::fake([
            'https://pgapi.eps.com.bd/v1/Auth/GetToken' => Http::response([
                'errorMessage' => 'Invalid hash',
            ], 401),
        ]);

        $this->actingAs($user)
            ->from(route('balance.payment.index'))
            ->post(route('balance.payment.create'), ['amount' => 800])
            ->assertRedirect(route('balance.payment.index'))
            ->assertSessionHas('error');

        $this->assertSame(PaymentStatus::Failed, BalancePaymentTransaction::firstOrFail()->status);
        $this->assertEquals(0, $account->fresh()->balance);
        $this->assertDatabaseCount('transactions', 0);
    }

    private function fakeSuccessfulGateway(): void
    {
        Http::fake(function (Request $request) {
            if (str_ends_with($request->url(), '/v1/Auth/GetToken')) {
                $this->authenticationRequestCount++;

                if ($this->authenticationFailsAfterFirstRequest && $this->authenticationRequestCount > 1) {
                    return Http::response(['errorMessage' => 'Temporarily unavailable'], 503);
                }

                return Http::response([
                    'token' => 'production-test-token',
                    'expireDate' => now()->addHour()->toIso8601String(),
                ], 200);
            }

            if (str_ends_with($request->url(), '/v1/EPSEngine/InitializeEPS')) {
                $data = $request->data();
                foreach (['success', 'fail', 'cancel'] as $type) {
                    $urlKey = $type === 'fail' ? 'failUrl' : $type.'Url';
                    $this->callbackUrls[$type] = $data[$urlKey];
                }

                return Http::response([
                    'TransactionId' => 'gateway-transaction-1',
                    'RedirectURL' => $this->redirectUrl,
                    'ErrorMessage' => '',
                    'ErrorCode' => null,
                ], 200);
            }

            if (str_contains($request->url(), '/v1/EPSEngine/CheckMerchantTransactionStatus')) {
                if ($this->verificationUnavailable) {
                    return Http::response(['ErrorMessage' => 'Unavailable'], 503);
                }

                parse_str((string) parse_url($request->url(), PHP_URL_QUERY), $query);
                $payment = BalancePaymentTransaction::where('transaction_id', $query['merchantTransactionId'])->firstOrFail();

                return Http::response([
                    'MerchantTransactionId' => $payment->transaction_id,
                    'EpsTransactionId' => 'eps-payment-1',
                    'Status' => $this->verificationStatus,
                    'TotalAmount' => number_format($this->verificationAmount ?? (float) $payment->amount, 2, '.', ''),
                    'TransactionDate' => now()->format('d M Y h:i:s A'),
                ], 200);
            }

            return Http::response([], 404);
        });
    }

    private function callbackUrl(string $type): string
    {
        return $this->callbackUrls[$type];
    }

    /** @return array{User, Account} */
    private function approvedUserWithAccount(?string $accountNumber = null): array
    {
        $user = User::factory()->create();
        $user->forceFill([
            'status' => 'approved',
            'phone' => '960'.fake()->unique()->numerify('#######'),
        ])->save();

        $account = Account::create([
            'user_id' => $user->id,
            'account_number' => $accountNumber ?? 'EX-'.fake()->unique()->numerify('######'),
            'balance' => 0,
        ]);

        return [$user, $account];
    }
}
