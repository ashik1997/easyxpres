<x-guest-layout>
    <x-slot:metaTitle>Confirm password · EasyXpres</x-slot:metaTitle>
    <div class="auth-card">
        <span class="ux-eyebrow">Protected area</span>
        <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900">Confirm your password</h1>
        <p class="mt-2 text-sm leading-6 text-slate-500">For your security, confirm your password before continuing.</p>
        <form method="POST" action="{{ route('password.confirm') }}" class="mt-7 space-y-5">
            @csrf
            <div>
                <label for="password" class="ux-field-label">Password</label>
                <input id="password" type="password" name="password" required autocomplete="current-password" class="ux-input">
                @error('password') <p class="ux-error"><i class="fas fa-circle-exclamation mt-0.5"></i>{{ $message }}</p> @enderror
            </div>
            <button type="submit" class="ux-primary-button w-full">Confirm <i class="fas fa-arrow-right text-xs"></i></button>
        </form>
    </div>
</x-guest-layout>
