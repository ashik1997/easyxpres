@php
    $appSetup = DB::table('app_setups')->first();
    $licenseNumber = $appSetup->license_number ?? null;
@endphp

<x-dashboard-layout>
    <x-slot:metaTitle>Dashboard · EasyXpres</x-slot:metaTitle>

    <div class="space-y-3 sm:space-y-6">
        <section class="relative overflow-hidden rounded-[1.25rem] bg-gradient-to-br from-[#005a63] via-[#008b83] to-[#20c7af] px-3 py-3 text-white shadow-xl sm:rounded-[2rem] sm:px-8 sm:py-8">
            <div class="absolute -right-12 -top-16 h-48 w-48 rounded-full border border-white/15 shadow-[0_0_0_32px_rgba(255,255,255,.05),0_0_0_64px_rgba(255,255,255,.035)] sm:-right-16 sm:-top-20 sm:h-64 sm:w-64 sm:shadow-[0_0_0_38px_rgba(255,255,255,.05),0_0_0_76px_rgba(255,255,255,.035)]"></div>
            <div class="relative z-10 flex items-center justify-between gap-3 sm:gap-8">
                <div class="min-w-0 flex-1">
                    <p class="text-[8px] font-semibold uppercase tracking-[.14em] text-teal-50/70 sm:text-xs sm:tracking-[.16em]">Main balance</p>
                    <div class="mt-0.5 flex items-end gap-1.5 sm:mt-1 sm:gap-2">
                        <span class="pb-0.5 text-[9px] font-bold text-teal-50/80 sm:pb-1 sm:text-sm">MVR</span>
                        <span class="truncate text-2xl font-extrabold leading-none tracking-tight sm:text-5xl" data-live-balance>{{ number_format(Auth::user()->account->balance ?? 0, 2) }}</span>
                        <button type="button" onclick="toggleBalanceVisibility(this)" class="flex h-5 w-5 shrink-0 items-center justify-center rounded-md bg-white/10 text-[9px] sm:mb-1 sm:h-8 sm:w-8 sm:rounded-xl sm:text-sm" aria-label="Hide balance"><i class="far fa-eye"></i></button>
                    </div>
                    <div class="mt-0.5 flex items-center gap-1.5 sm:mt-1 sm:gap-2">
                        <p class="text-[8px] font-bold leading-tight text-teal-50/90 sm:text-xs">USER ID <span data-account-id>{{ Auth::user()->account->account_number ?? 'N/A' }}</span></p>
                        <button type="button" onclick="copyAccountId(this)" class="flex h-5 w-5 shrink-0 items-center justify-center rounded-md bg-white/10 text-[8px] transition hover:bg-white/20 sm:h-6 sm:w-6 sm:text-[10px]" aria-label="Copy account ID" title="Copy account ID"><i class="far fa-copy"></i></button>
                    </div>
                </div>

                <div class="flex min-w-0 shrink-0 items-center gap-2 text-right sm:gap-3">
                    <div class="min-w-0">
                        <div class="flex items-center justify-end gap-1.5">
                            <p class="text-[8px] font-semibold leading-tight text-teal-50/80 sm:text-xs">Account info</p>
                            <span class="rounded-full border border-emerald-200/35 bg-emerald-300/15 px-1.5 py-0.5 text-[7px] font-bold leading-none text-emerald-100 sm:px-2 sm:text-[9px]">Active</span>
                        </div>
                        <h2 class="max-w-24 truncate text-sm font-extrabold leading-tight sm:max-w-48 sm:text-xl">{{ Auth::user()->name ?? 'EasyXpres User' }}</h2>
                        <p class="mt-0.5 flex items-center justify-end gap-1 text-[8px] leading-tight text-teal-50/85 sm:mt-1 sm:gap-1.5 sm:text-xs"><i class="fas fa-circle-check text-emerald-300"></i> Verified member</p>
                    </div>
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full border border-white/70 bg-white/15 text-sm backdrop-blur sm:h-14 sm:w-14 sm:border-2 sm:text-xl"><i class="far fa-user"></i></div>
                </div>
            </div>

            <div class="relative z-20 mt-2 grid w-full grid-cols-2 gap-3 rounded-2xl border border-white/70 bg-white/95 p-2 text-slate-800 shadow-xl backdrop-blur sm:mt-6 sm:gap-6 sm:rounded-3xl sm:p-3">
                <div class="flex items-center gap-1.5 rounded-xl border border-rose-100 bg-rose-50 p-2 shadow-sm sm:gap-3 sm:rounded-2xl sm:p-3">
                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-pink-500 to-rose-600 text-[10px] text-white sm:h-10 sm:w-10 sm:rounded-xl sm:text-base"><i class="fas fa-mobile-screen-button"></i></span>
                    <div><p class="text-[7px] font-bold uppercase leading-tight tracking-wide text-slate-500 sm:text-[10px]">Bkash rate</p><p class="text-xs font-extrabold leading-tight sm:text-base">{{ $appSetup->bkash_rate ?? 0 }}</p></div>
                </div>
                <div class="flex items-center gap-1.5 rounded-xl border border-blue-100 bg-blue-50 p-2 shadow-sm sm:gap-3 sm:rounded-2xl sm:p-3">
                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-sky-400 to-blue-600 text-[10px] text-white sm:h-10 sm:w-10 sm:rounded-xl sm:text-base"><i class="fas fa-building-columns"></i></span>
                    <div><p class="text-[7px] font-bold uppercase leading-tight tracking-wide text-slate-500 sm:text-[10px]">Bank rate</p><p class="text-xs font-extrabold leading-tight sm:text-base">{{ $appSetup->bank_rate ?? 0 }}</p></div>
                </div>
            </div>
        </section>

        <section>
    <div class="mb-3 flex items-end justify-between gap-3 sm:mb-4">
        <div>
            <span class="ux-eyebrow">Everyday services</span>
            <h2 class="mt-0.5 text-lg font-extrabold text-slate-900 sm:mt-1 sm:text-xl">
                Quick actions
            </h2>
        </div>

        <span class="text-[10px] font-semibold text-slate-400 sm:text-xs">
            Tap to continue
        </span>
    </div>

    <div class="grid grid-cols-3 gap-2.5 sm:grid-cols-4 sm:gap-3 md:grid-cols-6">

        <!-- Add Balance -->
        <a href="{{ route('balance.add.form') }}"
           aria-label="Add Balance — Pay with Digital Banking or manual request"
           class="eps-dashboard-pay-button dashboard-action-card ux-card-interactive">
            <span class="ux-icon-tile ux-icon-tile--blue">
                <i class="fas fa-wallet"></i>
            </span>
            <span class="text-xs font-bold text-slate-700">Add Balance</span>
        </a>

        <!-- View Packages -->
        <a href="{{ route('app.package.history') }}"
           class="dashboard-action-card ux-card-interactive">
            <span class="ux-icon-tile">
                <i class="fas fa-cube"></i>
            </span>
            <span class="text-xs font-bold text-slate-700">View Packages</span>
        </a>

        <!-- Shop -->
        <a href="{{ route('shop.auto-login') }}"
           class="dashboard-action-card ux-card-interactive w-full"
           aria-label="Open Shop securely"
           title="Open Shop">
            <span class="ux-icon-tile ux-icon-tile--violet">
                <i class="fas fa-shopping-bag"></i>
            </span>
            <span class="text-xs font-bold text-slate-700">Shop</span>
        </a>

        <!-- MLM -->
        <a href="{{ url('https://dailyearn.easyxpres.com/home/login') }}"
           class="dashboard-action-card ux-card-interactive">
            <span class="ux-icon-tile ux-icon-tile--violet">
                <i class="fas fa-coins"></i>
            </span>
            <span class="text-xs font-bold text-slate-700">Daily Earn</span>
        </a>

        <!-- Calling Card -->
        <a href="{{ route('app.calling.card') }}"
           class="dashboard-action-card ux-card-interactive">
            <span class="ux-icon-tile ux-icon-tile--orange">
                <i class="fas fa-phone-card"></i>
            </span>
            <span class="text-xs font-bold text-slate-700">Calling Card</span>
        </a>

        <!-- My Card -->
        <a href="{{ route('my.calling.card') }}"
           class="ux-card-interactive flex min-h-28 flex-col items-center justify-center gap-3 p-3 text-center">
            <span class="ux-icon-tile ux-icon-tile--blue">
                <i class="fas fa-id-card"></i>
            </span>
            <span class="text-xs font-bold text-slate-700">My Card</span>
        </a>

        {{--
        <a href="{{ route('app.vpn') }}"
           class="ux-card-interactive flex min-h-28 flex-col items-center justify-center gap-3 p-3 text-center">
            <span class="ux-icon-tile ux-icon-tile--blue">
                <i class="fas fa-shield-halved"></i>
            </span>
            <span class="text-xs font-bold text-slate-700">Buy VPN</span>
        </a>

        <a href="{{ route('my.vpn') }}"
           class="ux-card-interactive flex min-h-28 flex-col items-center justify-center gap-3 p-3 text-center">
            <span class="ux-icon-tile ux-icon-tile--violet">
                <i class="fas fa-lock"></i>
            </span>
            <span class="text-xs font-bold text-slate-700">My VPN</span>
        </a>
        --}}

        <!-- Maldives Recharge -->
        <a href="{{ route('app.male.recharge.form') }}"
           class="dashboard-action-card ux-card-interactive">
            <span class="ux-icon-tile ux-icon-tile--blue">
                <i class="fas fa-signal"></i>
            </span>
            <span class="text-xs font-bold text-slate-700">Maldives Recharge</span>
        </a>

        <!-- Mobile Banking -->
        <a href="{{ route('app.mobile.banking.view') }}"
           class="dashboard-action-card ux-card-interactive">
            <span class="ux-icon-tile">
                <i class="fas fa-money-bill-transfer"></i>
            </span>
            <span class="text-xs font-bold text-slate-700">Mobile Banking</span>
        </a>

        <!-- iBanking -->
        <a href="{{ route('app.ibanking.form') }}"
           class="dashboard-action-card ux-card-interactive">
            <span class="ux-icon-tile ux-icon-tile--violet">
                <i class="fas fa-building-columns"></i>
            </span>
            <span class="text-xs font-bold text-slate-700">iBanking</span>
        </a>

        <!-- BD Recharge -->
        <a href="{{ route('app.recharge.form') }}"
           class="dashboard-action-card ux-card-interactive">
            <span class="ux-icon-tile ux-icon-tile--violet">
                <i class="fas fa-phone-volume"></i>
            </span>
            <span class="text-xs font-bold text-slate-700">BD Recharge</span>
        </a>

    </div>
