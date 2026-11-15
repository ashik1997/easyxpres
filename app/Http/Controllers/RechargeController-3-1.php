<?php

namespace App\Http\Controllers;

use App\Models\PackageOrderl;
use App\Models\Recharge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RechargeController extends Controller
{
  public function submitRecharge(Request $request)
{
    $request->validate([
        'mobile' => 'required|string|max:15',
        'amount' => 'required|numeric|min:1',
    ]);

    $userId = auth()->id();

    $account = DB::table('accounts')
        ->where('user_id', $userId)
        ->lockForUpdate()
        ->first();

    if (! $account) {
        return back()->with('error', 'No account found.');
    }

    // ✅ Balance check
    if ($account->balance < $request->amount) {
        return back()->with('error', 'Insufficient balance.');
    }

    DB::transaction(function () use ($request, $account) {

        Recharge::create([
            'account_id' => $account->id,
            'mobile'     => $request->mobile,
            'amount'     => $request->amount,
            'status'     => 'approved',
        ]);

        // ✅ DEDUCT balance
        DB::table('accounts')
            ->where('id', $account->id)
            ->decrement('balance', $request->amount);
    });

    return redirect()
        ->route('dashboard')
        ->with('success', 'Recharge successful. Balance deducted.');
}



    public function rechargeHistory()
    {
        $authID = auth()->id();
        $accountID = DB::table('accounts')->where('user_id', $authID)->value('id');

        $recharges = Recharge::where('account_id', $accountID)->orderBy('created_at', 'desc')->get();

        return view('frontend.recharge.history', compact('recharges'));
    }

    public function packageHistory()
    {
       $authID = auth()->id();
        $accountID = DB::table('accounts')->where('user_id', $authID)->value('id');
        // packageorderl data fetch
        $packages = PackageOrderl::where('account_id', $accountID)->orderBy('created_at', 'desc')->get();
        return view('frontend.package.history', compact('packages'));
    }

    // Admin Functions
    public function index()
    {
        $recharges = Recharge::with('account.user')->orderBy('created_at', 'desc')->get();
    return view('admin.recharges.index', compact('recharges'));
    }

    public function approveRecharge($id)
{
    // Find the recharge
    $recharge = Recharge::with('account')->findOrFail($id);

    // Update recharge status
    $recharge->status = 'approved';
    $recharge->save();

    // Update account balance
    if ($recharge->account) {
        $recharge->account->balance -= $recharge->amount;
        $recharge->account->save();
    }

    return redirect()->route('admin.recharges.pending')
                     ->with('success', 'BD Recharge approved successfully.');
}


    public function rejectRecharge($id)
    {
        $recharge = Recharge::with('account')->findOrFail($id);
        $recharge->status = 'rejected';
        $recharge->save();

        if ($recharge->account) {
        $recharge->account->balance += $recharge->amount;
        $recharge->account->save();
    }

        return redirect()->route('admin.recharges.pending')->with('success', 'BD Recharge rejected successfully.');
    }
}
