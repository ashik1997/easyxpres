<x-frontend-layout>
    <x-slot:metaTitle>BD Recharge History · EasyXpres</x-slot:metaTitle>
    <x-slot:header>
        <p class="text-[10px] font-bold uppercase tracking-[.18em] text-teal-100">Bangladesh</p>
        <h1 class="truncate text-lg font-extrabold text-white">Recharge History</h1>
    </x-slot:header>

    <div class="mx-auto max-w-5xl">
        <div class="mb-5 flex items-start justify-between gap-3 sm:mb-6 sm:items-end">
            <div class="min-w-0">
                <span class="ux-eyebrow">Mobile recharge</span>
                <h2 class="ux-title mt-1">BD Recharge History</h2>
                <p id="recharge-history-description" class="ux-muted mt-2 max-w-2xl text-sm">Track number, operator, amount and approval status for every request.</p>
            </div>
            <a href="{{ route('app.recharge.form') }}" class="ux-primary-button shrink-0 px-3 sm:px-5">
                <i class="fas fa-plus text-xs"></i>
                <span class="hidden sm:inline">New BD Recharge</span>
                <span class="sm:hidden">New</span>
            </a>
        </div>

        @if ($recharges->isNotEmpty())
            <div class="recharge-history-wrap ux-table-wrap">
                <table class="ux-table recharge-history-table" aria-describedby="recharge-history-description">
                    <thead>
                        <tr>
                            <th scope="col">Number</th>
                            <th scope="col">Operator</th>
                            <th scope="col">Amount</th>
                            <th scope="col">Status</th>
                            <th scope="col">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recharges as $recharge)
                            <tr>
                                <td data-label="Number">
                                    <div class="flex items-center gap-3">
                                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-600"><i class="fas fa-mobile-screen-button"></i></span>
                                        <span class="font-extrabold text-slate-900">{{ $recharge->mobile }}</span>
                                    </div>
                                </td>
                                <td data-label="Operator">
                                    <span class="inline-flex items-center gap-2 font-semibold text-slate-700">
                                        <span class="h-2 w-2 rounded-full {{ $recharge->operator_name === 'Unknown' ? 'bg-slate-300' : 'bg-teal-500' }}"></span>
                                        {{ $recharge->operator_name }}
                                    </span>
                                </td>
                                <td data-label="Amount">
                                    <span class="font-extrabold text-teal-700">MVR {{ number_format((float) $recharge->amount, 2) }}</span>
                                </td>
                                <td data-label="Status"><x-user-status :status="$recharge->status" /></td>
                                <td data-label="Date">
                                    <time datetime="{{ $recharge->created_at->toIso8601String() }}" class="whitespace-nowrap text-sm font-medium text-slate-600">
                                        {{ $recharge->created_at->format('d M Y') }}
                                        <span class="mt-0.5 block text-xs font-normal text-slate-400">{{ $recharge->created_at->format('h:i A') }}</span>
                                    </time>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p class="mt-3 text-[11px] font-medium text-slate-400"><i class="fas fa-circle-info mr-1"></i>Operator is identified from the number prefix; ported numbers may differ.</p>
        @else
            <div class="ux-card p-10 text-center sm:p-14">
                <span class="mx-auto flex h-16 w-16 items-center justify-center rounded-3xl bg-teal-50 text-2xl text-teal-600"><i class="fas fa-mobile-screen-button"></i></span>
                <h3 class="mt-4 text-lg font-extrabold text-slate-900">No recharge history</h3>
                <p class="mx-auto mt-2 max-w-sm text-sm leading-6 text-slate-500">Your BD recharge requests will appear here after the first submission.</p>
                <a href="{{ route('app.recharge.form') }}" class="ux-primary-button mt-5"><i class="fas fa-plus text-xs"></i> Make a Recharge</a>
            </div>
        @endif
    </div>
</x-frontend-layout>
