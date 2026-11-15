@php
    $rechargeRate = (float) ($app?->mvr_to_bdt ?: 7.98);
@endphp

<x-frontend-layout>
    <x-slot:metaTitle>BD Recharge · EasyXpres</x-slot:metaTitle>
    <x-slot:header>
        <p class="text-[10px] font-bold uppercase tracking-[.18em] text-teal-100">Bangladesh</p>
        <h1 class="truncate text-lg font-extrabold text-white">BD Recharge</h1>
    </x-slot:header>

    <div class="mx-auto max-w-5xl">
        <div class="mb-5 flex items-start justify-between gap-3 sm:mb-6 sm:items-end">
            <div class="min-w-0">
                <span class="ux-eyebrow">Mobile service</span>
                <h2 class="ux-title mt-1">Recharge a BD number</h2>
                <p class="ux-muted mt-2 max-w-2xl text-sm">Enter the mobile number and amount to submit a secure recharge request.</p>
            </div>
            <a href="{{ route('app.recharge.history') }}" class="ux-secondary-button shrink-0 px-3 sm:px-5" data-testid="bd-recharge-history-link">
                <i class="fas fa-clock-rotate-left"></i>
                <span class="hidden sm:inline">BD Recharge History</span>
                <span class="sm:hidden">History</span>
            </a>
        </div>

        @if ($errors->any())
            <div class="mt-5 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700" role="alert">
                <div class="flex items-start gap-3">
                    <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-red-100"><i class="fas fa-circle-exclamation"></i></span>
                    <div>
                        <p class="font-extrabold">Please check your recharge details</p>
                        <ul class="mt-1 list-disc space-y-1 pl-4">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <div class="mt-5 grid items-start gap-5 lg:grid-cols-[minmax(0,1.5fr)_minmax(280px,.75fr)]">
            <section class="ux-card overflow-hidden" aria-labelledby="recharge-form-title">
                <div class="border-b border-slate-100 px-5 py-5 sm:px-7 sm:py-6">
                    <div class="flex items-center gap-4">
                        <span class="ux-icon-tile ux-icon-tile--blue"><i class="fas fa-phone-volume"></i></span>
                        <div>
                            <span class="ux-eyebrow">Recharge details</span>
                            <h2 id="recharge-form-title" class="mt-1 text-xl font-extrabold tracking-tight text-slate-900">Complete your request</h2>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('app.recharge.submit') }}" class="space-y-5 p-5 sm:p-7" data-recharge-form>
                    @csrf

                    <div>
                        <label for="recharge_mobile" class="ux-field-label">Bangladesh mobile number</label>
                        <div class="relative">
                            <span class="ux-input-icon"><i class="fas fa-mobile-screen-button"></i></span>
                            <input
                                id="recharge_mobile"
                                type="text"
                                name="mobile"
                                value="{{ old('mobile') }}"
                                required
                                maxlength="15"
                                inputmode="tel"
                                autocomplete="tel-national"
                                placeholder="01XXXXXXXXX"
                                class="ux-input-with-icon pr-28"
                                aria-describedby="operator-detection-note"
                                data-mobile-input
                            >
                            <span class="absolute inset-y-0 right-3 flex items-center">
                                <span class="rounded-lg bg-slate-100 px-2 py-1 text-[10px] font-extrabold text-slate-500" data-operator-label aria-live="polite">Operator</span>
                            </span>
                        </div>
                        <p id="operator-detection-note" class="mt-2 text-[11px] font-medium text-slate-400">Operator is detected automatically from the number prefix.</p>
                        @error('mobile') <p class="ux-error"><i class="fas fa-circle-exclamation mt-0.5"></i>{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <div class="mb-2 flex items-center justify-between gap-3">
                            <label for="recharge_mvr" class="ux-field-label mb-0">Recharge amount</label>
                            <span class="text-[11px] font-semibold text-slate-400">Minimum MVR 100</span>
                        </div>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex w-16 items-center justify-center rounded-l-2xl border-r border-teal-100 bg-teal-50 text-xs font-extrabold text-teal-700">MVR</span>
                                <input id="recharge_mvr" type="number" name="amount" value="{{ old('amount') }}" required min="100" step="0.01" inputmode="decimal" autocomplete="off" placeholder="0.00" class="ux-input pl-20 text-lg font-extrabold" data-mvr-input>
                            </div>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex w-16 items-center justify-center rounded-l-2xl border-r border-emerald-100 bg-emerald-50 text-xs font-extrabold text-emerald-700">BDT</span>
                                <input id="recharge_bdt" type="number" min="{{ number_format($rechargeRate, 2, '.', '') }}" step="0.01" inputmode="decimal" autocomplete="off" placeholder="0.00" class="ux-input pl-20 text-lg font-extrabold" aria-label="Estimated BDT amount" data-bdt-input>
                            </div>
                        </div>
                        <div class="mt-2 flex flex-wrap items-center justify-between gap-2">
                            <p class="text-[11px] font-medium text-slate-400">BDT amount is an estimate using the current rate.</p>
                            <div class="flex flex-wrap gap-1.5" aria-label="Quick recharge amounts">
                                @foreach ([100, 200, 500] as $amount)
                                    <button type="button" class="balance-quick-amount" data-quick-recharge="{{ $amount }}">+{{ number_format($amount) }}</button>
                                @endforeach
                            </div>
                        </div>
                        @error('amount') <p class="ux-error"><i class="fas fa-circle-exclamation mt-0.5"></i>{{ $message }}</p> @enderror
                    </div>

                    <div class="rounded-2xl border border-amber-100 bg-amber-50 p-4 text-xs leading-5 text-amber-800">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-circle-info mt-0.5 text-amber-600"></i>
                            <p><strong>Before you continue:</strong> check the mobile number carefully. The MVR amount will be deducted when the request is submitted.</p>
                        </div>
                    </div>

                    <button type="submit" class="ux-primary-button w-full" data-recharge-submit>
                        <i class="fas fa-bolt"></i> Confirm BD Recharge
                    </button>
                </form>
            </section>

            <aside class="space-y-4" aria-label="Recharge information">
                <section class="ux-card p-5 sm:p-6">
                    <span class="ux-eyebrow">How it works</span>
                    <ol class="mt-5 space-y-5">
                        @foreach ([
                            ['fa-mobile-screen-button', 'Enter number', 'Provide the destination BD mobile number.'],
                            ['fa-calculator', 'Set amount', 'Use MVR or the BDT estimate to set the value.'],
                            ['fa-list-check', 'Track status', 'Follow the request from recharge history.'],
                        ] as $index => [$icon, $title, $copy])
                            <li class="flex gap-3">
                                <span class="relative flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-teal-50 text-sm text-teal-700">
                                    <i class="fas {{ $icon }}"></i>
                                    <span class="absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full border-2 border-white bg-teal-700 text-[9px] font-extrabold text-white">{{ $index + 1 }}</span>
                                </span>
                                <div>
                                    <h3 class="text-sm font-extrabold text-slate-800">{{ $title }}</h3>
                                    <p class="mt-1 text-xs leading-5 text-slate-500">{{ $copy }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                </section>

                <section class="rounded-3xl border border-sky-100 bg-gradient-to-br from-sky-50 to-cyan-50 p-5 sm:p-6">
                    <div class="flex items-start gap-3">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-white text-sky-600 shadow-sm"><i class="fas fa-headset"></i></span>
                        <div>
                            <h2 class="text-sm font-extrabold text-slate-800">Need help?</h2>
                            <p class="mt-1 text-xs leading-5 text-slate-500">Review your history first, then contact support if a request needs attention.</p>
                        </div>
                    </div>
                </section>
            </aside>
        </div>
    </div>

    @push('scripts')
        <script>
            (() => {
                const rate = @json($rechargeRate);
                const mobileInput = document.querySelector('[data-mobile-input]');
                const operatorLabel = document.querySelector('[data-operator-label]');
                const mvrInput = document.querySelector('[data-mvr-input]');
                const bdtInput = document.querySelector('[data-bdt-input]');
                const form = document.querySelector('[data-recharge-form]');

                const detectOperator = (value) => {
                    let number = String(value || '').replace(/\D/g, '');
                    if (number.startsWith('880')) number = `0${number.slice(3)}`;
                    else if (number.length === 10 && number.startsWith('1')) number = `0${number}`;

                    return {
                        '013': 'Grameenphone',
                        '014': 'Banglalink',
                        '015': 'Teletalk',
                        '016': 'Airtel',
                        '017': 'Grameenphone',
                        '018': 'Robi',
                        '019': 'Banglalink',
                    }[number.slice(0, 3)] || 'Operator';
                };

                const updateOperator = () => {
                    if (operatorLabel) operatorLabel.textContent = detectOperator(mobileInput?.value);
                };

                const convertFromMvr = () => {
                    const amount = Number.parseFloat(mvrInput?.value);
                    if (bdtInput) bdtInput.value = Number.isFinite(amount) ? (amount * rate).toFixed(2) : '';
                };

                const convertFromBdt = () => {
                    const amount = Number.parseFloat(bdtInput?.value);
                    if (mvrInput) mvrInput.value = Number.isFinite(amount) && rate > 0 ? (amount / rate).toFixed(2) : '';
                };

                mobileInput?.addEventListener('input', updateOperator);
                mvrInput?.addEventListener('input', convertFromMvr);
                bdtInput?.addEventListener('input', convertFromBdt);

                document.querySelectorAll('[data-quick-recharge]').forEach((button) => {
                    button.addEventListener('click', () => {
                        if (!mvrInput) return;
                        mvrInput.value = button.dataset.quickRecharge || '';
                        convertFromMvr();
                        mvrInput.focus();
                    });
                });

                form?.addEventListener('submit', () => {
                    const submitButton = form.querySelector('[data-recharge-submit]');
                    if (!submitButton || submitButton.disabled) return;
                    submitButton.disabled = true;
                    submitButton.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Processing...';
                });

                updateOperator();
                if (mvrInput?.value) convertFromMvr();
            })();
        </script>
    @endpush
</x-frontend-layout>
