<x-frontend-layout>
    <x-slot:metaTitle>Mobile Banking · EasyXpres</x-slot:metaTitle>
    <x-slot:header><p class="text-[10px] font-bold uppercase tracking-[.18em] text-teal-100">Transfer money</p><h1 class="truncate text-lg font-extrabold text-white">Mobile Banking</h1></x-slot:header>

    @php $mobileBankings = \App\Models\MobileBanking::where('status', 'active')->get(); @endphp

    <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-end"><div><span class="ux-eyebrow">Available providers</span><h2 class="ux-title mt-1">Choose a service</h2><p class="ux-muted mt-2 text-sm">Compare the current rate before starting your request.</p></div><a href="{{ route('app.mobile.banking.history') }}" class="ux-secondary-button"><i class="fas fa-history"></i> View History</a></div>
    <div class="grid gap-4 md:grid-cols-2">
        @forelse ($mobileBankings as $banking)
            <article class="ux-card-interactive flex items-center gap-4 p-4 sm:p-5">
                <div class="ux-icon-tile h-16 w-16">@if ($banking->image_icon)<img src="{{ asset('storage/' . $banking->image_icon) }}" alt="" class="h-12 w-12 rounded-xl object-contain">@else<i class="fas fa-money-bill-transfer"></i>@endif</div>
                <div class="min-w-0 flex-1"><h3 class="truncate text-lg font-extrabold text-slate-900">{{ $banking->name }}</h3><p class="mt-1 text-xs font-semibold text-slate-500">1 MVR = <span class="text-teal-700">{{ number_format((float) $banking->rate, 2) }} BDT</span></p></div>
                <button type="button" class="mobile-banking-open ux-primary-button px-4" data-banking-id="{{ $banking->id }}" data-banking-rate="{{ $banking->rate }}">Use <i class="fas fa-chevron-right text-xs"></i></button>
            </article>
        @empty
            <div class="ux-card col-span-full p-10 text-center"><i class="fas fa-building-columns text-3xl text-teal-600"></i><h3 class="mt-3 font-extrabold text-slate-900">No service available</h3></div>
        @endforelse
    </div>

    <div id="mobileBankingModal" class="ux-modal-backdrop hidden" role="dialog" aria-modal="true" aria-labelledby="mobileBankingModalTitle">
        <div class="ux-modal-panel max-w-md">
            <div class="ux-modal-header"><div><p class="text-xs font-semibold text-teal-100">New request</p><h2 id="mobileBankingModalTitle" class="text-lg font-extrabold">Mobile Banking</h2></div><button type="button" id="closeMobileBankingModal" class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/10"><i class="fas fa-times"></i></button></div>
            <form id="mobileBankingForm" method="POST" action="{{ route('mobile.banking.store') }}" class="space-y-4 p-5 sm:p-6">
                @csrf
                <input type="hidden" name="mobile_banking_id" id="mobile_banking_id">
                <div class="ux-notice">Conversion rate: <strong id="mobileBankingRate">0</strong> BDT per MVR</div>
                <div><label for="mobileBankingNumber" class="ux-field-label">Mobile number</label><input id="mobileBankingNumber" type="text" name="number" required class="ux-input" placeholder="Enter destination number"></div>
                <div><label class="ux-field-label">Amount</label><div class="grid grid-cols-2 gap-3"><div class="relative"><span class="absolute inset-y-0 left-0 flex w-14 items-center justify-center rounded-l-2xl bg-teal-50 text-xs font-extrabold text-teal-700">MVR</span><input type="number" name="amount" id="mobileBankingMvr" required min="1" step="0.01" class="ux-input pl-16" oninput="convertMobileBanking('mvr')"></div><div class="relative"><span class="absolute inset-y-0 left-0 flex w-14 items-center justify-center rounded-l-2xl bg-emerald-50 text-xs font-extrabold text-emerald-700">BDT</span><input type="number" name="rate_calculation" id="mobileBankingBdt" required min="500" step="0.01" class="ux-input pl-16" oninput="convertMobileBanking('bdt')"></div></div><p class="mt-2 text-xs text-slate-500">Minimum converted amount: 500 BDT</p></div>
                <fieldset><legend class="ux-field-label">Account type</legend><div class="grid grid-cols-2 gap-3"><label class="flex cursor-pointer items-center gap-3 rounded-2xl border border-slate-200 p-3 text-sm font-bold text-slate-700"><input type="radio" name="money_status" value="personal" checked class="text-teal-600 focus:ring-teal-500"> Personal</label><label class="flex cursor-pointer items-center gap-3 rounded-2xl border border-slate-200 p-3 text-sm font-bold text-slate-700"><input type="radio" name="money_status" value="agent" class="text-teal-600 focus:ring-teal-500"> Agent</label></div></fieldset>
                <div class="flex justify-end gap-3"><button type="button" id="cancelMobileBankingModal" class="ux-secondary-button">Cancel</button><button type="submit" id="submitBtn" class="ux-primary-button">Confirm Request</button></div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            let currentRate = 0;
            const mobileBankingModal = document.getElementById('mobileBankingModal');
            function openMobileBankingModal(id, rate) {
                currentRate = parseFloat(rate) || 0;
                document.getElementById('mobile_banking_id').value = id;
                document.getElementById('mobileBankingRate').textContent = currentRate.toFixed(2);
                document.getElementById('mobileBankingMvr').value = '';
                document.getElementById('mobileBankingBdt').value = '';
                mobileBankingModal.classList.remove('hidden'); document.body.style.overflow = 'hidden';
            }
            function closeMobileBankingModal() { mobileBankingModal.classList.add('hidden'); document.body.style.overflow = ''; }
            function convertMobileBanking(type) {
                const mvr = document.getElementById('mobileBankingMvr'); const bdt = document.getElementById('mobileBankingBdt');
                if (currentRate <= 0) { bdt.value = ''; return; }
                if (type === 'mvr') bdt.value = mvr.value === '' ? '' : (parseFloat(mvr.value) * currentRate).toFixed(2);
                else mvr.value = bdt.value === '' ? '' : (parseFloat(bdt.value) / currentRate).toFixed(2);
            }
            document.querySelectorAll('.mobile-banking-open').forEach((button) => button.addEventListener('click', () => openMobileBankingModal(button.dataset.bankingId, button.dataset.bankingRate)));
            document.getElementById('closeMobileBankingModal').addEventListener('click', closeMobileBankingModal);
            document.getElementById('cancelMobileBankingModal').addEventListener('click', closeMobileBankingModal);
            mobileBankingModal.addEventListener('click', (event) => { if (event.target === mobileBankingModal) closeMobileBankingModal(); });
            document.getElementById('mobileBankingForm').addEventListener('submit', () => { const button = document.getElementById('submitBtn'); button.disabled = true; button.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Processing...'; });
        </script>
    @endpush
</x-frontend-layout>
