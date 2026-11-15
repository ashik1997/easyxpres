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
            @isset($header)
                {!! $header !!}
            @else
                <p class="text-[10px] font-bold uppercase tracking-[.18em] text-teal-100">EasyXpres</p>
                <h1 class="truncate text-lg font-extrabold text-white">Your digital services</h1>
            @endisset
        </x-user-app-header>

        @if (!empty($app?->marquee))
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

        <main class="user-content">
            {{ $slot }}
            <p class="mt-10 text-center text-xs text-slate-400">© {{ date('Y') }} EasyXpres · Fast · Secure · Easy</p>
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

        function openInputLink() {
            const input = document.getElementById('cardLink');
            const link = input ? input.value.trim() : '';
            link ? window.open(link, '_blank', 'noopener') : toastr.warning('App link is not available.');
        }
    </script>
    @stack('scripts')
</body>
</html>
