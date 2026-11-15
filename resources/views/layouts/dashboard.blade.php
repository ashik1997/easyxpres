<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover, interactive-widget=resizes-content">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <x-webview-safe-area />
    <title>{{ $metaTitle ?? config('app.name', 'EasyXpres') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet">
    <link href="{{ asset('backend/plugins/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="user-page">
    <div class="user-shell">
        <x-user-app-header :show-back="!request()->routeIs('dashboard')">
            @if (request()->routeIs('dashboard'))
                <p class="text-[8px] font-bold uppercase tracking-[.15em] text-teal-100 sm:text-[10px] sm:tracking-[.18em]">Welcome back</p>
                <h1 class="truncate text-[15px] font-extrabold leading-tight text-white sm:text-lg">{{ Auth::user()->name ?? 'EasyXpres User' }}</h1>
            @elseif (isset($header))
                {!! $header !!}
            @else
                <p class="text-[10px] font-bold uppercase tracking-[.18em] text-teal-100">EasyXpres</p>
                <h1 class="truncate text-lg font-extrabold text-white">{{ $metaTitle ?? 'Account' }}</h1>
            @endif
        </x-user-app-header>

        @if (request()->routeIs('dashboard') && !empty($app?->marquee))
            <aside class="app-notice" aria-label="Latest notice">
                <div class="app-notice-inner">
                    <div class="app-notice-badge" aria-hidden="true">
                        <span class="app-notice-icon"><i class="fas fa-bullhorn"></i></span>
                        <span>Notice</span>
                    </div>
                    <div class="app-notice-viewport">
                        <div class="marquee-track">
                            <div class="app-notice-message">{!! $app->marquee !!}</div>
                        </div>
                    </div>
                </div>
            </aside>
        @endif

        <main class="user-content {{ request()->routeIs('dashboard') ? 'user-content--dashboard' : '' }}">
            {{ $slot }}
        </main>

        <x-user-bottom-nav />
    </div>

    <x-whatsapp-support :whatsapp="$app?->whatsapp" :avoid-bottom-nav="true" />

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        window.toastr && Object.assign(toastr.options, {
            closeButton: true,
            progressBar: true,
            positionClass: 'toast-top-center',
            timeOut: 4000
        });

        @if (session('success')) toastr.success(@json(session('success'))); @endif
        @if (session('error')) toastr.error(@json(session('error'))); @endif
        @if (session('info')) toastr.info(@json(session('info'))); @endif
        @if (session('warning')) toastr.warning(@json(session('warning'))); @endif

        (() => {
            const balanceElements = document.querySelectorAll('[data-live-balance]');
            if (!balanceElements.length) return;

            let requestInProgress = false;
            async function refreshBalance() {
                if (document.hidden || requestInProgress) return;
                requestInProgress = true;
                try {
                    const response = await fetch(@json(route('balance.current')), {
                        headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        cache: 'no-store',
                        credentials: 'same-origin'
                    });
                    if (!response.ok) return;
                    const data = await response.json();
                    if (typeof data.formatted_balance === 'string') {
                        balanceElements.forEach((element) => element.textContent = data.formatted_balance);
                    }
                } catch (error) {
                    // Keep the latest visible balance when the connection is unavailable.
                } finally {
                    requestInProgress = false;
                }
            }
            refreshBalance();
            window.setInterval(refreshBalance, 5000);
            window.addEventListener('focus', refreshBalance);
            document.addEventListener('visibilitychange', refreshBalance);
        })();
    </script>
    @stack('scripts')
</body>
</html>
