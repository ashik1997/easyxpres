<?php

namespace App\Http\Controllers;

use App\Models\MobileBankingOrder;
use Illuminate\Http\Request;

class MobileBankingOrderController extends Controller
{
    public function index()
    {
        $packageOrders = MobileBankingOrder::with('mobile')->orderBy('created_at', 'desc')->get();

        return view('backend.mobile_banking_orders.index', compact('packageOrders'));
    }

     public function approveOrder($id)
    {
        $order = MobileBankingOrder::with('account')->findOrFail($id);
        $order->status = 'approved';
        $order->save();

       

        return redirect()->back()->with('success', 'Mobile Banking Order approved successfully.');
    }
    // reject order method
    public function rejectOrder($id)
    {
        $order = MobileBankingOrder::with('account')->findOrFail($id);
        $order->status = 'rejected';
        $order->save();

         if ($order->account) {
        $order->account->balance += $order->amount;
        $order->account->save();
    }

        return redirect()->back()->with('success', 'Mobile Banking Order rejected successfully.');
    }

    public function updateNote(Request $request, $id)
{
    $request->validate([
        'note_admin' => 'nullable|string|max:500',
    ]);

    $order = MobileBankingOrder::findOrFail($id);
    $order->note_admin = $request->note_admin;
    $order->save();

    return redirect()->back()
        ->with('success', 'Admin note updated successfully.');
}


}
