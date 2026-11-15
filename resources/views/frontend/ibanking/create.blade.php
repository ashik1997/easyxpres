@php
    $minimumMvr = $rate > 0 ? ceil(($minimumBdt / $rate) * 100) / 100 : 0;
    $currentAmount = old('amount');
    $currentBdt = $currentAmount !== null && $rate > 0 ? (float) $currentAmount * $rate : null;
    $availableBalance = (float) (Auth::user()->account->balance ?? 0);
    $quickTargets = [$minimumBdt, 50000, 100000];
@endphp

<x-frontend-layout>
    <x-slot:metaTitle>iBanking Transfer · EasyXpres</x-slot:metaTitle>
    <x-slot:header>
        <p class="text-[10px] font-bold uppercase tracking-[.18em] text-teal-100">Bank transfer</p>
        <h1 class="truncate text-lg font-extrabold text-white">iBanking</h1>
    </x-slot:header>

    <div class="ibanking-page mx-auto max-w-5xl">
        @if ($errors->any())
            <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700" role="alert">
                <div class="flex items-start gap-3">
                    <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-red-100"><i class="fas fa-circle-exclamation"></i></span>
                    <div>
                        <p class="font-extrabold">Please check your transfer details</p>
                        <ul class="mt-1 list-disc space-y-1 pl-4">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <section class="ibanking-hero" aria-labelledby="ibanking-page-title">
            <span class="ibanking-hero-orb ibanking-hero-orb--one"></span>
            <span class="ibanking-hero-orb ibanking-hero-orb--two"></span>
            <div class="relative z-10 max-w-xl">
                <span class="ibanking-hero-kicker"><i class="fas fa-shield-halved"></i> Secure bank transfer</span>
                <h2 id="ibanking-page-title" class="mt-3 text-2xl font-extrabold tracking-tight sm:text-3xl">Send money to Bangladesh</h2>
                <p class="mt-2 max-w-lg text-sm leading-6 text-teal-50/85">Enter the recipient’s bank details, preview the converted amount and submit your transfer securely.</p>
            </div>
            <div class="ibanking-rate-card">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-white/15 text-sm"><i class="fas fa-exchange-alt"></i></span>
                <div>
                    <p class="text-[9px] font-extrabold uppercase tracking-[.14em] text-teal-50/70">Today’s rate</p>
                    @if ($rate > 0)
                        <p class="mt-0.5 text-lg font-extrabold">1 MVR = {{ number_format($rate, 2) }} BDT</p>
                    @else
                        <p class="mt-0.5 text-sm font-extrabold text-amber-200">Rate temporarily unavailable</p>
                    @endif
                </div>
            </div>
        </section>

        <div class="mt-5 grid items-start gap-5 lg:grid-cols-[minmax(0,1fr)_300px]">
            <section class="ux-card overflow-hidden" aria-labelledby="transfer-details-title">
                <div class="border-b border-slate-100 px-5 py-5 sm:px-7 sm:py-6">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <span class="ux-eyebrow">Transfer details</span>
                            <h2 id="transfer-details-title" class="mt-1 text-xl font-extrabold tracking-tight text-slate-900">Recipient &amp; amount</h2>
                            <p class="mt-1 text-sm text-slate-500">Make sure the account information matches the recipient.</p>
                        </div>
                        <a href="{{ route('app.ibanking.history') }}" class="ux-secondary-button shrink-0 px-3 sm:px-4" data-testid="ibanking-history-link">
                            <i class="fas fa-clock-rotate-left"></i>
                            <span class="hidden sm:inline">Transfer History</span>
                            <span class="sm:hidden">History</span>
                        </a>
                    </div>
                </div>

                <form method="POST" action="{{ route('app.ibanking.add') }}" enctype="multipart/form-data" class="space-y-6 p-5 sm:p-7" data-ibanking-form>
                    @csrf

                    <div>
                        <label for="ibanking_bank" class="ux-field-label">Recipient bank</label>
                        <div class="relative">
                            <span class="ux-input-icon"><i class="fas fa-building-columns"></i></span>
                            <select id="ibanking_bank" name="bank_name_id" required class="ux-input pl-12" data-bank-select @disabled($banks->isEmpty())>
                                <option value="">Choose a bank</option>
                                @foreach ($banks as $bank)
                                    <option value="{{ $bank->id }}" @selected((string) old('bank_name_id') === (string) $bank->id)>{{ $bank->bank_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if ($banks->isEmpty())
                            <p class="ux-error"><i class="fas fa-circle-exclamation mt-0.5"></i>No active bank is available right now.</p>
                        @else
                            @error('bank_name_id') <p class="ux-error"><i class="fas fa-circle-exclamation mt-0.5"></i>{{ $message }}</p> @enderror
                        @endif
                    </div>

                    <div>
                        <label for="ibanking_holder" class="ux-field-label">Account holder name <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="ux-input-icon"><i class="fas fa-user"></i></span>
                            <input id="ibanking_holder" type="text" name="account_holder_name" value="{{ old('account_holder_name') }}" maxlength="120" autocomplete="name" placeholder="Account holder name" required class="ux-input-with-icon">
                        </div>
                        @error('account_holder_name') <p class="ux-error"><i class="fas fa-circle-exclamation mt-0.5"></i>{{ $message }}</p> @enderror
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="ibanking_account" class="ux-field-label">Account number</label>
                            <div class="relative">
                                <span class="ux-input-icon"><i class="fas fa-credit-card"></i></span>
                                <input id="ibanking_account" type="text" name="account_no" value="{{ old('account_no') }}" required maxlength="50" inputmode="numeric" autocomplete="off" placeholder="Enter account number" class="ux-input-with-icon font-semibold">
                            </div>
                            @error('account_no') <p class="ux-error"><i class="fas fa-circle-exclamation mt-0.5"></i>{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="ibanking_branch" class="ux-field-label">Branch <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="ux-input-icon"><i class="fas fa-map-marker-alt"></i></span>
                                <input id="ibanking_branch" type="text" name="branch" value="{{ old('branch') }}" maxlength="120" autocomplete="off" placeholder="Branch name" required class="ux-input-with-icon">
                            </div>
                            @error('branch') <p class="ux-error"><i class="fas fa-circle-exclamation mt-0.5"></i>{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="ibanking_document" class="ux-field-label">Supporting document <span class="font-medium text-slate-400">(optional)</span></label>
                        <div class="relative">
                            <span class="ux-input-icon"><i class="fas fa-paperclip"></i></span>
                            <input id="ibanking_document" type="file" name="document_attachment" accept=".jpg,.jpeg,.png,.pdf" class="ux-input-with-icon">
                        </div>
                        <p class="mt-1 text-xs text-slate-500">Upload a JPG, PNG or PDF document up to 5 MB.</p>
                        @error('document_attachment') <p class="ux-error"><i class="fas fa-circle-exclamation mt-0.5"></i>{{ $message }}</p> @enderror
                    </div>

                    <div class="ibanking-amount-panel">
                        <div class="mb-4 flex flex-wrap items-end justify-between gap-2">
                            <div>
                                <label for="ibanking_mvr" class="block text-sm font-extrabold text-slate-800">Transfer amount</label>
                                <p class="mt-1 text-xs text-slate-500">Enter either currency to calculate the other.</p>
                            </div>
                            <span class="rounded-full bg-white px-3 py-1.5 text-[10px] font-extrabold text-teal-700 shadow-sm">Minimum {{ number_format($minimumBdt) }} BDT</span>
                        </div>

                        <div class="grid items-center gap-3 sm:grid-cols-[minmax(0,1fr)_2.5rem_minmax(0,1fr)]">
                            <div>
                                <span class="mb-1.5 block text-[10px] font-extrabold uppercase tracking-wider text-slate-500">You send</span>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex w-[4.25rem] items-center justify-center rounded-l-2xl border-r border-teal-100 bg-teal-50 text-xs font-extrabold text-teal-700">MVR</span>
                                    <input id="ibanking_mvr" type="number" name="amount" value="{{ $currentAmount }}" required min="{{ $minimumMvr ?: 1 }}" step="0.01" inputmode="decimal" autocomplete="off" placeholder="0.00" class="ux-input pl-[4.75rem] text-lg font-extrabold" data-mvr-input @disabled($rate <= 0)>
                                </div>
                            </div>
                            <span class="ibanking-conversion-arrow"><i class="fas fa-exchange-alt"></i></span>
                            <div>
                                <span class="mb-1.5 block text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Recipient gets</span>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex w-[4.25rem] items-center justify-center rounded-l-2xl border-r border-emerald-100 bg-emerald-50 text-xs font-extrabold text-emerald-700">BDT</span>
                                    <input id="ibanking_bdt" type="number" value="{{ $currentBdt !== null ? number_format($currentBdt, 2, '.', '') : '' }}" min="{{ $minimumBdt }}" step="0.01" inputmode="decimal" autocomplete="off" placeholder="0.00" class="ux-input pl-[4.75rem] text-lg font-extrabold" data-bdt-input @disabled($rate <= 0)>
                                </div>
                            </div>
                        </div>

                        @if ($rate > 0)
                            <div class="mt-4 flex flex-wrap items-center justify-between gap-2">
                                <p class="text-[11px] font-semibold text-slate-500">Minimum debit: {{ number_format($minimumMvr, 2) }} MVR</p>
                                <div class="flex flex-wrap gap-1.5" aria-label="Quick transfer amounts">
                                    @foreach ($quickTargets as $target)
                                        @php $quickMvr = ceil(($target / $rate) * 100) / 100; @endphp
                                        <button type="button" class="ibanking-quick-amount" data-quick-mvr="{{ number_format($quickMvr, 2, '.', '') }}">{{ number_format($target / 1000) }}k BDT</button>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        @error('amount') <p class="ux-error"><i class="fas fa-circle-exclamation mt-0.5"></i>{{ $message }}</p> @enderror
                    </div>

                    <div class="ibanking-summary" aria-live="polite">
                        <div class="flex items-center justify-between gap-4">
                            <span class="text-slate-500">Recipient bank</span>
                            <strong class="max-w-[60%] truncate text-right text-slate-800" data-summary-bank>Not selected</strong>
                        </div>
                        <div class="mt-3 flex items-center justify-between gap-4">
                            <span class="text-slate-500">Balance debit</span>
                            <strong class="text-slate-800"><span data-summary-mvr>0.00</span> MVR</strong>
                        </div>
                        <div class="mt-3 flex items-center justify-between gap-4 border-t border-slate-200 pt-3">
                            <span class="font-bold text-slate-700">Recipient amount</span>
                            <strong class="text-base text-emerald-700"><span data-summary-bdt>0.00</span> BDT</strong>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 rounded-2xl border border-amber-100 bg-amber-50 p-4 text-xs leading-5 text-amber-900">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-white text-amber-500 shadow-sm"><i class="fas fa-circle-info"></i></span>
                        <span><strong class="block text-sm">Review before submitting</strong>Transfers are processed using the account details you provide and remain pending until reviewed.</span>
                    </div>

                    <button type="submit" class="ux-primary-button w-full" data-submit-button @disabled($rate <= 0 || $banks->isEmpty())>
                        Confirm Bank Transfer <i class="fas fa-arrow-right text-xs"></i>
                    </button>
                </form>
            </section>

            <aside class="space-y-4" aria-label="Transfer information">
                <section class="ibanking-balance-card">
                    <div class="flex items-start justify-between gap-3">
                        <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-white/15 text-lg"><i class="fas fa-wallet"></i></span>
                        <span class="rounded-full border border-white/15 bg-white/10 px-2.5 py-1 text-[9px] font-extrabold uppercase tracking-wider text-teal-50">Available</span>
                    </div>
                    <p class="mt-5 text-xs font-semibold text-teal-50/75">Your balance</p>
                    <p class="mt-1 text-2xl font-extrabold tracking-tight">{{ number_format($availableBalance, 2) }} <span class="text-sm text-teal-50/75">MVR</span></p>
                    <p class="mt-2 text-[11px] leading-5 text-teal-50/65">The MVR amount will be deducted after a successful submission.</p>
                </section>

                <section class="ux-card p-5">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <span class="ux-eyebrow">Simple process</span>
                            <h2 class="mt-1 text-base font-extrabold text-slate-900">How it works</h2>
                        </div>
                        <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-teal-50 text-teal-700"><i class="fas fa-list-check"></i></span>
                    </div>
                    <ol class="mt-5 space-y-4">
                        <li class="ibanking-step"><span>1</span><div><strong>Add recipient</strong><p>Select the bank and enter account details.</p></div></li>
                        <li class="ibanking-step"><span>2</span><div><strong>Check conversion</strong><p>Review the MVR debit and BDT amount.</p></div></li>
                        <li class="ibanking-step"><span>3</span><div><strong>Track status</strong><p>Follow your request from transfer history.</p></div></li>
                    </ol>
                </section>

                <section class="rounded-3xl border border-emerald-100 bg-emerald-50 p-5">
                    <div class="flex items-start gap-3">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-white text-emerald-600 shadow-sm"><i class="fas fa-lock"></i></span>
                        <div><h2 class="text-sm font-extrabold text-emerald-900">Protected request</h2><p class="mt-1 text-xs leading-5 text-emerald-800/75">The exchange rate and final amount are verified again on the server.</p></div>
                    </div>
                </section>
            </aside>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const rate = Number(@json($rate));
                const minimumBdt = Number(@json($minimumBdt));
                const mvrInput = document.querySelector('[data-mvr-input]');
                const bdtInput = document.querySelector('[data-bdt-input]');
                const bankSelect = document.querySelector('[data-bank-select]');
                const summaryBank = document.querySelector('[data-summary-bank]');
                const summaryMvr = document.querySelector('[data-summary-mvr]');
                const summaryBdt = document.querySelector('[data-summary-bdt]');
                const form = document.querySelector('[data-ibanking-form]');
                const submitButton = document.querySelector('[data-submit-button]');
                const formatter = new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                const numericValue = (input) => {
                    const value = Number.parseFloat(input?.value || '');
                    return Number.isFinite(value) ? value : 0;
                };

                const updateSummary = () => {
                    const mvr = numericValue(mvrInput);
                    const bdt = rate > 0 ? mvr * rate : 0;
                    summaryMvr.textContent = formatter.format(mvr);
                    summaryBdt.textContent = formatter.format(bdt);
                    const selectedOption = bankSelect?.options[bankSelect.selectedIndex];
                    summaryBank.textContent = selectedOption?.value ? selectedOption.text : 'Not selected';
                };

                const validateMinimum = () => {
                    if (!mvrInput) return;
                    if (rate <= 0 || !mvrInput.value) {
                        mvrInput.setCustomValidity('');
                        return;
                    }
                    const converted = numericValue(mvrInput) * rate;
                    mvrInput.setCustomValidity(
                        Math.round(converted * 100) / 100 < minimumBdt
                            ? `The converted iBanking amount must be at least ${minimumBdt.toLocaleString('en-US')} BDT.`
                            : ''
                    );
                };

                const convertFromMvr = () => {
                    if (!bdtInput || rate <= 0) return;
                    bdtInput.value = mvrInput?.value ? (numericValue(mvrInput) * rate).toFixed(2) : '';
                    validateMinimum();
                    updateSummary();
                };

                const convertFromBdt = () => {
                    if (!mvrInput || rate <= 0) return;
                    const convertedMvr = Math.ceil((numericValue(bdtInput) / rate) * 100) / 100;
                    mvrInput.value = bdtInput?.value ? convertedMvr.toFixed(2) : '';
                    validateMinimum();
                    updateSummary();
                };

                mvrInput?.addEventListener('input', convertFromMvr);
                bdtInput?.addEventListener('input', convertFromBdt);
                bankSelect?.addEventListener('change', updateSummary);

                document.querySelectorAll('[data-quick-mvr]').forEach((button) => {
                    button.addEventListener('click', () => {
                        if (!mvrInput) return;
                        mvrInput.value = button.dataset.quickMvr || '';
                        convertFromMvr();
                        mvrInput.focus();
                    });
                });

                form?.addEventListener('submit', () => {
                    validateMinimum();
                    if (!submitButton || submitButton.disabled || !form.checkValidity()) return;
                    submitButton.disabled = true;
                    submitButton.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Submitting transfer...';
                });

                validateMinimum();
                updateSummary();
            });
        </script>
    @endpush
</x-frontend-layout>
