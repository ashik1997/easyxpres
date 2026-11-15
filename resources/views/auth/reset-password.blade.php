<x-guest-layout>
    <x-slot:metaTitle>Reset password · EasyXpres</x-slot:metaTitle>

    <div class="auth-card">
        <span class="ux-eyebrow">Secure your account</span>
        <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900">Choose a new password</h1>
        <p class="mt-2 text-sm leading-6 text-slate-500">Use a strong password you haven’t used before.</p>

        <form method="POST" action="{{ route('password.store') }}" class="mt-7 space-y-5">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div>
                <label for="email" class="ux-field-label">Email address</label>
                <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username" class="ux-input">
                @error('email') <p class="ux-error"><i class="fas fa-circle-exclamation mt-0.5"></i>{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="password" class="ux-field-label">New password</label>
                <input id="password" type="password" name="password" required autocomplete="new-password" class="ux-input">
                @error('password') <p class="ux-error"><i class="fas fa-circle-exclamation mt-0.5"></i>{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="password_confirmation" class="ux-field-label">Confirm new password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="ux-input">
            </div>
            <button type="submit" class="ux-primary-button w-full">Reset Password <i class="fas fa-check text-xs"></i></button>
        </form>
    </div>
</x-guest-layout>
