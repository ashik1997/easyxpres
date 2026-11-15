<x-frontend-layout>
    <x-slot:metaTitle>Balance History · EasyXpres</x-slot:metaTitle>
    <x-slot:header><p class="text-[10px] font-bold uppercase tracking-[.18em] text-teal-100">Wallet</p><h1 class="truncate text-lg font-extrabold text-white">Balance History</h1></x-slot:header>

    <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-end"><div><span class="ux-eyebrow">Transactions</span><h2 class="ux-title mt-1">Deposit history</h2><p class="ux-muted mt-2 text-sm">All manual and approved balance requests.</p></div><a href="{{ route('balance.add.form') }}" class="ux-primary-button"><i class="fas fa-plus text-xs"></i> Add Balance</a></div>
    <div class="space-y-4">
        @forelse (Auth::user()->account->transactions->sortByDesc('id') as $transaction)
            <article class="ux-card p-4 sm:p-5"><div class="flex items-start gap-4"><span class="ux-icon-tile {{ $transaction->status === 'approved' ? '' : 'ux-icon-tile--orange' }}"><i class="fas {{ $transaction->status === 'approved' ? 'fa-arrow-down' : 'fa-clock' }}"></i></span><div class="min-w-0 flex-1"><div class="flex flex-wrap items-start justify-between gap-2"><div><h3 class="font-extrabold text-slate-900">Add Balance</h3><p class="mt-1 break-all text-xs text-slate-500">{{ $transaction->transaction_id ?: 'Manual request' }}</p></div><div class="text-right"><p class="text-lg font-extrabold text-teal-700">+ MVR {{ number_format((float) $transaction->amount, 2) }}</p><x-user-status :status="$transaction->status" class="mt-2" /></div></div><p class="mt-4 border-t border-slate-100 pt-3 text-xs font-medium text-slate-400"><i class="far fa-calendar mr-1"></i>{{ $transaction->created_at->format('d M Y, h:i A') }}</p></div></div></article>
        @empty
            <div class="ux-card p-10 text-center"><i class="fas fa-wallet text-3xl text-teal-600"></i><h3 class="mt-3 font-extrabold text-slate-900">No balance history</h3><p class="mt-1 text-sm text-slate-500">Your balance requests will appear here.</p></div>
        @endforelse
    </div>
</x-frontend-layout>
