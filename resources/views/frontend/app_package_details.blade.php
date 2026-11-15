<x-frontend-layout>
    <x-slot:metaTitle>Package Details · EasyXpres</x-slot:metaTitle>
    <x-slot:header><p class="text-[10px] font-bold uppercase tracking-[.18em] text-teal-100">Packages</p><h1 class="truncate text-lg font-extrabold text-white">Choose an offer</h1></x-slot:header>

    <div class="mb-6 flex flex-col justify-between gap-3 sm:flex-row sm:items-end">
        <div><span class="ux-eyebrow">Available offers</span><h2 class="ux-title mt-1">Package details</h2><p class="ux-muted mt-2 text-sm">Select the best option for your number.</p></div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('app.package.history') }}" class="ux-secondary-button"><i class="fas fa-arrow-left text-xs"></i> All Packages</a>
            <a href="{{ route('app.recharge.packages.history') }}" class="ux-secondary-button" data-testid="package-detail-history-link"><i class="fas fa-clock-rotate-left"></i> History</a>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @forelse ($subpackages as $subpackage)
            <article class="ux-card-interactive flex flex-col overflow-hidden p-5">
                <div class="flex items-center gap-4">
                    <div class="ux-icon-tile h-14 w-14">
                        @if ($subpackage->package?->image_icon)
                            <img src="{{ asset('storage/' . $subpackage->package->image_icon) }}" alt="" class="h-10 w-10 rounded-xl object-contain">
                        @else
                            <i class="fas fa-wifi"></i>
                        @endif
                    </div>
                    <div class="min-w-0"><p class="text-xs font-bold uppercase tracking-wider text-teal-600">Digital package</p><h3 class="mt-1 truncate text-lg font-extrabold text-slate-900">{{ $subpackage->title }}</h3></div>
                </div>
                <dl class="my-5 space-y-3 rounded-2xl bg-slate-50 p-4 text-sm">
                    <div class="flex justify-between gap-3"><dt class="text-slate-500">Base amount</dt><dd class="font-bold text-slate-800">MVR {{ number_format($subpackage->amount, 2) }}</dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-slate-500">Commission</dt><dd class="font-bold text-slate-800">MVR {{ number_format($subpackage->commission, 2) }}</dd></div>
                    <div class="flex justify-between gap-3 border-t border-slate-200 pt-3"><dt class="font-bold text-slate-700">Offer price</dt><dd class="text-lg font-extrabold text-teal-700">MVR {{ number_format($subpackage->offer_price, 2) }}</dd></div>
                </dl>
                <button type="button" class="package-purchase-button ux-primary-button mt-auto w-full" data-package-id="{{ $subpackage->id }}" data-package-title="{{ $subpackage->title }}" data-package-amount="{{ $subpackage->offer_price }}">Continue <i class="fas fa-arrow-right text-xs"></i></button>
            </article>
        @empty
            <div class="ux-card col-span-full p-10 text-center"><div class="mx-auto ux-icon-tile"><i class="fas fa-box-open"></i></div><h3 class="mt-4 font-extrabold text-slate-900">No offers available</h3><p class="mt-1 text-sm text-slate-500">Please check back again later.</p></div>
        @endforelse
    </div>

    <div id="paymentModal" class="ux-modal-backdrop hidden" role="dialog" aria-modal="true" aria-labelledby="modalPackageSubtitle">
        <div class="ux-modal-panel max-w-md">
            <div class="ux-modal-header"><div><p class="text-xs font-semibold text-teal-100">Confirm package</p><h2 id="modalPackageSubtitle" class="text-lg font-extrabold"></h2></div><button type="button" id="closePaymentModal" class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/10" aria-label="Close"><i class="fas fa-times"></i></button></div>
            <form id="paymentForm" method="POST" action="{{ route('payment.store') }}" class="space-y-4 p-5 sm:p-6">
                @csrf
                <input type="hidden" name="package_id" id="modalPackageId">
                <input type="hidden" name="package_title" id="modalPackageTitle">
                <div><label for="package_mobile" class="ux-field-label">Mobile number</label><input id="package_mobile" type="tel" name="mobile" required placeholder="Enter your mobile number" class="ux-input"></div>
                <div><label for="modalAmount" class="ux-field-label">Amount (MVR)</label><input id="modalAmount" type="number" name="amount" readonly required class="ux-input bg-slate-50 font-extrabold text-teal-700"></div>
                <button type="submit" class="ux-primary-button w-full"><i class="fas fa-lock text-xs"></i> Submit Payment</button>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            const packageModal = document.getElementById('paymentModal');
            document.querySelectorAll('.package-purchase-button').forEach((button) => {
                button.addEventListener('click', () => {
                    document.getElementById('modalPackageSubtitle').textContent = button.dataset.packageTitle;
                    document.getElementById('modalPackageTitle').value = button.dataset.packageTitle;
                    document.getElementById('modalAmount').value = button.dataset.packageAmount;
                    document.getElementById('modalPackageId').value = button.dataset.packageId;
                    packageModal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                });
            });
            function hidePackageModal() { packageModal.classList.add('hidden'); document.body.style.overflow = ''; }
            document.getElementById('closePaymentModal').addEventListener('click', hidePackageModal);
            packageModal.addEventListener('click', (event) => { if (event.target === packageModal) hidePackageModal(); });
        </script>
    @endpush
</x-frontend-layout>
