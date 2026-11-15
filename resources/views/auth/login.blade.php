<x-guest-layout>
    <x-slot:metaTitle>Sign in · EasyXpres</x-slot:metaTitle>

    <div class="auth-card">
        <div class="mb-7">
            <span class="ux-eyebrow">Welcome back</span>
            <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900">Sign in to EasyXpres</h1>
            <p class="mt-2 text-sm leading-6 text-slate-500">Use your registered phone number to continue.</p>
        </div>

        @if (session('status'))
            <div class="ux-notice mb-5">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <div>
                <label for="phone" class="ux-field-label">Phone number</label>
                <div class="relative">
                    <span class="ux-input-icon"><i class="fas fa-phone"></i></span>
                    <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" required autofocus autocomplete="tel" placeholder="Enter your phone number" class="ux-input-with-icon">
                </div>
                @error('phone') <p class="ux-error"><i class="fas fa-circle-exclamation mt-0.5"></i>{{ $message }}</p> @enderror
            </div>

            <div x-data="{ show: false }">
                <div class="flex items-center justify-between gap-3">
                    <label for="password" class="ux-field-label">Password</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="mb-2 text-xs font-bold text-teal-700 hover:text-teal-900">Forgot password?</a>
                    @endif
                </div>
                <div class="relative">
                    <span class="ux-input-icon"><i class="fas fa-lock"></i></span>
                    <input id="password" :type="show ? 'text' : 'password'" name="password" required autocomplete="current-password" placeholder="Enter your password" class="ux-input-with-icon pr-12">
                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex w-12 items-center justify-center text-slate-400 hover:text-teal-700" :aria-label="show ? 'Hide password' : 'Show password'">
                        <i class="far" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                    </button>
                </div>
                @error('password') <p class="ux-error"><i class="fas fa-circle-exclamation mt-0.5"></i>{{ $message }}</p> @enderror
            </div>

            <label class="flex cursor-pointer items-center gap-3 text-sm font-medium text-slate-600">
                <input type="checkbox" name="remember" class="rounded border-slate-300 text-teal-600 focus:ring-teal-500">
                Keep me signed in
            </label>

            <button type="submit" class="ux-primary-button w-full">
                Sign In <i class="fas fa-arrow-right text-xs"></i>
            </button>
        </form>

        @if (Route::has('register'))
            <p class="mt-6 border-t border-slate-100 pt-6 text-center text-sm text-slate-500">
                New to EasyXpres?
                <a href="{{ route('register') }}" class="font-extrabold text-teal-700 hover:text-teal-900">Create an account</a>
            </p>
        @endif

        @if ($app && $app->play_game_status == 1)
            <a href="{{ $app->play_game_link ?? '#' }}" target="_blank" rel="noopener noreferrer" class="ux-secondary-button mt-4 w-full">
                <i class="fas fa-gamepad text-orange-500"></i> Play Game
            </a>
        @endif

        @if (file_exists(public_path('apk/Easyxpres.apk')))
            <a href="{{ asset('apk/Easyxpres.apk') }}" download class="mt-4 flex items-center justify-center gap-2 text-xs font-bold text-slate-500 hover:text-teal-700">
                <i class="fab fa-android text-base text-emerald-500"></i> Download Android App
            </a>
        @endif
    </div>
</x-guest-layout>
