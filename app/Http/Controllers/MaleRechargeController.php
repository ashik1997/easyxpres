<?php

namespace App\Http\Controllers;

use App\Mail\MaleRechargeSuccessfulMail;
use App\Models\MaleRecharge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\RechargeSuccessfulMail;
use App\Models\MaliOperator;

class MaleRechargeController extends Controller
{
    public function showRechargeForm()
    {
        return view('frontend.male_recharge.create', [
            'operators' => $this->activeOperators(),
        ]);
    }

    public function submitRecharge(Request $request)
    {
        // Validate the request data
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

            MaleRecharge::create([
                'account_id' => $account->id,
                'mobile'     => $request->mobile,
                'amount'     => $request->amount,
                'status'     => 'pending',
            ]);

            // ✅ DEDUCT balance
            DB::table('accounts')
                ->where('id', $account->id)
                ->decrement('balance', $request->amount);

            Mail::to('easyxpres9@gmail.com')->send(
                new MaleRechargeSuccessfulMail($request->mobile, $request->amount)
            );
        });
        // Redirect back with a success message
        return redirect()->route('dashboard')->with('success', 'Male Recharge request submitted successfully!');
    }

    public function appSubmitRecharge(Request $request)
    {
        // Validate the request data (minimum 20 enforced here)
        $request->validate([
            'mobile' => 'required|string|max:15',
            'amount' => 'required|numeric|min:10', // ✅ minimum 10
        ], [
            'amount.min' => 'The recharge amount must be at least 10 MVR.',
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

        // DB transaction to safely create recharge and deduct balance
        DB::transaction(function () use ($request, $account) {

            MaleRecharge::create([
                'account_id' => $account->id,
                'mobile'     => $request->mobile,
                'amount'     => $request->amount,
                'status'     => 'pending',
            ]);

            // Deduct balance
            DB::table('accounts')
                ->where('id', $account->id)
                ->decrement('balance', $request->amount);

            // Send email notification (optional: queue recommended)
            Mail::to('easyxpres9@gmail.com')->send(
                new MaleRechargeSuccessfulMail($request->mobile, $request->amount)
            );
        });

        // Redirect back with success
        return redirect()->route('dashboard')->with('success', 'Male Recharge request submitted successfully!');
    }

    public function rechargeHistory()
    {
        $authID = auth()->id();
        $accountID = DB::table('accounts')->where('user_id', $authID)->value('id');

        $recharges = MaleRecharge::where('account_id', $accountID)->orderBy('created_at', 'desc')->get();

        $operators = $this->activeOperators();

        return view('frontend.male_recharge.history', compact('recharges', 'operators'));
    }

    public function appRechargeHistory()
    {
        $authID = auth()->id();
        $accountID = DB::table('accounts')->where('user_id', $authID)->value('id');

        $recharges = MaleRecharge::where('account_id', $accountID)->orderBy('created_at', 'desc')->get();

        $operators = $this->activeOperators();

        return view('frontend.male_recharge.app_history', compact('recharges', 'operators'));
    }

    public function index()
    {
        $recharges = MaleRecharge::with('account.user')->orderBy('created_at', 'desc')->get();
        return view('admin.male_recharges.index', compact('recharges'));
    }

    public function approveRecharge($id)
    {
        $recharge = MaleRecharge::with('account')->findOrFail($id);

        $commission = 0;

        $mobile = $recharge->mobile;
        if (strpos($mobile, '+') === 0) {
            $mobile = substr($mobile, 1);
        }

        $operator = MaliOperator::where('status', 'active')
            ->where(function ($query) use ($mobile) {
                $query->whereRaw("? LIKE CONCAT(code, '%')", [$mobile]);
            })
            ->first();

        if ($operator) {
            $commission = $recharge->amount * ($operator->commission / 100);
        }

        $recharge->status = 'approved';
        $recharge->commission = $commission;
        $recharge->save();

        if ($recharge->account) {
            $recharge->account->balance = $recharge->account->balance + $commission;
            $recharge->account->save();
        }

        return redirect()->route('admin.male.recharges.pending')
            ->with('success', 'Male Recharge approved successfully.');
    }

    public function rejectRecharge($id)
    {
        $recharge = MaleRecharge::with('account')->findOrFail($id);

        if ($recharge->account) {
            $recharge->account->balance = $recharge->account->balance - $recharge->commission + $recharge->amount;
            $recharge->account->save();
        }

        $recharge->commission = 0;
        $recharge->status = 'rejected';
        $recharge->save();


        return redirect()->route('admin.male.recharges.pending')->with('success', 'Male Recharge rejected successfully.');
    }

    private function activeOperators()
    {
        return MaliOperator::where('status', 'active')
            ->orderBy('name')
            ->get(['name', 'code', 'commission']);
    }
}
