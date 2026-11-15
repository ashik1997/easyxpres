<x-guest-layout>
    <x-slot:metaTitle>Create account · EasyXpres</x-slot:metaTitle>

    <div class="auth-card max-w-xl">
        <div class="mb-7">
            <span class="ux-eyebrow">Join EasyXpres</span>
            <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900">Create your account</h1>
            <p class="mt-2 text-sm leading-6 text-slate-500">A few details and you’ll be ready to get started.</p>
        </div>

        <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" class="grid gap-5 sm:grid-cols-2">
            @csrf

            <div class="sm:col-span-2">
                <label for="name" class="ux-field-label">Full name</label>
                <div class="relative">
                    <span class="ux-input-icon"><i class="far fa-user"></i></span>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Your full name" class="ux-input-with-icon">
                </div>
                @error('name') <p class="ux-error"><i class="fas fa-circle-exclamation mt-0.5"></i>{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="phone" class="ux-field-label">Phone number</label>
                <div class="relative">
                    <span class="ux-input-icon"><i class="fas fa-phone"></i></span>
                    <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" required autocomplete="tel" placeholder="Phone number" class="ux-input-with-icon">
                </div>
                @error('phone') <p class="ux-error"><i class="fas fa-circle-exclamation mt-0.5"></i>{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="email" class="ux-field-label">Email <span class="font-medium text-slate-400">(optional)</span></label>
                <div class="relative">
                    <span class="ux-input-icon"><i class="far fa-envelope"></i></span>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" placeholder="you@example.com" class="ux-input-with-icon">
                </div>
                @error('email') <p class="ux-error"><i class="fas fa-circle-exclamation mt-0.5"></i>{{ $message }}</p> @enderror
            </div>

            <div x-data="{ show: false }">
                <label for="password" class="ux-field-label">Password</label>
                <div class="relative">
                    <span class="ux-input-icon"><i class="fas fa-lock"></i></span>
                    <input id="password" :type="show ? 'text' : 'password'" name="password" required autocomplete="new-password" placeholder="Create a password" class="ux-input-with-icon pr-12">
                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex w-12 items-center justify-center text-slate-400 hover:text-teal-700" :aria-label="show ? 'Hide password' : 'Show password'"><i class="far" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i></button>
                </div>
                @error('password') <p class="ux-error"><i class="fas fa-circle-exclamation mt-0.5"></i>{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password_confirmation" class="ux-field-label">Confirm password</label>
                <div class="relative">
                    <span class="ux-input-icon"><i class="fas fa-shield-alt"></i></span>
                    <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Repeat your password" class="ux-input-with-icon">
                </div>
            </div>

            <input type="hidden" name="register_fee" value="0">

            <div class="sm:col-span-2">
                <button type="submit" class="ux-primary-button w-full">Create Account <i class="fas fa-arrow-right text-xs"></i></button>
            </div>
        </form>

        <p class="mt-6 border-t border-slate-100 pt-6 text-center text-sm text-slate-500">
            Already registered?
            <a href="{{ route('login') }}" class="font-extrabold text-teal-700 hover:text-teal-900">Sign in</a>
        </p>
    </div>
</x-guest-layout>
