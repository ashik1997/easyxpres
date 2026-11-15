<x-frontend-layout>
    <x-slot:metaTitle>Calling Cards · EasyXpres</x-slot:metaTitle>
    <x-slot:header><p class="text-[10px] font-bold uppercase tracking-[.18em] text-teal-100">Voice services</p><h1 class="truncate text-lg font-extrabold text-white">Calling Cards</h1></x-slot:header>

    <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-end"><div><span class="ux-eyebrow">Available cards</span><h2 class="ux-title mt-1">Choose a calling card</h2><p class="ux-muted mt-2 text-sm">Credentials stay hidden until your purchase is confirmed.</p></div><a href="{{ route('my.calling.card') }}" class="ux-secondary-button" data-testid="calling-card-history-link"><i class="fas fa-clock-rotate-left"></i> Card History</a></div>

    <input type="hidden" id="cardLink" value="{{ $appSetup->link ?? '' }}">
    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
        @forelse ($callingCards as $callingCard)
            <article class="relative overflow-hidden rounded-[2rem] bg-gradient-to-br from-[#005e68] via-[#008b83] to-[#22c7ae] p-5 text-white shadow-xl">
                <div class="absolute -right-12 -top-12 h-40 w-40 rounded-full border border-white/15 shadow-[0_0_0_28px_rgba(255,255,255,.05)]"></div>
                <div class="relative z-10 flex items-center justify-between gap-3">
                    <div class="flex h-11 w-11 items-center justify-center overflow-hidden rounded-2xl bg-white/15">
                        @if (!empty($appSetup?->icon))<img src="{{ asset('storage/' . $appSetup->icon) }}" alt="" class="h-9 w-9 rounded-xl object-cover">@else<i class="fas fa-phone"></i>@endif
                    </div>
                    @if (!empty($appSetup?->link))<button type="button" onclick="openInputLink()" class="rounded-xl border border-white/20 bg-white/10 px-3 py-2 text-xs font-bold hover:bg-white/20"><i class="fas fa-arrow-up-right-from-square mr-1"></i> Install App</button>@endif
                </div>
                <div class="relative z-10 mt-8"><p class="text-[10px] font-bold uppercase tracking-[.18em] text-teal-50/70">Operator code</p><p class="mt-2 font-mono text-xl font-bold tracking-[.2em]">{{ str_repeat('*', strlen($callingCard->card_no ?? '')) ?: '••••••••' }}</p></div>
                <div class="relative z-10 mt-6 grid grid-cols-2 gap-4"><div><p class="text-[10px] font-bold uppercase tracking-wider text-teal-50/70">Username</p><p class="mt-1 font-bold">{{ str_repeat('*', strlen($callingCard->user_name ?? '')) ?: '••••' }}</p></div><div class="text-right"><p class="text-[10px] font-bold uppercase tracking-wider text-teal-50/70">Password</p><p class="mt-1 font-bold">{{ str_repeat('*', strlen($callingCard->pin ?? '')) ?: '••••' }}</p></div></div>
                <div class="relative z-10 mt-6 grid grid-cols-3 gap-2 rounded-2xl bg-white/10 p-3 text-center"><div><p class="text-[9px] uppercase text-teal-50/70">Price</p><p class="mt-1 text-sm font-extrabold">MVR {{ number_format((float) $callingCard->payment, 2) }}</p></div><div><p class="text-[9px] uppercase text-teal-50/70">Point</p><p class="mt-1 text-sm font-extrabold">{{ $callingCard->point }}</p></div><div><p class="text-[9px] uppercase text-teal-50/70">Min:Sec</p><p class="mt-1 text-sm font-extrabold">{{ $callingCard->minute_sec }}</p></div></div>
                <form action="{{ route('calling.card.buy') }}" method="POST" class="relative z-10 mt-5" onsubmit="return confirm('Buy this calling card for MVR {{ number_format((float) $callingCard->payment, 2) }}?')">
                    @csrf
                    <input type="hidden" name="id" value="{{ $callingCard->id }}"><input type="hidden" name="user_id" value="{{ Auth::id() }}">
                    <button type="submit" class="w-full rounded-2xl bg-white px-4 py-3 text-sm font-extrabold text-teal-700 shadow-lg transition hover:-translate-y-0.5">Buy Now <i class="fas fa-arrow-right ml-1 text-xs"></i></button>
                </form>
            </article>
        @empty
            <div class="ux-card col-span-full p-10 text-center"><i class="fas fa-phone text-3xl text-teal-600"></i><h3 class="mt-3 font-extrabold text-slate-900">No calling cards available</h3></div>
        @endforelse
    </div>
</x-frontend-layout>
