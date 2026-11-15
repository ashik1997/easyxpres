<?php

namespace App\Http\Controllers;

use App\Models\Account;
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
   

    $account = Account::findOrFail($request->account_id);

    if ($request->type == 'add') {
        $account->balance += $request->amount;
    } else {
        if ($account->balance < $request->amount) {
            return back()->with('error', 'Insufficient Balance');
        }
        $account->balance -= $request->amount;
    }

    $account->save();

    return back()->with('success', 'Balance Updated Successfully');
}

    
}
