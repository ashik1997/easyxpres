<?php

use App\Enums\PaymentStatus;
use App\Exceptions\EpsPaymentException;
use App\Models\BalancePaymentTransaction;
use App\Services\Payments\EpsPaymentService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Schema;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('eps:doctor', function (EpsPaymentService $eps): int {
    try {
        $eps->runDiagnostics();
        $this->info('EPS production preflight passed: schema, BDT-to-MVR flow, callbacks, and authentication are ready.');

        return 0;
    } catch (EpsPaymentException $exception) {
        $this->error('EPS production preflight failed: '.$exception->getMessage());

        return 1;
    } catch (\Throwable $exception) {
        Log::error('Unexpected EPS production preflight error.', [
            'exception' => $exception::class,
            'reason' => $exception->getMessage(),
        ]);
        $this->error('EPS production preflight failed unexpectedly. Check the Laravel log.');

        return 1;
    }
})->purpose('Validate EPS production configuration without creating a payment');

Artisan::command('eps:reconcile {--limit=50}', function (EpsPaymentService $eps): int {
    if (! Schema::hasColumn('balance_payment_transactions', 'gateway_session_id')) {
        $this->error('EPS reconciliation requires the latest database migrations.');

        return 1;
    }

    $limit = max(1, min(200, (int) $this->option('limit')));
    $payments = BalancePaymentTransaction::query()
        ->where('gateway', 'eps')
        ->where('status', PaymentStatus::Processing->value)
        ->whereNull('ledger_transaction_id')
        ->whereNotNull('gateway_session_id')
        ->where('created_at', '>=', now()->subDays(30))
        ->where('updated_at', '<=', now()->subMinutes(2))
        ->oldest('id')
        ->limit($limit)
        ->get();

    $credited = 0;
    $checked = 0;
    $errors = 0;

    foreach ($payments as $payment) {
        try {
            $result = $eps->checkStatus($payment);
            $checked++;

            if ($result->status === PaymentStatus::Successful) {
                $credited++;
            }
        } catch (EpsPaymentException $exception) {
            $errors++;
            Log::warning('Scheduled EPS reconciliation did not complete.', [
                'transaction_id' => $payment->transaction_id,
                'retryable' => $exception->retryable,
                'reason' => $exception->getMessage(),
            ]);
        } catch (\Throwable $exception) {
            $errors++;
            Log::error('Unexpected scheduled EPS reconciliation error.', [
                'transaction_id' => $payment->transaction_id,
                'exception' => $exception::class,
                'reason' => $exception->getMessage(),
            ]);
        }
    }

    $this->info("EPS reconciliation finished: {$checked} checked, {$credited} credited, {$errors} errors.");

    return 0;
})->purpose('Verify processing EPS payments and credit confirmed balances');

Schedule::command('eps:reconcile --limit=50')
    ->everyFiveMinutes()
    ->withoutOverlapping(10);
