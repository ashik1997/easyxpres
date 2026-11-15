<?php

namespace App\Http\Controllers;

use App\Models\appSetup;
use App\Models\CallingCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CallingCardController extends Controller
{
    public function appCallingCard()
    {
        $appSetup = appSetup::where('id', 1)->first();
        $callingCards = CallingCard::orderByDesc('id')->where('buy_now_status', '0')->get();
        return view('frontend.calling_card.details', compact('appSetup', 'callingCards'));
    }

    public function webCallingCard()
    {
        $appSetup = appSetup::where('id', 1)->first();
        $callingCards = CallingCard::orderByDesc('id')->where('buy_now_status', '0')->get();
        return view('frontend.calling_card.web_details', compact('appSetup', 'callingCards'));
    }

   public function myCallingCard()
    {
        $userId = auth()->id();
        $appSetup = appSetup::where('id', 1)->first();
        $callingCards = CallingCard::where('user_id', $userId)
            ->where('buy_now_status', 1)
            ->orderByDesc('updated_at')
            ->limit(20)
            ->get();
        return view('frontend.calling_card.my_calling_cards', compact('appSetup', 'callingCards'));
    }


    public function buy(Request $request)
    {
        $userId = auth()->id();

        try {

            DB::transaction(function () use ($request, $userId) {

                // Find card
                $card = CallingCard::lockForUpdate()->findOrFail($request->id);

                // Prevent duplicate purchase
                if ($card->buy_now_status == 1) {
                    throw new \Exception('Card already sold.');
                }

                // Get account
                $account = DB::table('accounts')
                    ->where('user_id', $userId)
                    ->lockForUpdate()
                    ->first();

                if (!$account) {
                    throw new \Exception('No account found.');
                }

                // Balance check
                if ($account->balance < $card->payment) {
                    throw new \Exception('Insufficient balance.');
                }

                // Update card owner
                $card->update([
                    'buy_now_status' => 1,
                    'user_id' => $userId,
                    'updated_at' => now(),
                ]);

                // Deduct balance
                DB::table('accounts')
                    ->where('id', $account->id)
                    ->decrement('balance', $card->payment);
            });

            return back()->with('success', 'Card purchased successfully');
        } catch (\Exception $e) {

            return back()->with('error', $e->getMessage());
        }
    }

    public function indexCallingCards()
    {
        $callingCards = CallingCard::orderByDesc('id')->get();
        return view('backend.calling_card.index', compact('callingCards'));
    }

    // calling card destroy
    public function destroyCalling($id)
    {
        $callingCard = CallingCard::findOrFail($id);
        $callingCard->delete();

        return redirect()->route('admin.calling_card.index')->with('success', 'Calling Card deleted successfully!');
    }

    public function editCallingCard($id)
    {
        $callingCard = CallingCard::findOrFail($id);
        return view('backend.calling_card.edit', compact('callingCard'));
    }

    public function updateCallingCard(Request $request, $id)
    {
        $callingCard = CallingCard::findOrFail($id);

        $callingCard->update([
            'card_no' => $request->card_no,
            'user_name' => $request->user_name,
            'pin' => $request->pin,
            'point' => $request->point,
            'minute_sec' => $request->minute_sec,
            'payment' => $request->payment,
        ]);

        return redirect()->route('admin.calling_card.index')->with('success', 'Calling Card updated successfully!');
    }

    public function createCallingCards()
    {
        return view('backend.calling_card.create');
    }

    public function storeCallingCards(Request $request)
    {

        // dd($request->all());

        CallingCard::create([
            'card_no' => $request->card_no,
            'user_name' => $request->user_name,
            'pin' => $request->pin,
            'point' => $request->point,
            'minute_sec' => $request->minute_sec,
            'payment' => $request->payment,

        ]);

        // Redirect back with a success message
        return redirect()->route('admin.calling_card.index')->with('success', 'Calling Card created successfully!');
    }
}
