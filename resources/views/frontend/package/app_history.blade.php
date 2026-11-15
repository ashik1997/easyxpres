<x-frontend-layout>
    <x-slot:metaTitle>Packages · EasyXpres</x-slot:metaTitle>
    <x-slot:header><p class="text-[10px] font-bold uppercase tracking-[.18em] text-teal-100">Explore</p><h1 class="truncate text-lg font-extrabold text-white">Packages</h1></x-slot:header>

    @php $packages = \App\Models\Package::where('status', 'active')->get(); @endphp

    <div class="mb-6 flex items-start justify-between gap-3 sm:items-end">
        <div class="min-w-0"><span class="ux-eyebrow">Active packages</span><h2 class="ux-title mt-1">Find your package</h2><p class="ux-muted mt-2 text-sm">Choose a service to view its current offers.</p></div>
        <a href="{{ route('app.recharge.packages.history') }}" class="ux-secondary-button shrink-0 px-3 sm:px-5" data-testid="package-history-link">
            <i class="fas fa-clock-rotate-left"></i>
            <span class="hidden sm:inline">Package History</span>
            <span class="sm:hidden">History</span>
        </a>
    </div>
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($packages as $package)
            <a href="{{ route('app.package.show', $package->id) }}" class="ux-card-interactive group flex items-center gap-4 p-4">
                <div class="ux-icon-tile h-16 w-16">
                    @if ($package->image_icon)<img src="{{ asset('storage/' . $package->image_icon) }}" alt="" class="h-12 w-12 rounded-xl object-contain">@else<i class="fas fa-cube text-xl"></i>@endif
                </div>
                <div class="min-w-0 flex-1"><span class="ux-status ux-status--active">Active</span><h3 class="mt-2 truncate text-lg font-extrabold text-slate-900">{{ $package->name }}</h3>@if ($package->description)<p class="mt-1 line-clamp-2 text-xs leading-5 text-slate-500">{{ Str::limit($package->description, 90) }}</p>@endif</div>
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-teal-50 text-teal-700 transition group-hover:bg-teal-600 group-hover:text-white"><i class="fas fa-chevron-right text-xs"></i></span>
            </a>
        @empty
            <div class="ux-card col-span-full p-10 text-center"><i class="fas fa-box-open text-3xl text-teal-600"></i><h3 class="mt-3 font-extrabold text-slate-900">No packages available</h3></div>
        @endforelse
    </div>
</x-frontend-layout>
