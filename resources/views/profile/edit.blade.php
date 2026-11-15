<x-frontend-layout>
    <x-slot:metaTitle>My Profile · EasyXpres</x-slot:metaTitle>
    <x-slot:header><p class="text-[10px] font-bold uppercase tracking-[.18em] text-teal-100">Account</p><h1 class="truncate text-lg font-extrabold text-white">My Profile</h1></x-slot:header>

    <div class="mb-6"><span class="ux-eyebrow">Account settings</span><h2 class="ux-title mt-1">Profile & security</h2><p class="ux-muted mt-2 text-sm">Keep your personal details and password up to date.</p></div>
    <div class="grid gap-5 lg:grid-cols-2">
        <div class="ux-card p-5 sm:p-7">@include('profile.partials.update-profile-information-form')</div>
        <div class="ux-card p-5 sm:p-7">@include('profile.partials.update-password-form')</div>
        <div class="ux-card border-red-100 p-5 sm:p-7 lg:col-span-2">@include('profile.partials.delete-user-form')</div>
    </div>
</x-frontend-layout>
