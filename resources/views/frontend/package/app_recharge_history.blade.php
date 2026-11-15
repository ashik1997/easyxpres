<x-frontend-layout>
    <x-slot:metaTitle>Package History · EasyXpres</x-slot:metaTitle>
    <x-slot:header><p class="text-[10px] font-bold uppercase tracking-[.18em] text-teal-100">Transactions</p><h1 class="truncate text-lg font-extrabold text-white">Package History</h1></x-slot:header>

    <div class="mb-6"><span class="ux-eyebrow">Your activity</span><h2 class="ux-title mt-1">Package purchases</h2><p class="ux-muted mt-2 text-sm">Track every package order and its current status.</p></div>
    <div class="space-y-4">
        @forelse ($packages as $package)
            @php
                $packageDetail = DB::table('package_details')->where('id', $package->package_id)->first();
                $pack = $packageDetail ? DB::table('packages')->where('id', $packageDetail->package_id)->first() : null;
            @endphp
            <article class="ux-card p-4 sm:p-5">
                <div class="flex items-start gap-4">
                    <div class="ux-icon-tile h-14 w-14">@if ($pack?->image_icon)<img src="{{ asset('storage/' . $pack->image_icon) }}" alt="" class="h-10 w-10 rounded-xl object-contain">@else<i class="fas fa-box"></i>@endif</div>
                    <div class="min-w-0 flex-1"><div class="flex flex-wrap items-start justify-between gap-2"><div><p class="text-xs font-semibold text-slate-400">{{ \Carbon\Carbon::parse($package->created_at)->format('d M Y, h:i A') }}</p><h3 class="mt-1 font-extrabold text-slate-900">{{ $package->package_name }}</h3></div><x-user-status :status="$package->status" /></div><div class="mt-4 grid grid-cols-2 gap-3 rounded-2xl bg-slate-50 p-3 text-sm"><div><p class="text-xs text-slate-500">Number</p><p class="mt-1 font-bold text-slate-800">{{ $package->number }}</p></div><div><p class="text-xs text-slate-500">Amount</p><p class="mt-1 font-extrabold text-teal-700">MVR {{ number_format($package->amount, 2) }}</p></div></div></div>
                </div>
            </article>
        @empty
            <div class="ux-card p-10 text-center"><i class="fas fa-receipt text-3xl text-teal-600"></i><h3 class="mt-3 font-extrabold text-slate-900">No package history</h3><p class="mt-1 text-sm text-slate-500">Your purchases will appear here.</p></div>
        @endforelse
    </div>
</x-frontend-layout>
