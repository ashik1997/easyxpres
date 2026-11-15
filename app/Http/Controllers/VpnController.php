<?php

namespace App\Http\Controllers;

use App\Models\Vpn;
use App\Models\VpnName;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class VpnController extends Controller
{
    public function catalog(): View
    {
        $vpns = Vpn::with('vpnName')
            ->where('status', 'active')
            ->where('buy_now_status', false)
            ->whereDate('expire_date', '>=', today())
            ->whereHas('vpnName', fn ($query) => $query->where('status', 'active'))
            ->orderBy('payment')
            ->latest('id')
            ->get();

        return view('frontend.vpn.index', compact('vpns'));
    }

    public function history(): View
    {
        $vpns = Vpn::with('vpnName')
            ->where('user_id', auth()->id())
            ->where('buy_now_status', true)
            ->latest('purchased_at')
            ->get();

        return view('frontend.vpn.history', compact('vpns'));
    }

    public function buy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'vpn_id' => ['required', 'integer', 'exists:vpns,id'],
        ]);

        try {
            DB::transaction(function () use ($validated) {
                $vpn = Vpn::with('vpnName')->lockForUpdate()->findOrFail($validated['vpn_id']);

                if ($vpn->buy_now_status) {
                    throw new \RuntimeException('This VPN account has already been sold.');
                }

                if ($vpn->status !== 'active' || $vpn->vpnName?->status !== 'active') {
                    throw new \RuntimeException('This VPN account is not available.');
                }

                if ($vpn->expire_date->isBefore(today())) {
                    throw new \RuntimeException('This VPN account has expired.');
                }

                $account = DB::table('accounts')
                    ->where('user_id', auth()->id())
                    ->lockForUpdate()
                    ->first();

                if (! $account) {
                    throw new \RuntimeException('No balance account was found.');
                }

                if ((float) $account->balance < (float) $vpn->payment) {
                    throw new \RuntimeException('Insufficient balance.');
                }

                DB::table('accounts')
                    ->where('id', $account->id)
                    ->decrement('balance', $vpn->payment);

                $vpn->update([
                    'user_id' => auth()->id(),
                    'buy_now_status' => true,
                    'purchased_at' => now(),
                ]);
            }, 3);

            return redirect()->route('my.vpn')
                ->with('success', 'VPN account purchased successfully.');
        } catch (Throwable $exception) {
            $message = $exception instanceof \RuntimeException
                ? $exception->getMessage()
                : 'The purchase could not be completed. Please try again.';

            if (! $exception instanceof \RuntimeException) {
                report($exception);
            }

            return back()->with('error', $message);
        }
    }

    public function index(): View
    {
        $vpns = Vpn::with(['vpnName', 'user.account'])->latest()->get();

        return view('backend.vpns.index', compact('vpns'));
    }

    public function create(): View
    {
        $vpnNames = VpnName::where('status', 'active')->orderBy('name')->get();

        return view('backend.vpns.create', compact('vpnNames'));
    }

    public function store(Request $request): RedirectResponse
    {
        Vpn::create($this->validatedData($request));

        return redirect()->route('admin.vpns.index')
            ->with('success', 'VPN account created successfully.');
    }

    public function edit(Vpn $vpn): View|RedirectResponse
    {
        if ($vpn->buy_now_status) {
            return redirect()->route('admin.vpns.index')
                ->with('error', 'A sold VPN account cannot be edited.');
        }

        $vpnNames = VpnName::orderBy('name')->get();

        return view('backend.vpns.edit', compact('vpn', 'vpnNames'));
    }

    public function update(Request $request, Vpn $vpn): RedirectResponse
    {
        if ($vpn->buy_now_status) {
            return redirect()->route('admin.vpns.index')
                ->with('error', 'A sold VPN account cannot be edited.');
        }

        $vpn->update($this->validatedData($request));

        return redirect()->route('admin.vpns.index')
            ->with('success', 'VPN account updated successfully.');
    }

    public function destroy(Vpn $vpn): RedirectResponse
    {
        if ($vpn->buy_now_status) {
            return back()->with('error', 'A sold VPN account cannot be deleted.');
        }

        $vpn->delete();

        return redirect()->route('admin.vpns.index')
            ->with('success', 'VPN account deleted successfully.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'vpn_name_id' => ['required', 'integer', 'exists:vpn_names,id'],
            'expire_date' => ['required', 'date', 'after_or_equal:today'],
            'user_name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'max:255'],
            'link' => ['required', 'url', 'max:2000'],
            'payment' => ['required', 'numeric', 'min:0', 'max:9999999999999.99'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);
    }
}
