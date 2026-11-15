<x-guest-layout>
    <x-slot:metaTitle>Verify email · EasyXpres</x-slot:metaTitle>
    <div class="auth-card text-center">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-3xl bg-teal-50 text-2xl text-teal-700"><i class="far fa-envelope-open"></i></div>
        <span class="ux-eyebrow mt-5 block">One last step</span>
        <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900">Verify your email</h1>
        <p class="mt-3 text-sm leading-6 text-slate-500">We sent a verification link to your email. Open it to activate your account.</p>
        @if (session('status') == 'verification-link-sent')
            <div class="ux-notice mt-5">A fresh verification link has been sent.</div>
        @endif
        <div class="mt-7 flex flex-col gap-3 sm:flex-row">
            <form method="POST" action="{{ route('verification.send') }}" class="flex-1">
                @csrf
                <button type="submit" class="ux-primary-button w-full">Resend Email</button>
            </form>
            <form method="POST" action="{{ route('logout') }}" class="flex-1">
                @csrf
                <button type="submit" class="ux-secondary-button w-full">Sign Out</button>
            </form>
        </div>
    </div>
</x-guest-layout>
