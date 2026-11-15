<section>
    <header><span class="ux-icon-tile ux-icon-tile--blue"><i class="fas fa-lock"></i></span><h3 class="mt-4 text-xl font-extrabold text-slate-900">Update password</h3><p class="mt-1 text-sm leading-6 text-slate-500">Use a long, unique password to protect your account.</p></header>
    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-5">
        @csrf @method('put')
        <div><label for="update_password_current_password" class="ux-field-label">Current password</label><input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password" class="ux-input">@foreach ($errors->updatePassword->get('current_password') as $message)<p class="ux-error">{{ $message }}</p>@endforeach</div>
        <div><label for="update_password_password" class="ux-field-label">New password</label><input id="update_password_password" name="password" type="password" autocomplete="new-password" class="ux-input">@foreach ($errors->updatePassword->get('password') as $message)<p class="ux-error">{{ $message }}</p>@endforeach</div>
        <div><label for="update_password_password_confirmation" class="ux-field-label">Confirm password</label><input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" class="ux-input">@foreach ($errors->updatePassword->get('password_confirmation') as $message)<p class="ux-error">{{ $message }}</p>@endforeach</div>
        <div class="flex items-center gap-3"><button type="submit" class="ux-primary-button">Update Password</button>@if (session('status') === 'password-updated')<p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2500)" class="text-sm font-bold text-emerald-600">Saved</p>@endif</div>
    </form>
</section>
