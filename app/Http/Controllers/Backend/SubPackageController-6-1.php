<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\PackageDetail;
use App\Models\PackageOrderl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubPackageController extends Controller
{
    public function index()
    {
        // Logic to list all sub-packages
        $packages = Package::all();
        return view('backend.subpackages.index', compact('packages'));
    }

    public function create()
    {
        // Logic to show form to create a new sub-package
        $packages = Package::all();
        return view('backend.subpackages.create', compact('packages'));
    }

    public function store(Request $request)
    {
        // Logic to store a new sub-package
        $request->validate([
            'package_id' => 'required|exists:packages,id',
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'commission' => 'required|numeric',
            'offer_price' => 'nullable|numeric',
        ]);
        PackageDetail::create($request->all());
        return redirect()->route('admin.subpackages.list')->with('success', 'Sub-package created successfully.');
    }

    public function edit($id)
    {
        // Logic to show form to edit an existing sub-package
        $subpackage = PackageDetail::findOrFail($id);
        $packages = Package::all();
        return view('backend.subpackages.edit', compact('subpackage', 'packages'));
    }

    public function update(Request $request, $id)
    {
        // Logic to update an existing sub-package
        $request->validate([
            'package_id' => 'required|exists:packages,id',
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'commission' => 'required|numeric',
            'offer_price' => 'nullable|numeric',
        ]);
        $subpackage = PackageDetail::findOrFail($id);
        $subpackage->update($request->all());
        return redirect()->route('admin.subpackages.list')->with('success', 'Sub-package updated successfully.');
    }

    public function destroy($id)
    {
        // Logic to delete an existing sub-package
        $subpackage = PackageDetail::findOrFail($id);
        $subpackage->delete();
        return redirect()->route('admin.subpackages.list')->with('success', 'Sub-package deleted successfully.');
    }

    // status change method
    public function changeStatusSub($id)
    {
        $subpackage = PackageDetail::findOrFail($id);

        // Toggle status correctly for ENUM
        $subpackage->status = ($subpackage->status === 'active')
            ? 'inactive'
            : 'active';

        $subpackage->save();

        return redirect()->back()->with('success', 'Status updated successfully.');
    }

    //package show sub-packages method
    public function showSubPackages($packageId)
    {

        $package = Package::findOrFail($packageId);
        $subpackages = PackageDetail::where('package_id', $packageId)->where('status', 'active')->get();
        return view('frontend.package_details', compact('package', 'subpackages'));
    }

    // payStore 
  public function payStore(Request $request)
{
    $request->validate([
        'mobile'     => 'required|digits:11',
        'package_id' => 'required|exists:package_details,id',
    ]);

    $userId = auth()->id();

    // Check account
    $accountId = DB::table('accounts')->where('user_id', $userId)->value('id');
    if (!$accountId) {
        return back()->with('error', 'No account found for this user.');
    }




    // Create order
    PackageOrderl::create([
        'user_id'    => $userId,
        'account_id' => $accountId,
       'package_id' => $request->package_id, 
        'number'     => $request->mobile,
        'amount'     => $request->amount, 
        'status'     => 'pending',
    ]);

    return back()->with('success', 'Payment initiated successfully. We will process your order shortly.');
}




    // package order list method
    public function packageOrders()
    {
        
        $packageOrders = PackageOrderl::with('package')->orderBy('created_at', 'desc')->get();
        return view('backend.package_orders.index', compact('packageOrders'));
    }

     public function orders($id)
{
    // Fetch package
    $package = Package::findOrFail($id);

    // Fetch related orders
    $subpackages = PackageDetail::where('package_id', $id)->latest()->get();

    return view('backend.package_orders.orders', compact('package', 'subpackages'));
}


    // approve order method
    public function approveOrder($id)
    {
        $order = PackageOrderl::with('account')->findOrFail($id);
        $order->status = 'approved';
        $order->save();

        if ($order->account) {
        $order->account->balance -= $order->amount;
        $order->account->save();
    }

        return redirect()->back()->with('success', 'Order approved successfully.');
    }
    // reject order method
    public function rejectOrder($id)
    {
        $order = PackageOrderl::with('account')->findOrFail($id);
        $order->status = 'rejected';
        $order->save();

         if ($order->account) {
        $order->account->balance += $order->amount;
        $order->account->save();
    }

        return redirect()->back()->with('success', 'Order rejected successfully.');
    }
}
