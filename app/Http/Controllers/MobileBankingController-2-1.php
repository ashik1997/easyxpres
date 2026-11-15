<?php

namespace App\Http\Controllers;

use App\Mail\MobileBankingSuccessfulMail;
use App\Models\MobileBanking;
use App\Models\MobileBankingOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class MobileBankingController extends Controller
{
    public function index()
    {
        $mobileBanking = MobileBanking::latest()->get();
        return view('backend.mobile_banking.index', compact('mobileBanking'));
    }

    public function create()
    {
        // Logic to show form to create a new package
        return view('backend.mobile_banking.create');
    }
    public function store(Request $request)
    {


        $request->validate([
            'name' => 'required|string|max:255',
            'rate' => 'required',
            'image_icon' => 'nullable|image|mimes:webp,svg,png,jpg,jpeg|max:2048',
        ]);

        $imagePath = null;

        if ($request->hasFile('image_icon')) {
            $imagePath = $request->file('image_icon')->store('packages', 'public');
        }

        MobileBanking::create([
            'name' => $request->name,
            'rate' => $request->rate,
            'image_icon' => $imagePath,
        ]);

        return redirect()
            ->route('admin.mobile.banking.list')
            ->with('success', 'Mobile Banking created successfully');
    }

    public function edit($id)
    {
        // Logic to show form to edit an existing package
        $package = MobileBanking::findOrFail($id);
        return view('backend.mobile_banking.edit', compact('package'));
    }

    public function update(Request $request, $id)
    {
        // Logic to update an existing package
        $package = MobileBanking::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'rate' => 'required',
            'image_icon' => 'nullable|image|mimes:webp,svg,png,jpg,jpeg|max:2048',
        ]);
        if ($request->hasFile('image_icon')) {
            $imagePath = $request->file('image_icon')->store('packages', 'public');
            $package->image_icon = $imagePath;
        }
        $package->name = $request->name;
        $package->rate = $request->rate;
        $package->save();
        return redirect()
            ->route('admin.mobile.banking.list')
            ->with('success', 'Mobile Banking updated successfully');
    }

    public function destroy($id)
    {
        // Logic to delete a package
        $package = MobileBanking::findOrFail($id);
        $package->delete();
        return redirect()
            ->route('admin.mobile.banking.list')
            ->with('success', 'Mobile Banking deleted successfully');
    }

    public function changeStatus($id)
    {
        $package = MobileBanking::findOrFail($id);

        // Toggle status correctly for ENUM
        $package->status = ($package->status === 'active')
            ? 'inactive'
            : 'active';

        $package->save();

        return redirect()->back()->with('success', 'Status updated successfully.');
    }

    public function mobileBankingHistory()
    {
        $authID = auth()->id();
        $accountID = DB::table('accounts')->where('user_id', $authID)->value('id');
        // packageorderl data fetch
        $packages = MobileBankingOrder::where('account_id', $accountID)->orderBy('created_at', 'desc')->get();
        return view('frontend.mobile_banking.history', compact('packages'));
    }

     public function appMobileBankingView()
    {
       
        return view('frontend.mobile_banking.app_view');
    }

     public function appMobileBankingHistory()
    {
        $authID = auth()->id();
        $accountID = DB::table('accounts')->where('user_id', $authID)->value('id');
        // packageorderl data fetch
        $packages = MobileBankingOrder::where('account_id', $accountID)->orderBy('created_at', 'desc')->get();
        return view('frontend.mobile_banking.app_history', compact('packages'));
    }

    public function payStore(Request $request)
    {
        // dd($request->all());

        $request->validate([
            'mobile_banking_id'     => 'required',
            'number' => 'required',
            'amount'     => 'required|numeric|min:1',
        ]);

        $userId = auth()->id();

        // Get account
        $account = DB::table('accounts')->where('user_id', $userId)->first();

        if (! $account) {
            return back()->with('error', 'No account found for this user.');
        }

        // Balance check
        if ($account->balance < $request->amount) {
            return back()->with('error', 'Insufficient balance.');
        }

        DB::transaction(function () use ($request, $userId, $account) {

        // Create order (IMPORTANT: store the created model)
        $order = MobileBankingOrder::create([
            'account_id' => $account->id,
            'mobile_banking_id' => $request->mobile_banking_id,
            'number'     => $request->number,
            'amount'     => $request->amount,
            'bdt_amount'     => $request->rate_calculation,
            'money_status'     => $request->money_status,
            'status'     => 'pending',
        ]);

        // Deduct balance
        DB::table('accounts')
            ->where('id', $account->id)
            ->decrement('balance', $order->amount);
    
              Mail::to('easyxpres9@gmail.com')->send(
            new MobileBankingSuccessfulMail($request->number, $request->amount)
        );

            });

    return back()->with('success', 'Mobile Banking initiated successfully. We will process your order shortly.');
        
    }
}
