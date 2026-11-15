<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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

    // dd($request->all());

        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);


   
       
        $users = auth()->user();
        $user = $users->account()->first();
        $user->amount = $request->amount;
        // $user->balance_after += $request->input('amount');
        $user->save();

        dd($user);

        return redirect()->route('dashboard')->with('success', 'Balance added successfully!');
    }
}
