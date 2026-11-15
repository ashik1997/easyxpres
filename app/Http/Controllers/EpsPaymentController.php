<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatus;
use App\Exceptions\EpsPaymentException;
use App\Http\Requests\AddBalanceRequest;
use App\Models\BalancePaymentTransaction;
use App\Services\Payments\EpsPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class EpsPaymentController extends Controller
{
    public function __construct(private readonly EpsPaymentService $eps) {}

    public function index(Request $request): View
    {
        $payments = BalancePaymentTransaction::query()
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(15);

        return view('frontend.balance.eps', [
            'account' => $request->user()->account,
            'payments' => $payments,
            'minimumAmount' => (float) config('eps.minimum_amount'),
            'maximumAmount' => (float) config('eps.maximum_amount'),
            'exchangeRate' => (float) (DB::table('app_setups')->where('id', 1)->value('mvr_to_bdt') ?? 0),
        ]);
    }

    public function adminIndex(): View
    {
        return view('backend.balance.eps', [
            'payments' => BalancePaymentTransaction::query()
                ->with(['user', 'account'])
                ->latest()
                ->get(),
        ]);
    }

    public function create(AddBalanceRequest $request): RedirectResponse
    {
        try {
            $result = $this->eps->createPayment(
                $request->user(),
                (float) $request->validated('amount'),
                (string) $request->ip(),
            );

            return redirect()->away($result['redirect_url']);
        } catch (EpsPaymentException $exception) {
            Log::warning('EPS payment initialization did not complete.', [
                'user_id' => $request->user()->id,
                'retryable' => $exception->retryable,
                'reason' => $exception->getMessage(),
            ]);

            return back()->withInput()->with('error', $exception->getMessage());
        } catch (Throwable $exception) {
            $incidentId = 'EPS-'.Str::upper(Str::random(10));
            Log::error('Unexpected EPS payment initialization error.', [
                'incident_id' => $incidentId,
                'user_id' => $request->user()->id,
                'exception' => $exception::class,
                'reason' => $exception->getMessage(),
                'source' => basename($exception->getFile()).':'.$exception->getLine(),
            ]);

            return back()->withInput()->with(
                'error',
                "EPS could not start the payment. No balance has been added. Support reference: {$incidentId}.",
            );
        }
    }

    public function success(Request $request, string $merchantReference, string $callbackToken): View
    {
        return $this->handleBrowserCallback($request, $merchantReference, $callbackToken, 'success');
    }

    public function fail(Request $request, string $merchantReference, string $callbackToken): View
    {
        return $this->handleBrowserCallback($request, $merchantReference, $callbackToken, 'failure');
    }

    public function cancel(Request $request, string $merchantReference, string $callbackToken): View
    {
        return $this->handleBrowserCallback($request, $merchantReference, $callbackToken, 'cancellation');
    }

    public function ipn(Request $request): JsonResponse
    {
        try {
            $payment = $this->eps->handleIpn($request->all());

            return response()->json([
                'accepted' => true,
                'transaction_id' => $payment->transaction_id,
                'status' => $payment->status->value,
            ]);
        } catch (EpsPaymentException $exception) {
            Log::warning('EPS IPN was not processed.', [
                'retryable' => $exception->retryable,
                'reason' => $exception->getMessage(),
            ]);

            return response()->json([
                'accepted' => false,
                'message' => $exception->retryable
                    ? 'Verification is temporarily unavailable.'
                    : 'Invalid EPS notification.',
            ], $exception->retryable ? 503 : 422);
        } catch (Throwable $exception) {
            Log::error('Unexpected EPS IPN processing error.', [
                'exception' => $exception::class,
                'reason' => $exception->getMessage(),
            ]);

            return response()->json([
                'accepted' => false,
                'message' => 'Verification is temporarily unavailable.',
            ], 503);
        }
    }

    public function status(
        Request $request,
        BalancePaymentTransaction $payment,
    ): RedirectResponse {
        abort_unless($payment->user_id === $request->user()->id, 404);

        try {
            $payment = $this->eps->checkStatus($payment);

            return back()->with(
                $payment->status === PaymentStatus::Successful ? 'success' : 'info',
                $this->statusMessage($payment),
            );
        } catch (EpsPaymentException $exception) {
            Log::warning('EPS status check did not complete.', [
                'transaction_id' => $payment->transaction_id,
                'retryable' => $exception->retryable,
                'reason' => $exception->getMessage(),
            ]);

            return back()->with('error', $exception->getMessage());
        } catch (Throwable $exception) {
            Log::error('Unexpected EPS status check error.', [
                'transaction_id' => $payment->transaction_id,
                'exception' => $exception::class,
                'reason' => $exception->getMessage(),
            ]);

            return back()->with('error', 'EPS status could not be checked right now. Please try again later.');
        }
    }

    private function handleBrowserCallback(
        Request $request,
        string $merchantReference,
        string $callbackToken,
        string $type,
    ): View {
        $payment = null;

        try {
            $payload = array_merge($request->query(), [
                'merchant_reference' => $merchantReference,
            ]);
            $payment = $this->eps->findPaymentFromPayload($payload);

            $payment = match ($type) {
                'success' => $this->eps->handleSuccess($payment, $callbackToken, $payload),
                'failure' => $this->eps->handleFailure($payment, $callbackToken, $payload),
                'cancellation' => $this->eps->handleCancellation($payment, $callbackToken, $payload),
            };

            return view('frontend.balance.eps_result', [
                'payment' => $payment,
                'message' => $this->statusMessage($payment),
                'resultType' => $payment->status->value,
            ]);
        } catch (EpsPaymentException $exception) {
            Log::warning('EPS browser callback did not complete.', [
                'transaction_id' => $payment?->transaction_id,
                'callback_type' => $type,
                'retryable' => $exception->retryable,
                'reason' => $exception->getMessage(),
            ]);

            return view('frontend.balance.eps_result', [
                'payment' => $payment?->fresh(),
                'message' => $exception->getMessage(),
                'resultType' => $exception->retryable ? 'processing' : 'failed',
            ]);
        } catch (Throwable $exception) {
            Log::error('Unexpected EPS browser callback error.', [
                'transaction_id' => $payment?->transaction_id,
                'callback_type' => $type,
                'exception' => $exception::class,
                'reason' => $exception->getMessage(),
            ]);

            return view('frontend.balance.eps_result', [
                'payment' => $payment?->fresh(),
                'message' => 'The payment result could not be verified right now. No balance has been added.',
                'resultType' => 'processing',
            ]);
        }
    }

    private function statusMessage(BalancePaymentTransaction $payment): string
    {
        return match ($payment->status) {
            PaymentStatus::Successful => sprintf(
                'Payment verified. MVR %s has been added to your balance.',
                number_format((float) $payment->balance_amount, 2),
            ),
            PaymentStatus::Failed => 'EPS verified that this payment failed. No balance was added.',
            PaymentStatus::Cancelled => 'The EPS payment was cancelled. No balance was added.',
            PaymentStatus::Expired => 'The EPS payment expired. No balance was added.',
            default => 'The payment is still processing. Please use Check Status again shortly.',
        };
    }
}
