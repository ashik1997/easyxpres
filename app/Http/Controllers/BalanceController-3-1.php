<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BalanceController extends Controller
{
    // Show the form to add balance
    public function showAddForm()
    {
        return view('balance.add');
    }

    // Handle the balance addition
    public function addBalance(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'transaction_id' => 'required|string|unique:transactions,transaction_id',
            'file_upload' => 'nullable|file|max:2048',
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
            $account->update([
                'balance' => $newBalance,
            ]);

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
            ]);
        });

        return redirect()
            ->route('dashboard')
            ->with('success', 'Balance added successfully!');
    }
}
