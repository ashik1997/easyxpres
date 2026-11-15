<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <x-webview-safe-area />
    <title>{{ $metaTitle ?? config('app.name', 'EasyXpres') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet">
    <link href="{{ asset('backend/plugins/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-page">
    <main class="auth-shell">
        <aside class="auth-brand-panel">
            <div class="relative z-10">
                <img src="{{ asset('images/logo.png') }}" alt="EasyXpres" class="h-auto w-64 object-contain">
            </div>
            <div class="relative z-10 max-w-md pb-10">
                <span class="inline-flex rounded-full border border-white/20 bg-white/10 px-4 py-2 text-xs font-bold uppercase tracking-[.18em]">One account, more possibilities</span>
                <h2 class="mt-6 text-4xl font-extrabold leading-tight">Fast, secure and simple digital services.</h2>
                <p class="mt-4 text-base leading-7 text-teal-50/85">Manage balance, packages, recharge and transfers from one clean EasyXpres account.</p>
                <div class="mt-8 grid grid-cols-3 gap-3 text-center text-xs font-semibold text-white/80">
                    <div class="rounded-2xl border border-white/15 bg-white/10 p-3"><i class="fas fa-bolt mb-2 block text-lg"></i>Fast</div>
                    <div class="rounded-2xl border border-white/15 bg-white/10 p-3"><i class="fas fa-shield-alt mb-2 block text-lg"></i>Secure</div>
                    <div class="rounded-2xl border border-white/15 bg-white/10 p-3"><i class="fas fa-mobile-alt mb-2 block text-lg"></i>Easy</div>
                </div>
            </div>
        </aside>

        <section class="auth-content">
            <img src="{{ asset('images/logo.png') }}" alt="EasyXpres" class="auth-logo-mobile">
            {{ $slot }}

            <div class="mx-auto mt-7 flex items-center justify-center gap-3">
                @if (!empty($app?->facebook))
                    <a href="{{ $app->facebook }}" target="_blank" rel="noopener noreferrer" class="auth-social-link" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                @endif
                @if (!empty($app?->youtube))
                    <a href="{{ $app->youtube }}" target="_blank" rel="noopener noreferrer" class="auth-social-link" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                @endif
                @if (!empty($app?->telegram))
                    <a href="{{ $app->telegram }}" target="_blank" rel="noopener noreferrer" class="auth-social-link" aria-label="Telegram"><i class="fab fa-telegram-plane"></i></a>
                @endif
            </div>
            <p class="mt-5 text-center text-xs text-slate-400">© {{ date('Y') }} EasyXpres. All rights reserved.</p>
        </section>
    </main>
    <x-whatsapp-support :whatsapp="$app?->whatsapp" />
</body>
</html>
