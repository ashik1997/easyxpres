<?php

namespace App\Http\Controllers;

use App\Models\VpnName;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class VpnNameController extends Controller
{
    public function index(): View
    {
        $vpnNames = VpnName::withCount([
            'vpns',
            'vpns as available_vpns_count' => fn ($query) => $query
                ->where('status', 'active')
                ->where('buy_now_status', false)
                ->whereDate('expire_date', '>=', today()),
        ])->latest()->get();

        return view('backend.vpn_names.index', compact('vpnNames'));
    }

    public function create(): View
    {
        return view('backend.vpn_names.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:vpn_names,name'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        VpnName::create($validated);

        return redirect()->route('admin.vpn_names.index')
            ->with('success', 'VPN name created successfully.');
    }

    public function edit(VpnName $vpnName): View
    {
        return view('backend.vpn_names.edit', compact('vpnName'));
    }

    public function update(Request $request, VpnName $vpnName): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('vpn_names', 'name')->ignore($vpnName->id),
            ],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $vpnName->update($validated);

        return redirect()->route('admin.vpn_names.index')
            ->with('success', 'VPN name updated successfully.');
    }

    public function destroy(VpnName $vpnName): RedirectResponse
    {
        if ($vpnName->vpns()->exists()) {
            return back()->with('error', 'Delete the VPN accounts under this name first.');
        }

        $vpnName->delete();

        return redirect()->route('admin.vpn_names.index')
            ->with('success', 'VPN name deleted successfully.');
    }
}
