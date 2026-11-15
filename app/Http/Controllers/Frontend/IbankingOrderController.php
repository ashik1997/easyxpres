<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Mail\iBankingSuccessfulMail;
use App\Models\BankName;
use App\Models\IbankingOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class IbankingOrderController extends Controller
{
    private const MINIMUM_BDT_AMOUNT = 30000;

    public function showForm()
    {
        $banks = BankName::query()
            ->where('status', 'active')
            ->orderBy('bank_name')
            ->get(['id', 'bank_name']);

        $rate = (float) (DB::table('ibanking_rates')
            ->where('status', 'active')
            ->latest('id')
            ->value('rate') ?? 0);

        $minimumBdt = self::MINIMUM_BDT_AMOUNT;

        return view('frontend.ibanking.create', compact('banks', 'rate', 'minimumBdt'));
    }

    public function addiBanking(Request $request)
    {
        $request->validate([
            'bank_name_id' => [
                'required',
                Rule::exists('bank_names', 'id')->where('status', 'active'),
            ],
            'account_holder_name' => 'required|string|max:120',
            'branch' => 'required|string|max:120',
            'account_no' => 'required|string|max:50',
            'document_attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'amount' => 'required|numeric|min:1',
        ]);

        $rate = (float) (DB::table('ibanking_rates')
            ->where('status', 'active')
            ->value('rate') ?? 0);

        if ($rate <= 0) {
            return back()->with('error', 'iBanking conversion rate is unavailable.');
        }

        $bdtAmount = round((float) $request->amount * $rate, 2);
        $this->ensureMinimumBdtAmount($bdtAmount);

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
        $documentPath = $this->storeDocument($request);
        $accountHolderName = $request->input('account_holder_name');

        DB::transaction(function () use ($request, $account, $bdtAmount, $documentPath, $accountHolderName) {

            IbankingOrder::create([
                'account_id' => $account->id,
                'bank_name_id'     => $request->bank_name_id,
                'account_holder_name' => $accountHolderName,
                'branch' => $request->branch,
                'account_no'     => $request->account_no,
                'document_attachment' => $documentPath,
                'amount'     => $request->amount,
                'bdt_amount'     => $bdtAmount,
                'status'     => 'pending',
            ]);

            // ✅ DEDUCT balance
            DB::table('accounts')
                ->where('id', $account->id)
                ->decrement('balance', $request->amount);

            Mail::to('easyxpres9@gmail.com')->send(
                new iBankingSuccessfulMail($request->account_no, $request->amount)
            );
        });

        return redirect()
            ->route('dashboard')
            ->with('success', 'iBanking successful. Balance deducted.');
    }

    public function appAddiBanking(Request $request)
    {
        $request->validate([
            'bank_name_id' => [
                'required',
                Rule::exists('bank_names', 'id')->where('status', 'active'),
            ],
            'account_holder_name' => 'required|string|max:120',
            'branch'       => 'required|string|max:120',
            'account_no'   => 'required|string|max:50',
            'document_attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'amount'       => 'required|numeric|min:1',
        ]);

        $rate = (float) (DB::table('ibanking_rates')
            ->where('status', 'active')
            ->value('rate') ?? 0);

        if ($rate <= 0) {
            return back()->with('error', 'iBanking conversion rate is unavailable.');
        }

        $bdtAmount = round((float) $request->amount * $rate, 2);
        $this->ensureMinimumBdtAmount($bdtAmount);

        $userId = auth()->id();

        try {
            DB::transaction(function () use ($request, $userId, $bdtAmount) {

                $account = DB::table('accounts')
                    ->where('user_id', $userId)
                    ->lockForUpdate()
                    ->first();

                if (! $account) {
                    throw new \Exception('No account found.');
                }

                if ($account->balance < $request->amount) {
                    throw new \Exception('Insufficient balance.');
                }

                $documentPath = $this->storeDocument($request);
                $accountHolderName = $request->input('account_holder_name');

                IbankingOrder::create([
                    'account_id'   => $account->id,
                    'bank_name_id' => $request->bank_name_id,
                    'account_holder_name' => $accountHolderName,
                    'branch' => $request->branch,
                    'account_no'   => $request->account_no,
                    'document_attachment' => $documentPath,
                    'amount'       => $request->amount,
                    'bdt_amount'   => $bdtAmount,
                    'status'       => 'pending',
                ]);

                DB::table('accounts')
                    ->where('id', $account->id)
                    ->decrement('balance', $request->amount);

                Mail::to('easyxpres9@gmail.com')->send(
                    new iBankingSuccessfulMail($request->account_no, $request->amount)
                );
            });

            return redirect()
                ->route('dashboard')
                ->with('success', 'iBanking successful. Balance deducted.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function iBankingHistory()
    {
        $authID = auth()->id();
        $accountID = DB::table('accounts')->where('user_id', $authID)->value('id');
        // packageorderl data fetch
        $packages = IbankingOrder::with('bank')->where('account_id', $accountID)->orderBy('created_at', 'desc')->get();
        return view('frontend.ibanking.history', compact('packages'));
    }

    public function appiBankingHistory()
    {
        $authID = auth()->id();
        $accountID = DB::table('accounts')->where('user_id', $authID)->value('id');
        // packageorderl data fetch
        $packages = IbankingOrder::with('bank')->where('account_id', $accountID)->orderBy('created_at', 'desc')->get();
        return view('frontend.ibanking.app_history', compact('packages'));
    }

    public function iBankingOrder()
    {
        $packageOrders = IbankingOrder::with(['bank', 'account.user'])->orderBy('created_at', 'desc')->get();

        return view('backend.ibanking_orders.index', compact('packageOrders'));
    }

    public function approveOrder($id)
    {
        $order = IbankingOrder::with('account')->findOrFail($id);
        $order->status = 'approved';
        $order->save();



        return redirect()->back()->with('success', 'iBanking Order approved successfully.');
    }
    // reject order method
    public function rejectOrder($id)
    {
        $order = IbankingOrder::with('account')->findOrFail($id);
        $order->status = 'rejected';
        $order->save();

        if ($order->account) {
            $order->account->balance += $order->amount;
            $order->account->save();
        }

        return redirect()->back()->with('success', 'iBanking Order rejected successfully.');
    }

    public function uploadSlip(Request $request, $id)
    {
        $request->validate([
            'upload_slip' => 'required|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $order = IBankingOrder::findOrFail($id);

        if ($request->hasFile('upload_slip')) {

            $filePath = $request->file('upload_slip')
                ->store('transactions', 'public');

            $order->upload_slip = $filePath;
        }


        $order->save();

        return back()->with('success', 'Slip uploaded successfully.');
    }

    public function viewAttachment($id, string $type)
    {
        $path = $this->attachmentPath($id, $type);

        abort_unless($path && Storage::disk('public')->exists($path), 404);

        return response()->file(Storage::disk('public')->path($path));
    }

    public function downloadAttachment($id, string $type)
    {
        $path = $this->attachmentPath($id, $type);

        abort_unless($path && Storage::disk('public')->exists($path), 404);

        return Storage::disk('public')->download($path, basename($path));
    }

    public function viewUserAttachment($id, string $type)
    {
        $path = $this->userAttachmentPath($id, $type);

        abort_unless($path && Storage::disk('public')->exists($path), 404);

        return response()->file(Storage::disk('public')->path($path));
    }

    public function downloadUserAttachment($id, string $type)
    {
        $path = $this->userAttachmentPath($id, $type);

        abort_unless($path && Storage::disk('public')->exists($path), 404);

        return Storage::disk('public')->download($path, basename($path));
    }

    private function storeDocument(Request $request): ?string
    {
        return $request->hasFile('document_attachment')
            ? $request->file('document_attachment')->store('ibanking-documents', 'public')
            : null;
    }

    private function attachmentPath($id, string $type): ?string
    {
        abort_unless(in_array($type, ['document', 'slip'], true), 404);

        $order = IbankingOrder::findOrFail($id);
        return $this->pathForAttachment($order, $type);
    }

    private function userAttachmentPath($id, string $type): ?string
    {
        abort_unless(in_array($type, ['document', 'slip'], true), 404);

        $order = IbankingOrder::whereHas('account', function ($query) {
            $query->where('user_id', auth()->id());
        })->findOrFail($id);

        return $this->pathForAttachment($order, $type);
    }

    private function pathForAttachment(IbankingOrder $order, string $type): ?string
    {
        $path = $type === 'document' ? $order->document_attachment : $order->upload_slip;

        return $path ? ltrim(str_replace('public/', '', $path), '/') : null;
    }

    private function ensureMinimumBdtAmount(float $bdtAmount): void
    {
        if ($bdtAmount < self::MINIMUM_BDT_AMOUNT) {
            throw ValidationException::withMessages([
                'amount' => 'The converted iBanking amount must be at least 30,000 BDT.',
            ]);
        }
    }
}
