<x-guest-layout>
    <x-slot:metaTitle>Forgot password · EasyXpres</x-slot:metaTitle>

    <div class="auth-card">
        <a href="{{ route('login') }}" class="mb-6 inline-flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-teal-700"><i class="fas fa-arrow-left text-xs"></i> Back to sign in</a>
        <span class="ux-eyebrow block">Account recovery</span>
        <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900">Forgot your password?</h1>
        <p class="mt-2 text-sm leading-6 text-slate-500">Enter your registered email and we’ll send a secure password reset link.</p>

        @if (session('status')) <div class="ux-notice mt-5">{{ session('status') }}</div> @endif

        <form method="POST" action="{{ route('password.email') }}" class="mt-7 space-y-5">
            @csrf
            <div>
                <label for="email" class="ux-field-label">Email address</label>
                <div class="relative">
                    <span class="ux-input-icon"><i class="far fa-envelope"></i></span>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" placeholder="you@example.com" class="ux-input-with-icon">
                </div>
                @error('email') <p class="ux-error"><i class="fas fa-circle-exclamation mt-0.5"></i>{{ $message }}</p> @enderror
            </div>
            <button type="submit" class="ux-primary-button w-full">Email Reset Link <i class="far fa-paper-plane text-xs"></i></button>
        </form>
    </div>
</x-guest-layout>
