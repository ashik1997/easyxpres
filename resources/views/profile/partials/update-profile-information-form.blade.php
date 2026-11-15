<section>
    <header><span class="ux-icon-tile"><i class="far fa-user"></i></span><h3 class="mt-4 text-xl font-extrabold text-slate-900">Profile information</h3><p class="mt-1 text-sm leading-6 text-slate-500">Update your name and email address.</p></header>
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">@csrf</form>
    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-5">
        @csrf @method('patch')
        <div><label for="name" class="ux-field-label">Name</label><input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" class="ux-input">@foreach ($errors->get('name') as $message)<p class="ux-error">{{ $message }}</p>@endforeach</div>
        <div><label for="email" class="ux-field-label">Email</label><input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username" class="ux-input">@foreach ($errors->get('email') as $message)<p class="ux-error">{{ $message }}</p>@endforeach
            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())<div class="ux-notice mt-3">Your email is unverified. <button form="send-verification" class="font-extrabold underline">Resend verification email</button></div>@if (session('status') === 'verification-link-sent')<p class="mt-2 text-sm font-bold text-emerald-600">A new verification link was sent.</p>@endif @endif
        </div>
        <div class="flex items-center gap-3"><button type="submit" class="ux-primary-button">Save Profile</button>@if (session('status') === 'profile-updated')<p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2500)" class="text-sm font-bold text-emerald-600">Saved</p>@endif</div>
    </form>
</section>
