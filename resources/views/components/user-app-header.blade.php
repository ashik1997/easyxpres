@props(['showBack' => false])

<header class="user-topbar">
    <div class="user-topbar-inner mx-auto flex min-h-[54px] max-w-6xl items-center justify-between gap-2 px-3 sm:min-h-[72px] sm:gap-3 sm:px-6">
        <div class="flex min-w-0 items-center gap-2 sm:gap-3">
            @if ($showBack)
                <a
                    href="{{ route('dashboard') }}"
                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white/10 text-sm text-white transition hover:bg-white/20 sm:h-10 sm:w-10 sm:rounded-2xl sm:text-base"
                    aria-label="Back to dashboard"
                >
                    <i class="fas fa-arrow-left"></i>
                </a>
            @else
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-orange-400 to-orange-600 shadow-lg sm:h-11 sm:w-11 sm:rounded-2xl" aria-label="EasyXpres">
                    <span class="-skew-x-6 text-sm font-black italic tracking-[-.08em] text-white sm:text-base">EX</span>
                </div>
            @endif

            <div class="min-w-0">
                {{ $slot }}
            </div>
        </div>

        <div class="relative shrink-0" x-data="{ open: false }" @keydown.escape.window="open = false">
            <button
                type="button"
                @click="open = !open"
                class="flex h-8 items-center gap-1 rounded-lg border border-white/20 bg-white/10 px-2 text-xs font-bold text-white transition hover:bg-white/20 sm:h-10 sm:gap-2 sm:rounded-2xl sm:px-3 sm:text-sm"
                :aria-expanded="open"
                aria-label="Open account menu"
            >
                <span class="flex h-5 w-5 items-center justify-center rounded-md bg-white/15 text-[11px] sm:h-7 sm:w-7 sm:rounded-xl sm:text-sm">
                    <i class="far fa-user"></i>
                </span>
                <span class="hidden max-w-28 truncate sm:block">{{ Auth::user()->name ?? 'Account' }}</span>
                <i class="fas fa-chevron-down text-[10px] opacity-70"></i>
            </button>

            <div
                x-cloak
                x-show="open"
                @click.outside="open = false"
                x-transition.origin.top.right
                class="absolute right-0 mt-2 w-56 overflow-hidden rounded-2xl border border-slate-200 bg-white p-2 text-slate-700 shadow-2xl"
            >
                <div class="border-b border-slate-100 px-3 py-2">
                    <p class="truncate text-sm font-extrabold text-slate-800">{{ Auth::user()->name ?? 'EasyXpres User' }}</p>
                    <p class="mt-0.5 truncate text-xs text-slate-500">{{ Auth::user()->account->account_number ?? 'Account' }}</p>
                </div>
                <a href="{{ route('profile.edit') }}" class="mt-1 flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold hover:bg-teal-50 hover:text-teal-700">
                    <i class="far fa-user-circle w-4"></i> My Profile
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left text-sm font-semibold text-red-600 hover:bg-red-50">
                        <i class="fas fa-sign-out-alt w-4"></i> Sign Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
