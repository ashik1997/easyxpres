<x-frontend-layout>
    <x-slot:metaTitle>Add Balance · EasyXpres</x-slot:metaTitle>
    <x-slot:header>
        <p class="text-[10px] font-bold uppercase tracking-[.18em] text-teal-100">Wallet</p>
        <h1 class="truncate text-lg font-extrabold text-white">Add Balance</h1>
    </x-slot:header>

    <div class="balance-add-page mx-auto max-w-5xl">
        @if ($errors->any())
            <div class="mt-5 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700" role="alert">
                <div class="flex items-start gap-3">
                    <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-red-100"><i class="fas fa-circle-exclamation"></i></span>
                    <div>
                        <p class="font-extrabold">Please check your submission</p>
                        <ul class="mt-1 list-disc space-y-1 pl-4">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <section class="ux-card mt-5 overflow-hidden" aria-labelledby="funding-method-title">
            <div class="border-b border-slate-100 px-5 py-5 sm:px-7 sm:py-6">
                <div class="flex flex-wrap items-end justify-between gap-3">
                    <div>
                        <span class="ux-eyebrow">Step 1 of 2</span>
                        <h2 id="funding-method-title" class="mt-1 text-xl font-extrabold tracking-tight text-slate-900 sm:text-2xl">Choose top-up method</h2>
                        <p class="mt-1 text-sm text-slate-500">Select the payment flow that works best for you.</p>
                    </div>
                    <a href="{{ route('app.balance.history') }}" class="ux-secondary-button shrink-0 px-3 sm:px-5" data-testid="balance-history-link">
                        <i class="fas fa-clock-rotate-left"></i>
                        <span class="hidden sm:inline">Balance History</span>
                        <span class="sm:hidden">History</span>
                    </a>
                </div>

                <div class="mt-5 grid gap-3 sm:grid-cols-2" role="tablist" aria-label="Balance top-up methods">
                    <button
                        id="manual-method-tab"
                        type="button"
                        class="balance-method-card is-selected"
                        role="tab"
                        aria-selected="true"
                        aria-controls="manual-method-panel"
                        data-method-tab="manual"
                    >
                        <span class="balance-method-icon balance-method-icon--manual"><i class="fas fa-receipt"></i></span>
                        <span class="min-w-0 flex-1">
                            <span class="block text-base font-extrabold text-slate-900">Manual Request</span>
                            <span class="mt-0.5 block text-xs font-semibold text-slate-500">Upload payment proof</span>
                        </span>
                        <span class="balance-method-check"><i class="fas fa-check"></i></span>
                    </button>

                    <button
                        id="instant-method-tab"
                        type="button"
                        class="balance-method-card"
                        role="tab"
                        aria-selected="false"
                        aria-controls="instant-method-panel"
                        data-method-tab="instant"
                    >
                        <span class="balance-method-icon balance-method-icon--instant"><i class="fas fa-building-columns"></i></span>
                        <span class="min-w-0 flex-1">
                            <span class="block text-base font-extrabold text-slate-900">Digital Banking</span>
                            <span class="mt-0.5 block text-xs font-semibold text-slate-500">Instant &amp; automatic</span>
                        </span>
                        <span class="balance-method-check"><i class="fas fa-check"></i></span>
                    </button>
                </div>
            </div>

            <div
                id="instant-method-panel"
                class="balance-method-panel hidden p-5 sm:p-7"
                role="tabpanel"
                aria-labelledby="instant-method-tab"
                data-method-panel="instant"
            >
                <div class="grid items-center gap-6 lg:grid-cols-[1fr_auto]">
                    <div class="flex items-start gap-4">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-emerald-100 text-lg text-emerald-700"><i class="fas fa-bolt"></i></span>
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-lg font-extrabold text-slate-900">Fast, verified checkout</h3>
                                <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-[9px] font-extrabold uppercase tracking-wider text-emerald-700">Recommended</span>
                            </div>
                            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">Pay securely through the EPS hosted checkout. Your balance is credited after successful server verification.</p>
                            <div class="mt-4 flex flex-wrap gap-x-5 gap-y-2 text-xs font-semibold text-slate-600">
                                <span class="inline-flex items-center gap-2"><i class="fas fa-circle-check text-emerald-500"></i> No proof upload</span>
                                <span class="inline-flex items-center gap-2"><i class="fas fa-circle-check text-emerald-500"></i> Automatic status</span>
                                <span class="inline-flex items-center gap-2"><i class="fas fa-circle-check text-emerald-500"></i> Secure gateway</span>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('balance.payment.index') }}" class="ux-primary-button w-full whitespace-nowrap lg:w-auto">
                        Pay with Digital Banking <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>
            </div>

            <div
                id="manual-method-panel"
                class="balance-method-panel p-5 sm:p-7"
                role="tabpanel"
                aria-labelledby="manual-method-tab"
                data-method-panel="manual"
            >
                <form method="POST" action="{{ route('app.balance.add') }}" enctype="multipart/form-data" class="space-y-5" data-balance-request-form>
                    @csrf
                    <div>
                        <p class="ux-field-label">Select your bank</p>
                        <input type="hidden" name="payment_bank" value="{{ old('payment_bank', 'mib') }}" data-bank-input>
                        <div class="mt-2 grid gap-3 sm:grid-cols-2" role="radiogroup" aria-label="Select bank for manual request">
                            <button type="button" class="balance-bank-card {{ old('payment_bank', 'mib') === 'mib' ? 'is-selected' : '' }}" role="radio" aria-checked="{{ old('payment_bank', 'mib') === 'mib' ? 'true' : 'false' }}" data-bank="mib">
                                <span class="balance-bank-logo balance-bank-logo--mib" aria-hidden="true">
                                    @if (!empty($app?->mib_bank_logo)) <img src="{{ asset('storage/'.$app->mib_bank_logo) }}" alt=""> @else <span>M</span> @endif
                                </span>
                                <span class="min-w-0 flex-1"><strong>{{ $app?->mib_bank_name ?: 'Maldives Islamic Bank' }}</strong><small>Manual bank transfer</small></span>
                                <span class="balance-bank-check"><i class="fas fa-check"></i></span>
                            </button>
                            <button type="button" class="balance-bank-card {{ old('payment_bank') === 'bml' ? 'is-selected' : '' }}" role="radio" aria-checked="{{ old('payment_bank') === 'bml' ? 'true' : 'false' }}" data-bank="bml">
                                <span class="balance-bank-logo balance-bank-logo--bml" aria-hidden="true">
                                    @if (!empty($app?->bml_bank_logo)) <img src="{{ asset('storage/'.$app->bml_bank_logo) }}" alt=""> @else <span>B</span> @endif
                                </span>
                                <span class="min-w-0 flex-1"><strong>{{ $app?->bml_bank_name ?: 'Bank of Maldives' }}</strong><small>Manual bank transfer</small></span>
                                <span class="balance-bank-check"><i class="fas fa-check"></i></span>
                            </button>
                        </div>
                        @error('payment_bank') <p class="ux-error"><i class="fas fa-circle-exclamation mt-0.5"></i>{{ $message }}</p> @enderror
                    </div>

                    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm leading-6 text-amber-900">
                        <div class="mb-2 flex items-center gap-2 font-extrabold"><i class="fas fa-circle-info"></i> Payment instructions</div>
                        <div data-bank-instructions="mib" class="prose prose-sm max-w-none text-amber-900 {{ old('payment_bank', 'mib') === 'mib' ? '' : 'hidden' }}">
                            @if (!empty($app?->mib_balance_content ?? $app?->add_balance_content))
                                {!! $app?->mib_balance_content ?? $app?->add_balance_content !!}
                            @else
                                <p>Maldives Islamic Bank payment instructions are not available. Please contact support.</p>
                            @endif
                        </div>
                        <div data-bank-instructions="bml" class="prose prose-sm max-w-none text-amber-900 {{ old('payment_bank') === 'bml' ? '' : 'hidden' }}">
                            @if (!empty($app?->bml_balance_content))
                                {!! $app->bml_balance_content !!}
                            @else
                                <p>Bank of Maldives payment instructions are not available. Please contact support.</p>
                            @endif
                        </div>
                    </div>
                    <div>
                        <label for="balance_amount" class="ux-field-label">Top-up amount</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex w-[4.5rem] items-center justify-center rounded-l-2xl border-r border-teal-100 bg-teal-50 text-xs font-extrabold text-teal-700">MVR</span>
                            <input id="balance_amount" type="number" name="amount" value="{{ old('amount') }}" required min="1" step="0.01" inputmode="decimal" autocomplete="off" placeholder="0.00" class="ux-input pl-20 pr-4 text-lg font-extrabold" data-amount-input>
                        </div>
                        <div class="mt-2 flex flex-wrap items-center justify-between gap-2">
                            <p class="text-[11px] font-medium text-slate-400">Minimum MVR 1.00</p>
                            <div class="flex flex-wrap gap-1.5" aria-label="Quick amounts">
                                <button type="button" class="balance-quick-amount" data-quick-amount="100">+100</button>
                                <button type="button" class="balance-quick-amount" data-quick-amount="500">+500</button>
                                <button type="button" class="balance-quick-amount" data-quick-amount="1000">+1,000</button>
                            </div>
                        </div>
                        @error('amount') <p class="ux-error"><i class="fas fa-circle-exclamation mt-0.5"></i>{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="balance_proof" class="ux-field-label">Payment proof</label>
                        <label for="balance_proof" class="balance-upload-zone" data-upload-zone>
                            <span class="balance-upload-icon"><i class="fas fa-cloud-arrow-up"></i></span>
                            <span class="mt-3 text-sm font-extrabold text-slate-700" data-upload-title>Choose payment screenshot</span>
                            <span class="mt-1 max-w-sm truncate text-xs text-slate-400" data-file-name aria-live="polite">JPG, JPEG or PNG · Maximum 2 MB</span>
                            <span class="mt-3 rounded-full bg-white px-3 py-1 text-[10px] font-bold text-teal-700 shadow-sm">Browse file</span>
                        </label>
                        <input id="balance_proof" type="file" name="file_upload" required accept="image/jpeg,image/png" class="sr-only" data-proof-input>
                        @error('file_upload') <p class="ux-error"><i class="fas fa-circle-exclamation mt-0.5"></i>{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-start gap-3 rounded-2xl bg-slate-50 p-4 text-xs leading-5 text-slate-500">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-white text-amber-500 shadow-sm"><i class="fas fa-clock"></i></span>
                        <span><strong class="block text-sm text-slate-700">Admin verification required</strong>Your request stays pending until the payment proof is reviewed.</span>
                    </div>

                    <button type="submit" class="ux-primary-button w-full" data-submit-button>
                        Submit Balance Request <i class="fas fa-paper-plane text-xs"></i>
                    </button>
                </form>
            </div>
        </section>

        <section class="balance-trust-band mt-5" aria-label="Payment benefits">
            <div class="flex items-start gap-3">
                <span class="balance-trust-icon"><i class="fas fa-bolt"></i></span>
                <div><h3 class="text-sm font-extrabold text-slate-800">Fast processing</h3><p class="mt-0.5 text-xs leading-5 text-slate-500">Instant verification with Digital Banking.</p></div>
            </div>
            <div class="flex items-start gap-3">
                <span class="balance-trust-icon"><i class="fas fa-shield-halved"></i></span>
                <div><h3 class="text-sm font-extrabold text-slate-800">Secure &amp; safe</h3><p class="mt-0.5 text-xs leading-5 text-slate-500">Protected checkout and payment records.</p></div>
            </div>
        </section>

        <section class="ux-card mt-5 p-5 sm:p-7" aria-labelledby="balance-process-title">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <div>
                    <span class="ux-eyebrow">Simple process</span>
                    <h2 id="balance-process-title" class="mt-1 text-lg font-extrabold text-slate-900">How it works</h2>
                </div>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-teal-50 px-3 py-1.5 text-[10px] font-bold text-teal-700"><i class="fas fa-lock"></i> Protected flow</span>
            </div>

            <div class="balance-process mt-6">
                <div class="balance-process-step">
                    <span class="balance-process-number">1</span>
                    <span class="balance-process-icon"><i class="fas fa-hand-pointer"></i></span>
                    <h3>Choose method</h3>
                    <p>Pick instant or manual payment.</p>
                </div>
                <div class="balance-process-step">
                    <span class="balance-process-number">2</span>
                    <span class="balance-process-icon"><i class="fas fa-money-bill-transfer"></i></span>
                    <h3>Complete payment</h3>
                    <p>Pay securely and submit details.</p>
                </div>
                <div class="balance-process-step">
                    <span class="balance-process-number">3</span>
                    <span class="balance-process-icon"><i class="fas fa-wallet"></i></span>
                    <h3>Balance updated</h3>
                    <p>Track the result in your history.</p>
                </div>
            </div>
        </section>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const tabs = Array.from(document.querySelectorAll('[data-method-tab]'));
                const panels = Array.from(document.querySelectorAll('[data-method-panel]'));
                const input = document.querySelector('[data-proof-input]');
                const uploadZone = document.querySelector('[data-upload-zone]');
                const uploadTitle = document.querySelector('[data-upload-title]');
                const fileName = document.querySelector('[data-file-name]');
                const amountInput = document.querySelector('[data-amount-input]');
                const form = document.querySelector('[data-balance-request-form]');
                const submitButton = document.querySelector('[data-submit-button]');
                const bankButtons = Array.from(document.querySelectorAll('[data-bank]'));
                const bankInput = document.querySelector('[data-bank-input]');
                const bankInstructions = Array.from(document.querySelectorAll('[data-bank-instructions]'));

                const selectMethod = (method) => {
                    tabs.forEach((tab) => {
                        const selected = tab.dataset.methodTab === method;
                        tab.classList.toggle('is-selected', selected);
                        tab.setAttribute('aria-selected', selected ? 'true' : 'false');
                        tab.setAttribute('tabindex', selected ? '0' : '-1');
                    });
                    panels.forEach((panel) => panel.classList.toggle('hidden', panel.dataset.methodPanel !== method));
                };

                tabs.forEach((tab) => tab.addEventListener('click', () => selectMethod(tab.dataset.methodTab)));

                bankButtons.forEach((button) => button.addEventListener('click', () => {
                    bankButtons.forEach((item) => {
                        const selected = item === button;
                        item.classList.toggle('is-selected', selected);
                        item.setAttribute('aria-checked', selected ? 'true' : 'false');
                    });
                    if (bankInput) bankInput.value = button.dataset.bank || 'mib';
                    bankInstructions.forEach((panel) => panel.classList.toggle('hidden', panel.dataset.bankInstructions !== button.dataset.bank));
                }));

                input?.addEventListener('change', () => {
                    const file = input.files?.[0];
                    fileName.textContent = file?.name || 'JPG, JPEG or PNG · Maximum 2 MB';
                    uploadTitle.textContent = file ? 'Screenshot selected' : 'Choose payment screenshot';
                    uploadZone?.classList.toggle('has-file', Boolean(file));
                });

                document.querySelectorAll('[data-quick-amount]').forEach((button) => {
                    button.addEventListener('click', () => {
                        if (!amountInput) return;
                        amountInput.value = button.dataset.quickAmount || '';
                        amountInput.focus();
                    });
                });

                form?.addEventListener('submit', () => {
                    if (!submitButton || submitButton.disabled) return;
                    submitButton.disabled = true;
                    submitButton.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Submitting...';
                });

                @if ($errors->has('amount') || $errors->has('payment_bank') || $errors->has('file_upload'))
                    selectMethod('manual');
                @else
                    const requestedMethod = new URLSearchParams(window.location.search).get('method');
                    selectMethod(requestedMethod === 'instant' ? 'instant' : 'manual');
                @endif
            });
        </script>
    @endpush
</x-frontend-layout>
