<x-guest-layout>
    <x-slot:metaTitle>Account pending · EasyXpres</x-slot:metaTitle>
    <div class="auth-card text-center">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-3xl bg-amber-50 text-2xl text-amber-600"><i class="far fa-clock"></i></div>
        <span class="ux-eyebrow mt-5 block">Account review</span>
        <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900">Approval in progress</h1>
        <p class="mt-3 text-sm leading-6 text-slate-500">Your account has been created and is waiting for approval. Please check again shortly.</p>
        <form method="POST" action="{{ route('logout') }}" class="mt-7">
            @csrf
            <button type="submit" class="ux-secondary-button w-full">Return to Sign In</button>
        </form>
    </div>
</x-guest-layout>
