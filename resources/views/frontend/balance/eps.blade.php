<x-dashboard-layout>
    <x-slot name="metaTitle">Add Balance with EPS</x-slot>

    <style>
        #eps-payment-page,
        #eps-payment-page * {
            box-sizing: border-box;
        }

        #eps-payment-page {
            width: 100%;
            max-width: 72rem;
            margin: 0 auto;
            padding: 32px 20px;
            color: #e2e8f0;
        }

        #eps-payment-page .eps-page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 20px;
        }

        #eps-payment-page .eps-back-link {
            color: #fdba74;
            font-size: 14px;
            text-decoration: none;
        }

        #eps-payment-page .eps-back-link:hover {
            color: #fed7aa;
        }

        #eps-payment-page .eps-page-title {
            margin: 4px 0 0;
            color: #fff;
            font-size: clamp(22px, 4vw, 28px);
            font-weight: 800;
            line-height: 1.2;
        }

        #eps-payment-page .eps-page-subtitle {
            margin: 5px 0 0;
            color: #94a3b8;
            font-size: 14px;
        }

        #eps-payment-page .eps-current-balance {
            flex: 0 0 auto;
            min-width: 190px;
            padding: 12px 20px;
            border: 1px solid rgba(251, 191, 36, .3);
            border-radius: 16px;
            background: rgba(251, 191, 36, .1);
        }

        #eps-payment-page .eps-current-balance-label {
            margin: 0;
            color: #94a3b8;
            font-size: 12px;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        #eps-payment-page .eps-current-balance-value {
            margin: 2px 0 0;
            color: #fcd34d;
            font-size: 20px;
            font-weight: 800;
        }

        #eps-payment-page .eps-alert {
            margin-bottom: 16px;
            padding: 12px 16px;
            border: 1px solid;
            border-radius: 12px;
            overflow-wrap: anywhere;
        }

        #eps-payment-page .eps-alert-success {
            border-color: rgba(16, 185, 129, .3);
            background: rgba(16, 185, 129, .1);
            color: #a7f3d0;
        }

        #eps-payment-page .eps-alert-info {
            border-color: rgba(59, 130, 246, .3);
            background: rgba(59, 130, 246, .1);
            color: #bfdbfe;
        }

        #eps-payment-page .eps-alert-error {
            border-color: rgba(239, 68, 68, .3);
            background: rgba(239, 68, 68, .1);
            color: #fecaca;
        }

        #eps-payment-page .eps-main-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 320px;
            gap: 20px;
            margin-bottom: 20px;
        }

        #eps-payment-page .eps-card {
            min-width: 0;
            padding: 28px;
            border: 1px solid rgba(255, 255, 255, .1);
            border-radius: 16px;
            background: rgba(255, 255, 255, .05);
            box-shadow: 0 18px 38px rgba(2, 6, 23, .2);
        }

        #eps-payment-page .eps-card-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
        }

        #eps-payment-page .eps-eyebrow {
            margin: 0;
            color: #fdba74;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .18em;
            text-transform: uppercase;
        }

        #eps-payment-page .eps-card-title {
            margin: 4px 0 0;
            color: #fff;
            font-size: 20px;
            font-weight: 800;
        }

        #eps-payment-page .eps-live-badge {
            flex: 0 0 auto;
            padding: 5px 12px;
            border: 1px solid rgba(34, 211, 238, .3);
            border-radius: 999px;
            background: rgba(34, 211, 238, .1);
            color: #a5f3fc;
            font-size: 12px;
            font-weight: 700;
        }

        #eps-payment-page .eps-form {
            display: grid;
            gap: 20px;
            margin-top: 24px;
        }

        #eps-payment-page .eps-label {
            display: block;
            margin-bottom: 8px;
            color: #e2e8f0;
            font-size: 14px;
            font-weight: 700;
        }

        #eps-payment-page .eps-amount-control {
            display: flex;
            width: 100%;
            min-width: 0;
            overflow: hidden;
            border: 1px solid #475569;
            border-radius: 12px;
            background: rgba(15, 23, 42, .72);
        }

        #eps-payment-page .eps-amount-control:focus-within {
            border-color: #fb923c;
            box-shadow: 0 0 0 3px rgba(251, 146, 60, .15);
        }

        #eps-payment-page .eps-currency-prefix {
            display: flex;
            flex: 0 0 auto;
            align-items: center;
            padding: 0 16px;
            background: rgba(249, 115, 22, .15);
            color: #fdba74;
            font-weight: 800;
        }

        #eps-payment-page .eps-amount-input {
            width: 100%;
            min-width: 0;
            padding: 12px 16px;
            border: 0;
            outline: 0;
            background: transparent;
            color: #fff;
            font: inherit;
        }

        #eps-payment-page .eps-amount-input::placeholder {
            color: #64748b;
        }

        #eps-payment-page .eps-field-error {
            margin: 8px 0 0;
            color: #fca5a5;
            font-size: 14px;
        }

        #eps-payment-page .eps-quick-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 8px;
        }

        #eps-payment-page .eps-quick-button {
            min-width: 0;
            padding: 9px 8px;
            border: 1px solid rgba(251, 146, 60, .3);
            border-radius: 12px;
            background: rgba(249, 115, 22, .1);
            color: #fed7aa;
            font: inherit;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
        }

        #eps-payment-page .eps-quick-button:hover,
        #eps-payment-page .eps-quick-button:focus-visible {
            background: rgba(249, 115, 22, .22);
        }

        #eps-payment-page .eps-credit-box {
            padding: 16px;
            border: 1px solid #334155;
            border-radius: 12px;
            background: rgba(15, 23, 42, .62);
        }

        #eps-payment-page .eps-credit-row {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            font-size: 14px;
        }

        #eps-payment-page .eps-credit-label {
            color: #94a3b8;
        }

        #eps-payment-page .eps-credit-value {
            flex: 0 0 auto;
            color: #6ee7b7;
        }

        #eps-payment-page .eps-security-note {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 12px;
            border: 1px solid rgba(59, 130, 246, .22);
            border-radius: 12px;
            background: rgba(59, 130, 246, .1);
            color: #dbeafe;
            font-size: 12px;
            line-height: 1.55;
        }

        #eps-payment-page #eps-pay-button {
            display: flex !important;
            width: 100%;
            min-height: 48px;
            align-items: center;
            justify-content: center;
            padding: 12px 20px;
            border: 0;
            border-radius: 12px;
            background: linear-gradient(90deg, #f97316, #f59e0b) !important;
            color: #fff !important;
            font: inherit;
            font-weight: 800;
            line-height: 1.3;
            text-align: center;
            box-shadow: 0 10px 24px rgba(249, 115, 22, .24);
            cursor: pointer;
            transition: background .2s ease, transform .2s ease, opacity .2s ease;
        }

        #eps-payment-page #eps-pay-button:hover:not(:disabled) {
            background: linear-gradient(90deg, #ea580c, #d97706) !important;
            transform: translateY(-1px);
        }

        #eps-payment-page #eps-pay-button:disabled {
            opacity: .6;
            cursor: not-allowed;
        }

        #eps-payment-page .eps-how-card {
            align-self: start;
            padding: 20px;
            color: #cbd5e1;
            font-size: 14px;
        }

        #eps-payment-page .eps-how-title,
        #eps-payment-page .eps-history-title {
            margin: 0;
            color: #fff;
            font-weight: 800;
        }

        #eps-payment-page .eps-steps {
            display: grid;
            gap: 16px;
            margin: 16px 0 0;
            padding: 0;
            list-style: none;
        }

        #eps-payment-page .eps-step {
            display: flex;
            gap: 12px;
        }

        #eps-payment-page .eps-step-number {
            color: #fdba74;
        }

        #eps-payment-page .eps-how-footer {
            margin-top: 20px;
            padding-top: 16px;
            border-top: 1px solid rgba(255, 255, 255, .1);
            color: #94a3b8;
            font-size: 12px;
        }

        #eps-payment-page .eps-history-card {
            min-width: 0;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, .1);
            border-radius: 16px;
            background: rgba(255, 255, 255, .05);
            box-shadow: 0 18px 38px rgba(2, 6, 23, .2);
        }

        #eps-payment-page .eps-history-header {
            padding: 16px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, .1);
        }

        #eps-payment-page .eps-history-scroll {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        #eps-payment-page .eps-history-table {
            width: 100%;
            min-width: 980px;
            border-collapse: collapse;
            color: #e2e8f0;
            font-size: 14px;
            text-align: left;
        }

        #eps-payment-page .eps-history-table thead {
            background: rgba(15, 23, 42, .52);
            color: #94a3b8;
            font-size: 12px;
            text-transform: uppercase;
        }

        #eps-payment-page .eps-history-table th,
        #eps-payment-page .eps-history-table td {
            padding: 12px 20px;
            vertical-align: middle;
        }

        #eps-payment-page .eps-history-table tbody tr + tr {
            border-top: 1px solid rgba(255, 255, 255, .1);
        }

        #eps-payment-page .eps-status-badge {
            display: inline-flex;
            padding: 4px 10px;
            border: 1px solid rgba(148, 163, 184, .28);
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
        }

        #eps-payment-page .eps-check-button {
            padding: 0;
            border: 0;
            background: transparent;
            color: #fdba74;
            font: inherit;
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
            cursor: pointer;
        }

        #eps-payment-page .eps-pagination {
            padding: 16px 20px;
            border-top: 1px solid rgba(255, 255, 255, .1);
        }

        @media (max-width: 900px) {
            #eps-payment-page .eps-main-grid {
                grid-template-columns: minmax(0, 1fr);
            }
        }

        @media (max-width: 700px) {
            #eps-payment-page {
                padding: 20px 12px;
            }

            #eps-payment-page .eps-page-header {
                align-items: stretch;
                flex-direction: column;
            }

            #eps-payment-page .eps-current-balance {
                width: 100%;
                min-width: 0;
            }

            #eps-payment-page .eps-card {
                padding: 18px 16px;
            }

            #eps-payment-page .eps-card-header {
                gap: 10px;
            }

            #eps-payment-page .eps-eyebrow {
                letter-spacing: .1em;
            }

            #eps-payment-page .eps-credit-row {
                align-items: flex-start;
                flex-direction: column;
                gap: 5px;
            }

            #eps-payment-page .eps-history-scroll {
                overflow: visible;
            }

            #eps-payment-page .eps-history-table,
            #eps-payment-page .eps-history-table tbody,
            #eps-payment-page .eps-history-table tr,
            #eps-payment-page .eps-history-table td {
                display: block;
                width: 100%;
                min-width: 0;
            }

            #eps-payment-page .eps-history-table thead {
                display: none;
            }

            #eps-payment-page .eps-history-table tbody tr {
                padding: 12px 16px;
            }

            #eps-payment-page .eps-history-table tbody tr + tr {
                border-top: 1px solid rgba(255, 255, 255, .1);
            }

            #eps-payment-page .eps-history-table td {
                display: grid;
                grid-template-columns: minmax(105px, .75fr) minmax(0, 1.25fr);
                gap: 12px;
                padding: 6px 0;
                white-space: normal !important;
                overflow-wrap: anywhere;
            }

            #eps-payment-page .eps-history-table td::before {
                content: attr(data-label);
                color: #94a3b8;
                font-size: 11px;
                font-weight: 700;
                letter-spacing: .04em;
                text-transform: uppercase;
            }

            #eps-payment-page .eps-history-table td[colspan] {
                display: block;
                padding: 28px 0;
                text-align: center;
            }

            #eps-payment-page .eps-history-table td[colspan]::before {
                content: none;
            }
        }

        @media (max-width: 420px) {
            #eps-payment-page .eps-quick-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            #eps-payment-page .eps-card-title {
                font-size: 18px;
            }

            #eps-payment-page .eps-live-badge {
                padding: 4px 9px;
            }
        }
    </style>

    <main id="eps-payment-page" class="max-w-6xl mx-auto px-3 sm:px-5 py-5 sm:py-8 space-y-5">
        <div class="eps-page-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <a href="{{ route('balance.add.form') }}" class="eps-back-link text-sm text-orange-300 hover:text-orange-200">← Add Balance</a>
                <h1 class="eps-page-title mt-1 text-2xl font-bold text-white">Add Balance with EPS</h1>
                <p class="eps-page-subtitle text-sm text-slate-400">Secure live checkout with server-side payment verification.</p>
            </div>
            <div class="eps-current-balance rounded-2xl border border-amber-400/30 bg-amber-400/10 px-5 py-3">
                <p class="eps-current-balance-label text-xs uppercase tracking-wider text-slate-400">Current balance</p>
                <p class="eps-current-balance-value text-xl font-extrabold text-amber-300">
                    MVR {{ number_format((float) ($account?->balance ?? 0), 2) }}
                </p>
            </div>
        </div>

        @if (session('success'))
            <div class="eps-alert eps-alert-success rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-emerald-200">
                {{ session('success') }}
            </div>
        @endif
        @if (session('info'))
            <div class="eps-alert eps-alert-info rounded-xl border border-blue-500/30 bg-blue-500/10 px-4 py-3 text-blue-200">
                {{ session('info') }}
            </div>
        @endif
        @if (session('error'))
            <div class="eps-alert eps-alert-error rounded-xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-red-200">
                {{ session('error') }}
            </div>
        @endif

        <section class="eps-main-grid grid lg:grid-cols-[minmax(0,1fr)_320px] gap-5">
            <div class="eps-card rounded-2xl border border-white/10 bg-white/5 p-5 sm:p-7 shadow-xl">
                <div class="eps-card-header flex items-start justify-between gap-4">
                    <div>
                        <p class="eps-eyebrow text-xs font-semibold uppercase tracking-[0.2em] text-orange-300">EPS Payment Gateway</p>
                        <h2 class="eps-card-title mt-1 text-xl font-bold text-white">Enter payment amount</h2>
                    </div>
                    <span class="eps-live-badge rounded-full border border-cyan-400/30 bg-cyan-400/10 px-3 py-1 text-xs font-semibold text-cyan-200">
                        Live
                    </span>
                </div>

                    <form id="eps-payment-form" method="POST" action="{{ route('balance.payment.create') }}" class="eps-form mt-6 space-y-5">
                        @csrf

                        <div>
                            <label for="eps-amount" class="eps-label mb-2 block text-sm font-semibold text-slate-200">EPS payable amount (BDT)</label>
                            <div class="eps-amount-control flex overflow-hidden rounded-xl border border-slate-600 bg-slate-900/70 focus-within:border-orange-400">
                                <span class="eps-currency-prefix flex items-center bg-orange-500/15 px-4 font-bold text-orange-300">BDT</span>
                                <input
                                    id="eps-amount"
                                    name="amount"
                                    type="number"
                                    required
                                    min="{{ $minimumAmount }}"
                                    max="{{ $maximumAmount }}"
                                    step="0.01"
                                    value="{{ old('amount') }}"
                                    placeholder="500.00"
                                    class="eps-amount-input w-full border-0 bg-transparent px-4 py-3 text-white outline-none"
                                >
                            </div>
                            @error('amount')
                                <p class="eps-field-error mt-2 text-sm text-red-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="eps-quick-grid grid grid-cols-2 sm:grid-cols-4 gap-2" aria-label="Quick amount selection">
                            @foreach ([500, 1000, 2000, 5000] as $quickAmount)
                                <button
                                    type="button"
                                    data-quick-amount="{{ $quickAmount }}"
                                    class="eps-quick-button rounded-xl border border-orange-400/25 bg-orange-500/10 px-3 py-2 text-sm font-semibold text-orange-200 hover:bg-orange-500/20"
                                >
                                    BDT {{ number_format($quickAmount) }}
                                </button>
                            @endforeach
                        </div>

                        <div class="eps-credit-box rounded-xl border border-slate-700 bg-slate-900/60 p-4">
                            <div class="eps-credit-row flex justify-between gap-4 text-sm">
                                <span class="eps-credit-label text-slate-400">Balance credit after verification</span>
                                <strong id="eps-credit-preview" class="eps-credit-value text-emerald-300">MVR 0.00</strong>
                            </div>
                            <p class="mt-2 text-xs text-slate-400">Current rate: 1 MVR = BDT {{ number_format($exchangeRate, 6) }}</p>
                        </div>

                        <div class="eps-security-note flex items-center gap-3 rounded-xl border border-blue-500/20 bg-blue-500/10 p-3 text-xs text-blue-100">
                            <i class="fas fa-shield-alt"></i>
                            <span>Your balance is credited only after EPS confirms the transaction through its verification API.</span>
                        </div>

                        <button
                            id="eps-pay-button"
                            type="submit"
                            class="w-full rounded-xl bg-gradient-to-r from-orange-500 to-amber-500 px-5 py-3 font-bold text-white shadow-lg transition hover:from-orange-600 hover:to-amber-600 disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            <span data-button-label>Pay securely with EPS</span>
                        </button>
                    </form>
            </div>

            <aside class="eps-card eps-how-card rounded-2xl border border-white/10 bg-white/5 p-5 text-sm text-slate-300">
                <h2 class="eps-how-title font-bold text-white">How it works</h2>
                <ol class="eps-steps mt-4 space-y-4">
                    <li class="eps-step flex gap-3"><span class="eps-step-number text-orange-300">1.</span><span>Enter a BDT amount and continue to EPS.</span></li>
                    <li class="eps-step flex gap-3"><span class="eps-step-number text-orange-300">2.</span><span>Complete payment on the hosted EPS payment page.</span></li>
                    <li class="eps-step flex gap-3"><span class="eps-step-number text-orange-300">3.</span><span>We verify reference, status, and paid amount directly with EPS.</span></li>
                    <li class="eps-step flex gap-3"><span class="eps-step-number text-orange-300">4.</span><span>The BDT amount is converted and credited once in MVR.</span></li>
                </ol>
                <div class="eps-how-footer mt-5 border-t border-white/10 pt-4 text-xs text-slate-400">
                    Prefer a manual request? Return to the Add Balance page and upload your payment proof.
                </div>
            </aside>
        </section>

        <section class="eps-history-card rounded-2xl border border-white/10 bg-white/5 shadow-xl overflow-hidden">
            <div class="eps-history-header border-b border-white/10 px-5 py-4">
                <h2 class="eps-history-title font-bold text-white">EPS transaction history</h2>
            </div>
            <div class="eps-history-scroll overflow-x-auto">
                <table class="eps-history-table min-w-full text-left text-sm">
                    <thead class="bg-slate-900/50 text-xs uppercase text-slate-400">
                        <tr>
                            <th class="px-5 py-3">Reference</th>
                            <th class="px-5 py-3">Gateway reference</th>
                            <th class="px-5 py-3">Paid</th>
                            <th class="px-5 py-3">Balance credit</th>
                            <th class="px-5 py-3">Gateway</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3">Created</th>
                            <th class="px-5 py-3">Verified</th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10 text-slate-200">
                        @forelse ($payments as $payment)
                            <tr>
                                <td data-label="Reference" class="whitespace-nowrap px-5 py-4 font-mono text-xs">{{ $payment->transaction_id }}</td>
                                <td data-label="Gateway reference" class="whitespace-nowrap px-5 py-4 font-mono text-xs">{{ $payment->gateway_transaction_id ?? $payment->gateway_session_id ?? '—' }}</td>
                                <td data-label="Paid" class="whitespace-nowrap px-5 py-4">{{ $payment->currency }} {{ number_format((float) $payment->amount, 2) }}</td>
                                <td data-label="Balance credit" class="whitespace-nowrap px-5 py-4">{{ $payment->balance_currency }} {{ number_format((float) $payment->balance_amount, 2) }}</td>
                                <td data-label="Gateway" class="px-5 py-4 uppercase">{{ $payment->gateway }}</td>
                                <td data-label="Status" class="px-5 py-4">
                                    <span class="eps-status-badge inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold {{ $payment->status->badgeClasses() }}">
                                        {{ $payment->status->label() }}
                                    </span>
                                </td>
                                <td data-label="Created" class="whitespace-nowrap px-5 py-4 text-slate-400">{{ $payment->created_at->format('d M Y, h:i A') }}</td>
                                <td data-label="Verified" class="whitespace-nowrap px-5 py-4 text-slate-400">{{ $payment->verified_at?->format('d M Y, h:i A') ?? '—' }}</td>
                                <td data-label="Action" class="px-5 py-4">
                                    @if ($payment->status !== \App\Enums\PaymentStatus::Successful)
                                        <form method="POST" action="{{ route('balance.payment.status', $payment) }}">
                                            @csrf
                                            <button type="submit" class="eps-check-button whitespace-nowrap text-xs font-semibold text-orange-300 hover:text-orange-200">
                                                Check status
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-5 py-10 text-center text-slate-400">No EPS payment attempts yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($payments->hasPages())
                <div class="eps-pagination border-t border-white/10 px-5 py-4">{{ $payments->links() }}</div>
            @endif
        </section>
    </main>

        @push('scripts')
            <script>
                (() => {
                    const form = document.getElementById('eps-payment-form');
                    const input = document.getElementById('eps-amount');
                    const preview = document.getElementById('eps-credit-preview');
                    const button = document.getElementById('eps-pay-button');
                    const exchangeRate = {{ json_encode($exchangeRate) }};

                    const updatePreview = () => {
                        const amount = Number.parseFloat(input.value || '0');
                        const credit = Number.isFinite(amount) && exchangeRate > 0 ? amount / exchangeRate : 0;
                        preview.textContent = `MVR ${credit.toFixed(2)}`;
                    };

                    document.querySelectorAll('[data-quick-amount]').forEach((quickButton) => {
                        quickButton.addEventListener('click', () => {
                            input.value = quickButton.dataset.quickAmount;
                            updatePreview();
                            input.focus();
                        });
                    });

                    input.addEventListener('input', updatePreview);
                    updatePreview();

                    form.addEventListener('submit', () => {
                        if (button.disabled) return;
                        button.disabled = true;
                        button.querySelector('[data-button-label]').textContent = 'Connecting to EPS…';
                    });
                })();
            </script>
        @endpush
</x-dashboard-layout>
