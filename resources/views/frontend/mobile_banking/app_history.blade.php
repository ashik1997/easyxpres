<x-frontend-layout>
    <x-slot:metaTitle>Mobile Banking History · EasyXpres</x-slot:metaTitle>
    <x-slot:header><p class="text-[10px] font-bold uppercase tracking-[.18em] text-teal-100">Mobile banking</p><h1 class="truncate text-lg font-extrabold text-white">Transfer History</h1></x-slot:header>

    <div class="mb-6"><span class="ux-eyebrow">Transfer activity</span><h2 class="ux-title mt-1">Mobile banking requests</h2><p class="ux-muted mt-2 text-sm">Review provider, destination, amount and notes.</p></div>
    <div class="space-y-4">
        @forelse ($packages as $package)
            @php $mobileDetail = DB::table('mobile_bankings')->where('id', $package->mobile_banking_id)->first(); @endphp
            <article class="ux-card p-4 sm:p-5"><div class="flex items-start gap-4"><div class="ux-icon-tile h-14 w-14">@if ($mobileDetail?->image_icon)<img src="{{ asset('storage/' . $mobileDetail->image_icon) }}" alt="" class="h-10 w-10 rounded-xl object-contain">@else<i class="fas fa-money-bill-transfer"></i>@endif</div><div class="min-w-0 flex-1"><div class="flex flex-wrap items-start justify-between gap-2"><div><p class="text-xs text-slate-400">{{ $package->created_at->format('d M Y, h:i A') }}</p><h3 class="mt-1 font-extrabold text-slate-900">{{ $mobileDetail->name ?? 'Mobile banking' }}</h3><p class="mt-1 text-xs font-medium text-slate-500">{{ ucfirst($package->money_status) }} · {{ $package->number }}</p></div><x-user-status :status="$package->status" /></div><div class="mt-4 grid grid-cols-2 gap-3 rounded-2xl bg-slate-50 p-3"><div><p class="text-xs text-slate-500">MVR amount</p><p class="mt-1 font-extrabold text-teal-700">{{ number_format((float) $package->amount, 2) }}</p></div><div><p class="text-xs text-slate-500">BDT amount</p><p class="mt-1 font-extrabold text-emerald-700">{{ number_format((float) $package->bdt_amount, 2) }}</p></div></div>@if (!empty($package->note_admin))<div class="mt-3 rounded-2xl bg-amber-50 p-3 text-xs leading-5 text-amber-800"><strong>Note:</strong> {{ $package->note_admin }}</div>@endif</div></div></article>
        @empty
            <div class="ux-card p-10 text-center"><i class="fas fa-money-bill-transfer text-3xl text-teal-600"></i><h3 class="mt-3 font-extrabold text-slate-900">No mobile banking history</h3></div>
        @endforelse
    </div>
</x-frontend-layout>
