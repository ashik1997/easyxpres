<x-frontend-layout>
    <x-slot:metaTitle>My VPN Accounts · EasyXpres</x-slot:metaTitle>
    <x-slot:header><p class="text-[10px] font-bold uppercase tracking-[.18em] text-teal-100">Secure access</p><h1 class="truncate text-lg font-extrabold text-white">My VPN</h1></x-slot:header>

    <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-end"><div><span class="ux-eyebrow">Purchased accounts</span><h2 class="ux-title mt-1">Your VPN credentials</h2><p class="ux-muted mt-2 text-sm">Keep these credentials private.</p></div><a href="{{ route('app.vpn') }}" class="ux-primary-button"><i class="fas fa-plus"></i> Buy VPN</a></div>
    <div class="grid gap-5 lg:grid-cols-2">
        @forelse ($vpns as $vpn)
            @php($expired = $vpn->expire_date->isBefore(today()))
            <article class="ux-card overflow-hidden"><div class="flex items-center justify-between gap-3 bg-gradient-to-r from-[#005e68] to-[#00a892] px-5 py-4 text-white"><div><p class="text-[10px] font-bold uppercase tracking-[.16em] text-teal-50/70">VPN account</p><h3 class="mt-1 text-lg font-extrabold">{{ $vpn->vpnName->name }}</h3></div><x-user-status :status="$expired ? 'expired' : 'active'" /></div><div class="space-y-3 p-5">@foreach ([['Username', $vpn->user_name], ['Password', $vpn->password]] as [$label, $value])<div class="flex items-center justify-between gap-3 rounded-2xl bg-slate-50 p-3"><div class="min-w-0"><p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">{{ $label }}</p><p class="mt-1 break-all font-mono text-sm font-bold text-slate-800">{{ $value }}</p></div><button type="button" data-copy="{{ $value }}" class="copy-button flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white text-slate-500 shadow-sm hover:text-teal-700"><i class="far fa-copy"></i></button></div>@endforeach<a href="{{ $vpn->link }}" target="_blank" rel="noopener noreferrer" class="ux-secondary-button w-full"><i class="fas fa-arrow-up-right-from-square"></i> Open VPN Access Link</a><div class="flex flex-wrap justify-between gap-2 border-t border-slate-100 pt-3 text-xs text-slate-400"><span>Purchased {{ ($vpn->purchased_at ?? $vpn->updated_at)->format('d M Y, h:i A') }}</span><span class="font-bold text-slate-600">MVR {{ number_format((float) $vpn->payment, 2) }}</span></div></div></article>
        @empty
            <div class="ux-card lg:col-span-2 p-10 text-center"><i class="fas fa-key text-3xl text-teal-600"></i><h3 class="mt-3 font-extrabold text-slate-900">No VPN purchases yet</h3><a href="{{ route('app.vpn') }}" class="ux-primary-button mt-5">Browse VPN Accounts</a></div>
        @endforelse
    </div>
    @push('scripts')<script>document.querySelectorAll('.copy-button').forEach((button) => button.addEventListener('click', async () => { try { await navigator.clipboard.writeText(button.dataset.copy); toastr.success('Copied to clipboard'); } catch (error) { toastr.error('Unable to copy'); } }));</script>@endpush
</x-frontend-layout>
