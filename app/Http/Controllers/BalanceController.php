<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\BalanceSuccessfulMail;

class BalanceController extends Controller
{
    // Show the form to add balance
    public function showAddForm()
    {
        return view('frontend.balance.add');
    }

    // Handle the balance addition
    public function addBalance(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'transaction_id' => 'required|string|unique:transactions,transaction_id',
            'file_upload' => 'nullable|file|mimes:jpg,jpeg,png|max:2048', // max 2MB
        ]);

        $user = auth()->user();
        $account = $user->account;


        DB::transaction(function () use ($request, $account) {

            // Optional file upload
            $filePath = null;
            if ($request->hasFile('file_upload')) {
                $filePath = $request->file('file_upload')->store('transactions', 'public');
            }

            // Update balance
            $newBalance = $account->balance + $request->amount;
            // $account->update([
            //     'balance' => $newBalance,
            // ]);

            // accounts table account_number need to be changed to id


            // Create transaction
            Transaction::create([

                'account_id' => $account->id,
                'transaction_id' => $request->transaction_id,
                'type'           => 'deposit',
                'amount'         => $request->amount,
                'balance_after'  => $newBalance,
                'note'           => 'Balance added by user',
                'file_upload'    => $filePath,
                'status'         => 'pending',
            ]);

             Mail::to('easyxpres9@gmail.com')->send(
            new BalanceSuccessfulMail($request->transaction_id, $request->amount)
        );
        });

        return redirect()
            ->route('dashboard')
            ->with('success', 'Balance added successfully!');
    }


     public function appAddBalance(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_bank' => 'nullable|in:mib,bml',
            'file_upload' => 'nullable|image|max:2048',
        ]);

        $user = auth()->user();
        $account = $user->account;


        DB::transaction(function () use ($request, $account) {

            // Optional file upload
            $filePath = null;
            if ($request->hasFile('file_upload')) {
                $filePath = $request->file('file_upload')->store('transactions', 'public');
            }

            // Update balance
            $newBalance = $account->balance + $request->amount;
            // $account->update([
            //     'balance' => $newBalance,
            // ]);

            // accounts table account_number need to be changed to id


            // Create transaction
            Transaction::create([

                'account_id' => $account->id,
                'transaction_id' => $request->transaction_id  ?? 'N/A',
                'type'           => 'deposit',
                'amount'         => $request->amount,
                'balance_after'  => $newBalance,
                'note'           => match ($request->payment_bank) {
                    'bml' => 'Manual balance request via Bank of Maldives',
                    'mib' => 'Manual balance request via Maldives Islamic Bank',
                    default => 'Balance added by user',
                },
                'file_upload'    => $filePath,
                'status'         => 'pending',
            ]);

             Mail::to('easyxpres9@gmail.com')->send(
            new BalanceSuccessfulMail($request->transaction_id, $request->amount)
        );
        });

        return redirect()
            ->route('dashboard')
            ->with('success', 'Balance added successfully!');
    }

    // Pending Balance Data
      public function pendingBalance()
{
    // User name relation with transactions
    // Fetch pending transactions
    // $pendingTransactions = Transaction::where('status', 'pending')->get();

    $pendingTransactions = Transaction::with('account.user')
        ->where('status', 'pending')
        ->orderBy('created_at', 'desc')
        ->get();

    return view('backend.balance.pending', compact('pendingTransactions'));
}

public function adminBalanceHistory()
{
    $transactions = Transaction::with('account.user')
        ->where('type', 'deposit')
        ->latest()
        ->get();

    return view('backend.balance.history', compact('transactions'));
}

// approved balance data
public function approvedBalance($id)
{
   $transaction = Transaction::with('account')->findOrFail($id);

    // Update recharge status
    $transaction->status = 'approved';
    $transaction->save();

    if ($transaction->account) {
        $transaction->account->balance += $transaction->amount;
        $transaction->account->save();
    }

    return redirect()->back()->with('success', 'Balance approved successfully.');
}


