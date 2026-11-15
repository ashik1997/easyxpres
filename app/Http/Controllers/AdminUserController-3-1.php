<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
     public function index()
    {
        $users = User::where('is_admin', false)->get();
        return view('admin.users', compact('users'));
    }

    public function approve($id)
    {
        $user = User::findOrFail($id);
        $user->status = 'approved';
        $user->save();

        return back()->with('success', 'User Approved');
    }

     public function reject($id)
    {
        $user = User::findOrFail($id);
        $user->status = 'rejected';
        $user->save();

        return back()->with('success', 'User Rejected');
    }

    public function userList()
    {
        // $users = User::where('is_admin', false)->get();
        $users = User::with('account')->where('is_admin', false)->latest()->get();
        return view('backend.user.list', compact('users'));
    }

 public function updateBalance(Request $request)
{

// dd($request->all());
    $request->validate([
        'account_id' => 'required',
        'type'       => 'required|in:add,deduct',
        'amount'     => 'required|numeric|min:1',
        'note'       => 'required',
    ]);

    $account = Account::findOrFail($request->account_id);
    

    $amount = $request->amount;

    \DB::beginTransaction();

    try {

        if ($request->type == 'add') {

            $account->balance += $amount;
            $transactionAmount = $amount; // positive


        } else {

            if ($account->balance < $amount) {
                return back()->with('error', 'Insufficient Balance');
            }

            $account->balance -= $amount;
            $transactionAmount = -$amount; // 👈 negative value
 
        }

        $account->save();

        Transaction::create([
            'account_id'     => $request->account_id,
            'transaction_id' => 'ADMIN-' . strtoupper(uniqid()),
            'type'           => 'deposit',
            'amount'         => $transactionAmount, 
            'balance_after'  => $account->balance,
            'note'           => $request->note,
            'status'         => 'approved',
        ]);

        \DB::commit();

        return back()->with('success', 'Balance Updated Successfully');

    } catch (\Exception $e) {

        \DB::rollback();
        return back()->with('error', 'Something went wrong!');
    }
}

    
}
