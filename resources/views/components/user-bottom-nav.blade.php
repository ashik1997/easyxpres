<nav class="user-bottom-nav" aria-label="Primary navigation">
    <a href="{{ route('dashboard') }}" class="user-bottom-link {{ request()->routeIs('dashboard') ? 'is-active' : '' }}">
        <i class="fas fa-home text-lg"></i>
        <span>Home</span>
    </a>
    <a href="{{ route('app.package.history') }}" class="user-bottom-link {{ request()->routeIs('app.package.*') ? 'is-active' : '' }}">
        <i class="fas fa-cube text-lg"></i>
        <span>Packages</span>
    </a>
    <a href="{{ route('balance.add.form') }}" class="user-bottom-link user-bottom-link--action {{ request()->routeIs('balance.add.form', 'balance.payment.*') ? 'is-active' : '' }}" aria-label="Add balance">
        <i class="fas fa-plus text-xl"></i>
        <span class="sr-only">Add Balance</span>
    </a>
    <a href="{{ route('app.balance.history') }}" class="user-bottom-link {{ request()->routeIs('app.balance.history') ? 'is-active' : '' }}">
        <i class="fas fa-history text-lg"></i>
        <span>History</span>
    </a>
    <a href="{{ route('profile.edit') }}" class="user-bottom-link {{ request()->routeIs('profile.*') ? 'is-active' : '' }}">
        <i class="far fa-user text-lg"></i>
        <span>Profile</span>
    </a>
</nav>
