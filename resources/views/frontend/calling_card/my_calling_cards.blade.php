<x-frontend-layout>
    <x-slot:metaTitle>My Calling Cards · EasyXpres</x-slot:metaTitle>
    <x-slot:header><p class="text-[10px] font-bold uppercase tracking-[.18em] text-teal-100">Voice services</p><h1 class="truncate text-lg font-extrabold text-white">My Calling Cards</h1></x-slot:header>

    <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-end"><div><span class="ux-eyebrow">Purchased cards</span><h2 class="ux-title mt-1">Your credentials</h2><p class="ux-muted mt-2 text-sm">Copy the details you need and keep them private.</p></div><a href="{{ route('app.calling.card') }}" class="ux-primary-button"><i class="fas fa-plus"></i> Buy Card</a></div>
    <div class="grid gap-5 md:grid-cols-2">
        @forelse ($callingCards as $callingCard)
            <article class="overflow-hidden rounded-[2rem] bg-gradient-to-br from-[#005e68] via-[#008b83] to-[#22c7ae] text-white shadow-xl">
                <div class="flex items-center justify-between gap-3 border-b border-white/10 px-5 py-4"><div><p class="text-[10px] font-bold uppercase tracking-[.18em] text-teal-50/70">Calling Card</p><p class="mt-1 text-xs font-semibold">Purchased {{ $callingCard->updated_at?->format('d M Y, h:i A') }}</p></div><i class="fas fa-phone-volume text-xl"></i></div>
                <div class="space-y-3 p-5">
                    @foreach ([['Operator Code', $callingCard->card_no], ['Username', $callingCard->user_name], ['Password', $callingCard->pin]] as [$label, $value])
                        <div class="flex items-center justify-between gap-3 rounded-2xl bg-white/10 p-3"><div class="min-w-0"><p class="text-[10px] font-bold uppercase tracking-wider text-teal-50/70">{{ $label }}</p><p class="mt-1 break-all font-mono text-sm font-bold">{{ $value }}</p></div><button type="button" class="copy-card-value flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white/10 hover:bg-white/20" data-copy-value="{{ $value }}" aria-label="Copy {{ $label }}"><i class="far fa-copy"></i></button></div>
                    @endforeach
                    <div class="grid grid-cols-2 gap-3 pt-1"><div class="rounded-2xl bg-white/10 p-3 text-center"><p class="text-[10px] uppercase text-teal-50/70">Point</p><p class="mt-1 font-extrabold">{{ $callingCard->point }}</p></div><div class="rounded-2xl bg-white/10 p-3 text-center"><p class="text-[10px] uppercase text-teal-50/70">Min:Sec</p><p class="mt-1 font-extrabold">{{ $callingCard->minute_sec }}</p></div></div>
                </div>
            </article>
        @empty
            <div class="ux-card col-span-full p-10 text-center"><i class="fas fa-credit-card text-3xl text-teal-600"></i><h3 class="mt-3 font-extrabold text-slate-900">No calling cards yet</h3><a href="{{ route('app.calling.card') }}" class="ux-primary-button mt-5">Browse Cards</a></div>
        @endforelse
    </div>

    @push('scripts')
        <script>
            document.querySelectorAll('.copy-card-value').forEach((button) => button.addEventListener('click', async () => {
                try { await navigator.clipboard.writeText(button.dataset.copyValue); toastr.success('Copied to clipboard'); }
                catch (error) { toastr.error('Unable to copy'); }
            }));
        </script>
    @endpush
</x-frontend-layout>