// rejected balance data
public function rejectedBalance($id)
{
    $transaction = Transaction::with('account')->findOrFail($id);
        $transaction->status = 'rejected';
        $transaction->save();

     
    return redirect()->back()->with('success', 'Balance rejected successfully.');
}

public function currentBalance(Request $request)
{
    $balance = (float) ($request->user()->account()->value('balance') ?? 0);

    return response()->json([
        'balance' => $balance,
        'formatted_balance' => number_format($balance, 2, '.', ','),
    ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
}

public function downloadFile(Transaction $transaction)
{
    if (!$transaction->file_upload || !Storage::disk('public')->exists($transaction->file_upload)) {
        abort(404, 'File not found');
    }

    return Storage::disk('public')->download($transaction->file_upload);
}

public function changeStatus(Request $request, Transaction $transaction)
{
    $request->validate([
        'status' => 'required|in:pending,approved,rejected'
    ]);

    $transaction->status = $request->status;
    $transaction->save();

    return back()->with('success', 'Transaction status updated successfully!');
}

public function shopWalletBalance(Request $request)
{
    if (! $this->hasValidShopWalletSignature($request)) {
        return response()->json(['success' => false, 'message' => 'Unauthorized wallet request.'], 401);
    }

    $validated = $request->validate([
        'phone' => ['required', 'string', 'max:30'],
        'amount_bdt' => ['nullable', 'numeric', 'min:0'],
    ]);

    $account = $this->shopWalletAccount($validated['phone']);
    if (! $account) {
        return response()->json([
            'success' => false,
            'code' => 'wallet_account_not_found',
            'message' => 'No active Easy Express wallet was found for this account.',
        ], 404);
    }

    $rate = $this->shopWalletExchangeRate();
    if ($rate <= 0) {
        return response()->json([
            'success' => false,
            'code' => 'exchange_rate_unavailable',
            'message' => 'Wallet payment is temporarily unavailable. Please choose another payment method.',
        ], 503);
    }

    $required = $this->shopWalletMvrAmount((float) ($validated['amount_bdt'] ?? 0), $rate);
    $balance = round((float) $account->balance, 2);

    return response()->json([
        'success' => true,
        'currency' => 'MVR',
        'balance_mvr' => $balance,
        'required_amount_mvr' => $required,
        'exchange_rate_bdt_per_mvr' => $rate,
        'sufficient' => $balance >= $required,
    ]);
}

public function shopWalletDebit(Request $request)
{
    if (! $this->hasValidShopWalletSignature($request)) {
        return response()->json(['success' => false, 'message' => 'Unauthorized wallet request.'], 401);
    }

    $validated = $request->validate([
        'phone' => ['required', 'string', 'max:30'],
        'amount_bdt' => ['required', 'numeric', 'gt:0'],
        'reference' => ['required', 'uuid'],
        'order_code' => ['required', 'string', 'max:100'],
    ]);

    $result = DB::transaction(function () use ($validated) {
        $account = $this->shopWalletAccount($validated['phone'], true);
        if (! $account) {
            return ['status' => 404, 'body' => [
                'success' => false,
                'code' => 'wallet_account_not_found',
                'message' => 'No active Easy Express wallet was found for this account.',
            ]];
        }

        $transactionId = 'SHOP-' . $validated['reference'];
        $reversalId = 'SHOP-REV-' . $validated['reference'];
        $existing = Transaction::query()->where('transaction_id', $transactionId)->lockForUpdate()->first();

        if (Transaction::query()->where('transaction_id', $reversalId)->exists()) {
            return ['status' => 409, 'body' => [
                'success' => false,
                'code' => 'payment_reversed',
                'message' => 'This wallet payment was reversed. Please try placing the order again.',
            ]];
        }

        if ($existing) {
            $original = json_decode((string) $existing->note, true) ?: [];
            if ((int) $existing->account_id !== (int) $account->id
                || $existing->type !== 'withdraw'
                || abs((float) ($original['amount_bdt'] ?? -1) - (float) $validated['amount_bdt']) > 0.001
                || ! hash_equals((string) ($original['order_code'] ?? ''), (string) $validated['order_code'])) {
                return ['status' => 409, 'body' => [
                    'success' => false,
                    'code' => 'payment_reference_conflict',
                    'message' => 'This wallet payment reference is already in use.',
                ]];
            }

            return ['status' => 200, 'body' => $this->shopWalletDebitResponse(
                $existing,
                (float) $validated['amount_bdt'],
                (float) ($original['exchange_rate_bdt_per_mvr'] ?? 0),
                true
            )];
        }

        $rate = $this->shopWalletExchangeRate();
        if ($rate <= 0) {
            return ['status' => 503, 'body' => [
                'success' => false,
                'code' => 'exchange_rate_unavailable',
                'message' => 'Wallet payment is temporarily unavailable. Please choose another payment method.',
            ]];
        }

        $amountMvr = $this->shopWalletMvrAmount((float) $validated['amount_bdt'], $rate);

        $balanceBefore = round((float) $account->balance, 2);
        if ($balanceBefore < $amountMvr) {
            return ['status' => 422, 'body' => [
                'success' => false,
                'code' => 'insufficient_balance',
                'message' => 'Insufficient wallet balance. Please choose another payment method.',
                'balance_mvr' => $balanceBefore,
                'required_amount_mvr' => $amountMvr,
            ]];
        }

        $balanceAfter = round($balanceBefore - $amountMvr, 2);
        $account->balance = $balanceAfter;
        $account->save();

        $transaction = Transaction::create([
            'account_id' => $account->id,
            'transaction_id' => $transactionId,
            'type' => 'withdraw',
            'amount' => $amountMvr,
            'balance_after' => $balanceAfter,
            'note' => json_encode([
                'source' => 'ecom_shop',
                'order_code' => $validated['order_code'],
                'payment_reference' => $validated['reference'],
                'amount_bdt' => round((float) $validated['amount_bdt'], 2),
                'exchange_rate_bdt_per_mvr' => $rate,
            ], JSON_UNESCAPED_SLASHES),
            'status' => 'approved',
        ]);

        return ['status' => 200, 'body' => $this->shopWalletDebitResponse(
            $transaction,
            (float) $validated['amount_bdt'],
            $rate,
            false
        )];
    }, 3);

    return response()->json($result['body'], $result['status']);
}

public function shopWalletReverse(Request $request)
{
    if (! $this->hasValidShopWalletSignature($request)) {
        return response()->json(['success' => false, 'message' => 'Unauthorized wallet request.'], 401);
    }

    $validated = $request->validate([
        'phone' => ['required', 'string', 'max:30'],
        'reference' => ['required', 'uuid'],
        'reason' => ['nullable', 'string', 'max:500'],
    ]);

    $result = DB::transaction(function () use ($validated) {
        $account = $this->shopWalletAccount($validated['phone'], true);
        if (! $account) {
            return ['status' => 404, 'body' => [
                'success' => false,
                'code' => 'wallet_account_not_found',
                'message' => 'No active Easy Express wallet was found for this account.',
            ]];
        }

        $debitId = 'SHOP-' . $validated['reference'];
        $reversalId = 'SHOP-REV-' . $validated['reference'];
        $debit = Transaction::query()
            ->where('transaction_id', $debitId)
            ->where('account_id', $account->id)
            ->lockForUpdate()
            ->first();

        if (! $debit || $debit->type !== 'withdraw' || $debit->status !== 'approved') {
            return ['status' => 404, 'body' => [
                'success' => false,
                'code' => 'wallet_transaction_not_found',
                'message' => 'The wallet debit could not be found.',
            ]];
        }

        $existing = Transaction::query()->where('transaction_id', $reversalId)->lockForUpdate()->first();
        if ($existing) {
            return ['status' => 200, 'body' => [
                'success' => true,
                'transaction_id' => $existing->transaction_id,
                'balance_after_mvr' => round((float) $existing->balance_after, 2),
                'already_processed' => true,
            ]];
        }

        $balanceAfter = round((float) $account->balance + (float) $debit->amount, 2);
        $account->balance = $balanceAfter;
        $account->save();

        $reversal = Transaction::create([
            'account_id' => $account->id,
            'transaction_id' => $reversalId,
            'type' => 'deposit',
            'amount' => (float) $debit->amount,
            'balance_after' => $balanceAfter,
            'note' => json_encode([
                'source' => 'ecom_shop_reversal',
                'original_transaction_id' => $debit->transaction_id,
                'payment_reference' => $validated['reference'],
                'reason' => $validated['reason'] ?? null,
            ], JSON_UNESCAPED_SLASHES),
            'status' => 'approved',
        ]);

        return ['status' => 200, 'body' => [
            'success' => true,
            'transaction_id' => $reversal->transaction_id,
            'balance_after_mvr' => $balanceAfter,
            'already_processed' => false,
        ]];
    }, 3);

    return response()->json($result['body'], $result['status']);
}

private function hasValidShopWalletSignature(Request $request): bool
{
    $key = (string) config('shop_wallet.key', '');
    $timestamp = (string) $request->header('X-Shop-Timestamp', '');
    $signature = (string) $request->header('X-Shop-Signature', '');

    if ($key === '' || ! ctype_digit($timestamp) || ! preg_match('/^[a-f0-9]{64}$/', $signature)) {
        return false;
    }

    $clockSkew = max(30, (int) config('shop_wallet.clock_skew_seconds', 300));
    if (abs(now()->timestamp - (int) $timestamp) > $clockSkew) {
        return false;
    }

    $signed = $timestamp . "\n" . $request->method() . "\n" . $request->getPathInfo() . "\n" . $request->getContent();

    return hash_equals(hash_hmac('sha256', $signed, $key), $signature);
}

private function shopWalletAccount(string $phone, bool $lock = false): ?Account
{
    $digits = preg_replace('/\D+/', '', $phone) ?? '';
    if ($digits === '') {
        return null;
    }

    $phones = [$digits, '+' . $digits];
    if (str_starts_with($digits, '88')) {
        $phones[] = substr($digits, 2);
    } elseif (str_starts_with($digits, '01')) {
        $phones[] = '88' . $digits;
        $phones[] = '+88' . $digits;
    }
    if (str_starts_with($digits, '960')) {
        $phones[] = substr($digits, 3);
    }

    $user = User::query()
        ->where('status', 'approved')
        ->whereIn('phone', array_values(array_unique($phones)))
        ->first();

    if (! $user) {
        return null;
    }

    $query = Account::query()->where('user_id', $user->id);
    return $lock ? $query->lockForUpdate()->first() : $query->first();
}

private function shopWalletExchangeRate(): float
{
    return round((float) (DB::table('app_setups')->where('id', 1)->value('mvr_to_bdt') ?? 0), 6);
}

private function shopWalletMvrAmount(float $amountBdt, float $rate): float
{
    return round($amountBdt / $rate, 2);
}

private function shopWalletDebitResponse(Transaction $transaction, float $amountBdt, float $rate, bool $alreadyProcessed): array
{
    return [
        'success' => true,
        'transaction_id' => $transaction->transaction_id,
        'amount_bdt' => round($amountBdt, 2),
        'amount_mvr' => round((float) $transaction->amount, 2),
        'exchange_rate_bdt_per_mvr' => $rate,
        'balance_after_mvr' => round((float) $transaction->balance_after, 2),
        'already_processed' => $alreadyProcessed,
    ];
}

 public function balanceHistory()
    {
      
        return view('frontend.balance.history');
    }

    public function appBalanceHistory()
    {
      
        return view('frontend.balance.app_history');
    }

}