</section>

        @if ($licenseNumber)
            <section class="rounded-[1.5rem] border border-teal-100 bg-gradient-to-r from-white to-teal-50/70 p-4 shadow-sm sm:rounded-[2rem] sm:p-5" aria-labelledby="licence-information-title">
                <div class="flex items-start gap-3 sm:items-center sm:gap-4">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-teal-700 text-base text-white shadow-[0_8px_18px_rgba(0,90,99,.18)] sm:h-12 sm:w-12 sm:text-lg">
                        <i class="fas fa-certificate" aria-hidden="true"></i>
                    </span>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                            <h2 id="licence-information-title" class="text-sm font-extrabold text-slate-900 sm:text-base">Licence Information</h2>
                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2 py-0.5 text-[9px] font-extrabold uppercase tracking-wide text-emerald-700 sm:text-[10px]"><i class="fas fa-circle-check" aria-hidden="true"></i> Verified</span>
                        </div>
                        <p class="mt-1 text-[10px] font-bold uppercase tracking-[.12em] text-slate-400 sm:text-xs">Licence number</p>
                        <p class="mt-0.5 break-all font-mono text-sm font-extrabold tracking-wide text-teal-800 sm:text-base">{{ $licenseNumber }}</p>
                    </div>
                </div>
            </section>
        @endif

        <section class="relative grid grid-cols-[minmax(0,1fr)_5rem] items-center gap-3 overflow-hidden rounded-[1.75rem] bg-gradient-to-r from-teal-100 via-cyan-50 to-emerald-100 p-5 shadow-sm sm:flex sm:justify-between sm:gap-8 sm:rounded-[2rem] sm:p-8">
            <div class="relative z-10 max-w-xl">
                <span class="ux-eyebrow">EasyXpres account</span>
                <h2 class="mt-1.5 text-xl font-extrabold text-slate-900 sm:mt-2 sm:text-2xl">Fast · Secure · Easy</h2>
                <p class="mt-1.5 text-xs leading-5 text-slate-600 sm:mt-2 sm:text-sm sm:leading-6">Recharge, payments and digital services—organized in one secure place.</p>
                <a href="{{ route('profile.edit') }}" class="ux-primary-button mt-3 px-4 py-2.5 text-xs sm:mt-5 sm:px-5 sm:py-3 sm:text-sm">Manage Profile <i class="fas fa-arrow-right text-xs"></i></a>
            </div>
            <img src="{{ asset('images/logo.png') }}" alt="EasyXpres" class="relative z-10 m-0 h-auto w-20 object-contain sm:ml-auto sm:w-56">
        </section>

    </div>

    @push('scripts')
        <script>
            let balanceIsVisible = true;

            function toggleBalanceVisibility(button) {
                const balance = document.querySelector('[data-live-balance]');
                if (!balance) return;
                balanceIsVisible = !balanceIsVisible;
                balance.dataset.amount = balance.dataset.amount || balance.textContent.trim();
                balance.textContent = balanceIsVisible ? balance.dataset.amount : '••••••';
                button.querySelector('i').className = balanceIsVisible ? 'far fa-eye' : 'far fa-eye-slash';
            }

            async function copyAccountId(button) {
                const accountId = document.querySelector('[data-account-id]')?.textContent.trim();
                if (!accountId || accountId === 'N/A') return;

                try {
                    await navigator.clipboard.writeText(accountId);
                    const icon = button.querySelector('i');
                    icon.className = 'fas fa-check';
                    button.setAttribute('aria-label', 'Account ID copied');
                    window.setTimeout(() => {
                        icon.className = 'far fa-copy';
                        button.setAttribute('aria-label', 'Copy account ID');
                    }, 1500);
                } catch (error) {
                    window.prompt('Copy account ID:', accountId);
                }
            }

        </script>
    @endpush
</x-dashboard-layout>
