@php
    $operatorOptions = $operators->map(fn ($operator) => [
        'name' => $operator->name,
        'code' => (string) $operator->code,
        'commission' => (float) $operator->commission,
    ])->values();
@endphp

<x-frontend-layout>
    <x-slot:metaTitle>Maldives Recharge · EasyXpres</x-slot:metaTitle>
    <x-slot:header>
        <p class="text-[10px] font-bold uppercase tracking-[.18em] text-teal-100">Maldives</p>
        <h1 class="truncate text-lg font-extrabold text-white">Maldives Recharge</h1>
    </x-slot:header>

    <div class="mx-auto max-w-5xl">
        <div class="mb-5 flex items-start justify-between gap-3 sm:mb-6 sm:items-end">
            <div class="min-w-0">
                <span class="ux-eyebrow">Mobile service</span>
                <h2 class="ux-title mt-1">Recharge a Maldives number</h2>
                <p class="ux-muted mt-2 max-w-2xl text-sm">Submit a secure mobile recharge and track its approval and commission.</p>
            </div>
            <a href="{{ route('app.male.recharge.history') }}" class="ux-secondary-button shrink-0 px-3 sm:px-5" data-testid="maldives-recharge-history-link">
                <i class="fas fa-clock-rotate-left"></i>
                <span class="hidden sm:inline">Maldives Recharge History</span>
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
            <section class="ux-card overflow-hidden" aria-labelledby="maldives-recharge-form-title">
                <div class="border-b border-slate-100 px-5 py-5 sm:px-7 sm:py-6">
                    <div class="flex items-center gap-4">
                        <span class="ux-icon-tile ux-icon-tile--blue"><i class="fas fa-signal"></i></span>
                        <div>
                            <span class="ux-eyebrow">Recharge details</span>
                            <h2 id="maldives-recharge-form-title" class="mt-1 text-xl font-extrabold tracking-tight text-slate-900">Complete your request</h2>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('app.male.recharge.submit') }}" class="space-y-5 p-5 sm:p-7" data-maldives-recharge-form>
                    @csrf

                    <div>
                        <label for="maldives_mobile" class="ux-field-label">Maldives mobile number</label>
                        <div class="relative">
                            <span class="ux-input-icon"><i class="fas fa-mobile-screen-button"></i></span>
                            <input
                                id="maldives_mobile"
                                type="text"
                                name="mobile"
                                value="{{ old('mobile') }}"
                                required
                                maxlength="15"
                                inputmode="tel"
                                autocomplete="tel-national"
                                placeholder="+960 7XXXXXX"
                                class="ux-input-with-icon pr-28"
                                aria-describedby="maldives-operator-note"
                                data-maldives-mobile-input
                            >
                            <span class="absolute inset-y-0 right-3 flex items-center">
                                <span class="rounded-lg bg-slate-100 px-2 py-1 text-[10px] font-extrabold text-slate-500" data-maldives-operator-label aria-live="polite">Operator</span>
                            </span>
                        </div>
                        <p id="maldives-operator-note" class="mt-2 text-[11px] font-medium text-slate-400" data-maldives-operator-note>Operator is detected from the active operator list.</p>
                        @error('mobile') <p class="ux-error"><i class="fas fa-circle-exclamation mt-0.5"></i>{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <div class="mb-2 flex items-center justify-between gap-3">
                            <label for="maldives_amount" class="ux-field-label mb-0">Recharge amount</label>
                            <span class="text-[11px] font-semibold text-slate-400">Minimum MVR 10.00</span>
                        </div>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex w-20 items-center justify-center rounded-l-2xl border-r border-teal-100 bg-teal-50 text-xs font-extrabold text-teal-700">MVR</span>
                            <input id="maldives_amount" type="number" name="amount" value="{{ old('amount') }}" required min="10" step="0.01" inputmode="decimal" autocomplete="off" placeholder="0.00" class="ux-input pl-24 text-lg font-extrabold" data-maldives-amount-input>
                        </div>
                        <div class="mt-2 flex flex-wrap items-center justify-between gap-2">
                            <p class="text-[11px] font-medium text-slate-400">This amount is deducted when the request is submitted.</p>
                            <div class="flex flex-wrap gap-1.5" aria-label="Quick recharge amounts">
                                @foreach ([10, 50, 100] as $amount)
                                    <button type="button" class="balance-quick-amount" data-maldives-quick-amount="{{ $amount }}">+{{ number_format($amount) }}</button>
                                @endforeach
                            </div>
                        </div>
                        @error('amount') <p class="ux-error"><i class="fas fa-circle-exclamation mt-0.5"></i>{{ $message }}</p> @enderror
                    </div>

                    <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-4 text-xs leading-5 text-emerald-800">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-circle-info mt-0.5 text-emerald-600"></i>
                            <p><strong>Commission:</strong> the eligible operator commission is calculated and added to your balance after approval.</p>
                        </div>
                    </div>

                    <button type="submit" class="ux-primary-button w-full" data-maldives-recharge-submit>
                        <i class="fas fa-bolt"></i> Confirm Maldives Recharge
                    </button>
                </form>
            </section>

            <aside class="space-y-4" aria-label="Maldives recharge information">
                <section class="ux-card p-5 sm:p-6">
                    <span class="ux-eyebrow">How it works</span>
                    <ol class="mt-5 space-y-5">
                        @foreach ([
                            ['fa-mobile-screen-button', 'Enter number', 'Provide the destination Maldives mobile number.'],
                            ['fa-coins', 'Choose amount', 'Enter MVR 10.00 or more for the recharge.'],
                            ['fa-list-check', 'Track status', 'Follow approval and commission in history.'],
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
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-white text-sky-600 shadow-sm"><i class="fas fa-broadcast-tower"></i></span>
                        <div>
                            <h2 class="text-sm font-extrabold text-slate-800">Active operators</h2>
                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                {{ $operators->isNotEmpty() ? $operators->pluck('name')->implode(' · ') : 'No active operator is configured.' }}
                            </p>
                        </div>
                    </div>
                </section>
            </aside>
        </div>
    </div>

    @push('scripts')
        <script>
            (() => {
                const operators = @json($operatorOptions);
                const mobileInput = document.querySelector('[data-maldives-mobile-input]');
                const operatorLabel = document.querySelector('[data-maldives-operator-label]');
                const operatorNote = document.querySelector('[data-maldives-operator-note]');
                const amountInput = document.querySelector('[data-maldives-amount-input]');
                const form = document.querySelector('[data-maldives-recharge-form]');

                const detectOperator = (value) => {
                    const number = String(value || '').replace(/\D/g, '');
                    const candidates = [number];
                    if (number.startsWith('960')) candidates.push(number.slice(3));

                    return [...operators]
                        .filter((operator) => String(operator.code || '').replace(/\D/g, ''))
                        .sort((left, right) => String(right.code).length - String(left.code).length)
                        .find((operator) => {
                            const code = String(operator.code).replace(/\D/g, '');
                            return candidates.some((candidate) => candidate.startsWith(code));
                        });
                };

                const updateOperator = () => {
                    const operator = detectOperator(mobileInput?.value);
                    if (operatorLabel) operatorLabel.textContent = operator?.name || 'Operator';
                    if (operatorNote) {
                        operatorNote.textContent = operator
                            ? `${operator.name} detected${Number(operator.commission) > 0 ? ` · ${Number(operator.commission).toFixed(2)}% commission` : ''}.`
                            : 'Operator is detected from the active operator list.';
                    }
                };

                mobileInput?.addEventListener('input', updateOperator);

                document.querySelectorAll('[data-maldives-quick-amount]').forEach((button) => {
                    button.addEventListener('click', () => {
                        if (!amountInput) return;
                        amountInput.value = button.dataset.maldivesQuickAmount || '';
                        amountInput.focus();
                    });
                });

                form?.addEventListener('submit', () => {
                    const submitButton = form.querySelector('[data-maldives-recharge-submit]');
                    if (!submitButton || submitButton.disabled) return;
                    submitButton.disabled = true;
                    submitButton.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Processing...';
                });

                updateOperator();
            })();
        </script>
    @endpush
</x-frontend-layout>
