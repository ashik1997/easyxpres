@php
    $isDashboard = request()->routeIs('admin.dashboard');
    $isUsers = request()->routeIs('admin.users.*', 'admin.balance.eps');
    $isCallingCards = request()->routeIs('admin.calling_card.*', 'admin.setup.link_icon', 'admin.setup.link_icon.update');
    $isVpn = request()->routeIs('admin.vpn_names.*', 'admin.vpns.*');
    $isBalance = request()->routeIs('admin.balance.*') && !request()->routeIs('admin.balance.eps');
    $isPackages = request()->routeIs('admin.packages.*', 'admin.subpackages.*', 'admin.package.*', 'admin.package_orders.*', 'admin.admin.package.orders');
    $isRecharges = request()->routeIs('admin.recharges.*');
    $isMaleRecharges = request()->routeIs('admin.male.recharges.*', 'admin.mali.operators.*');
    $isMobileBanking = request()->routeIs('admin.mobile.banking.*', 'admin.mobile_banking_orders.*');
    $isIBanking = request()->routeIs('admin.ibanking.*', 'admin.iBanking.*', 'admin.ibanking_orders.*');
    $isAppSetup = (request()->routeIs('admin.setup.*') && !request()->routeIs('admin.setup.link_icon', 'admin.setup.link_icon.update'))
        || request()->routeIs('admin.notification.*', 'admin.change.password');
    $UserCount = $sidebarCounts['users'] ?? DB::table('users')->where('status', 'pending')->count();
    $callingCount = $sidebarCounts['calling_cards'] ?? DB::table('calling_cards')->where('buy_now_status', '0')->count();
    $vpnCount = $sidebarCounts['vpns'] ?? DB::table('vpns')->where('status', 'active')->where('buy_now_status', false)->whereDate('expire_date', '>=', today())->count();
    $BalanceCount = $sidebarCounts['balance'] ?? DB::table('transactions')->where('status', 'pending')->count();
    $PackageCount = $sidebarCounts['packages'] ?? DB::table('package_orderls')->where('status', 'pending')->count();
    $RechargeCount = $sidebarCounts['bd_recharge'] ?? DB::table('recharges')->where('status', 'pending')->count();
    $maleRechargeCount = $sidebarCounts['male_recharge'] ?? DB::table('male_recharges')->where('status', 'pending')->count();
    $mobileBankingOrder = $sidebarCounts['mobile_banking'] ?? DB::table('mobile_banking_orders')->where('status', 'pending')->count();
    $iBank = $sidebarCounts['ibanking'] ?? DB::table('ibanking_orders')->where('status', 'pending')->count();
    $appSetupExists = DB::table('app_setups')->where('id', 1)->exists();
@endphp

<div class="sidebar">
    <!-- Sidebar user panel (optional) -->


    <!-- SidebarSearch Form -->



    <div class="sidebar-context">
        <span class="sidebar-context-icon" aria-hidden="true"><i class="fas fa-user-shield"></i></span>
        <span class="sidebar-context-copy">
            <strong>Administrator</strong>
            <span><i class="fas fa-circle text-success mr-1" style="font-size: 6px"></i> Secure workspace</span>
        </span>
    </div>

    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul
            id="sidebar-menu"
            class="nav nav-pills nav-sidebar flex-column"
            data-widget="treeview"
            role="menu"
            data-accordion="false"
        >

            <li class="nav-header">Overview</li>

            <li class="nav-item">
                <a
                    href="{{ route('admin.dashboard') }}"
                    class="nav-link {{ $isDashboard ? 'active' : '' }}"
                    @if ($isDashboard) aria-current="page" @endif
                >
                    <i class="nav-icon fas fa-th"></i>
                    <p>
                        Dashboard
                        <!-- <span class="right badge badge-danger">New</span> -->
                    </p>
                </a>
            </li>

            <li class="nav-header">Management</li>

            <li class="nav-item {{ $isUsers ? 'menu-open' : '' }}">
                <a
                    href="#"
                    class="nav-link {{ $isUsers ? 'active' : '' }}"
                >

                    <i class="nav-icon fas fa-users text-success"></i>

                    <sup class="badge badge-danger ">
                        {{ $UserCount ?? 0 }}
                    </sup>

                    <p>
                        Users Management
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>



                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a
                            href="{{ route('admin.users.list') }}"
                            class="nav-link {{ request()->routeIs('admin.users.list') ? 'active' : '' }}"
                        >
                            <i class="far fa-circle nav-icon"></i>
                            <p>List Users

                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a
                            href="{{ route('admin.balance.eps') }}"
                            class="nav-link {{ request()->routeIs('admin.balance.eps') ? 'active' : '' }}"
                        >
                            <i class="far fa-circle nav-icon"></i>
                            <p>EPS Payments</p>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item {{ $isCallingCards ? 'menu-open' : '' }}">
                <a
                    href="#"
                    class="nav-link {{ $isCallingCards ? 'active' : '' }}"
                >

                    <i class="nav-icon fas fa-id-card text-primary"></i>

                    <sup class="badge badge-danger">
                        {{ $callingCount ?? 0 }}
                    </sup>

                    <p>
                        Calling Card Management
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>

                <ul class="nav nav-treeview">

                    <li class="nav-item">
                        <a
                            href="{{ route('admin.setup.link_icon') }}"
                            class="nav-link {{ request()->routeIs('admin.setup.link_icon') ? 'active' : '' }}"
                        >
                            <i class="far fa-circle nav-icon"></i>
                            <p>Setup Link/Icon</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a
                            href="{{ route('admin.calling_card.create') }}"
                            class="nav-link {{ request()->routeIs('admin.calling_card.create') ? 'active' : '' }}"
                        >
                            <i class="far fa-circle nav-icon"></i>
                            <p>Create Calling Card</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a
                            href="{{ route('admin.calling_card.index') }}"
                            class="nav-link {{ request()->routeIs('admin.calling_card.index', 'admin.calling_card.edit') ? 'active' : '' }}"
                        >
                            <i class="far fa-circle nav-icon"></i>
                            <p>List Calling Card</p>
                        </a>
                    </li>

                    <!-- <li class="nav-item">
                        <a href=""
                            class="nav-link ">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Buy List Calling Card</p>
                        </a>
                    </li> -->



                </ul>
            </li>


            <li class="nav-item {{ $isVpn ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ $isVpn ? 'active' : '' }}">
                    <i class="nav-icon fas fa-shield-alt text-info"></i>
                    <sup class="badge badge-danger">{{ $vpnCount }}</sup>
                    <p>VPN Management <i class="fas fa-angle-left right"></i></p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('admin.vpn_names.index') }}" class="nav-link {{ request()->routeIs('admin.vpn_names.*') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i><p>VPN Names</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.vpns.create') }}" class="nav-link {{ request()->routeIs('admin.vpns.create') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i><p>Create VPN Account</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.vpns.index') }}" class="nav-link {{ request()->routeIs('admin.vpns.index', 'admin.vpns.edit') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i><p>VPN Account List</p>
                        </a>
                    </li>
                </ul>
            </li>


            <li class="nav-item {{ $isBalance ? 'menu-open' : '' }}">
                <a
                    href="#"
                    class="nav-link {{ $isBalance ? 'active' : '' }}"
                >

                    <i class="nav-icon fas fa-wallet text-warning"></i>

                    <sup class="badge badge-danger ">
                        {{ $BalanceCount ?? 0 }}
                    </sup>

                    <p>
                        Balance Management
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>



                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a
                            href="{{ route('admin.balance.pending') }}"
                            class="nav-link {{ request()->routeIs('admin.balance.pending') ? 'active' : '' }}"
                        >
                            <i class="far fa-circle nav-icon"></i>
                            <p>List Balance

                            </p>
                        </a>



                    </li>
                    <li class="nav-item">
                        <a
                            href="{{ route('admin.balance.history') }}"
                            class="nav-link {{ request()->routeIs('admin.balance.history') ? 'active' : '' }}"
                        >
                            <i class="fas fa-history nav-icon"></i>
                            <p>Balance History</p>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- // Package Management -->
            <li
                class="nav-item {{ $isPackages ? 'menu-open' : '' }}">
                <a
                    href="#"
                    class="nav-link {{ $isPackages ? 'active' : '' }}"
                >

                    <i class="nav-icon fas fa-box-open text-info"></i>

                    <sup class="badge badge-danger ">
                        {{ $PackageCount ?? 0 }}
                    </sup>

                    <p>
                        Package Management
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>

                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a
                            href="{{ route('admin.packages.list') }}"
                            class="nav-link {{ request()->routeIs('admin.packages.list', 'admin.packages.create', 'admin.packages.edit') ? 'active' : '' }}"
                        >
                            <i class="far fa-circle nav-icon"></i>
                            <p>Packages List </p>
                        </a>



                        <a
                            href="{{ route('admin.subpackages.list') }}"
                            class="nav-link {{ request()->routeIs('admin.subpackages.list', 'admin.subpackages.create', 'admin.subpackages.edit') ? 'active' : '' }}"
                        >
                            <i class="far fa-circle nav-icon"></i>
                            <p>Sub Package List</p>
                        </a>




                        <a
                            href="{{ route('admin.package.orders') }}"
                            class="nav-link {{ request()->routeIs('admin.package.orders', 'admin.package_orders.*', 'admin.admin.package.orders') ? 'active' : '' }}"
                        >
                            <i class="far fa-circle nav-icon"></i>
                            <p>Package Orders

                            </p>
                        </a>

                </ul>
            </li>

            <!-- // Recharge Management -->
            <li class="nav-item {{ $isRecharges ? 'menu-open' : '' }}">
                <a
                    href="#"
                    class="nav-link {{ $isRecharges ? 'active' : '' }}"
                >

                    <i class="nav-icon fas fa-mobile-alt text-primary"></i>

                    <sup class="badge badge-danger ">
                        {{ $RechargeCount ?? 0 }}
                    </sup>

                    <p>
                        BD Recharge Management
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>


                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a
                            href="{{ route('admin.recharges.pending') }}"
                            class="nav-link {{ request()->routeIs('admin.recharges.pending') ? 'active' : '' }}"
                        >
                            <i class="far fa-circle nav-icon"></i>
                            <p>List BD Recharges

                            </p>
                        </a>



                    </li>
                </ul>

            </li>

            <!-- // Male Recharge Management -->
            <li
                class="nav-item {{ $isMaleRecharges ? 'menu-open' : '' }}">
                <a
                    href="#"
                    class="nav-link {{ $isMaleRecharges ? 'active' : '' }}"
                >

                    <i class="nav-icon fas fa-mobile-alt text-warning"></i>

                    <sup class="badge badge-danger ">
                        {{ $maleRechargeCount ?? 0 }}
                    </sup>

                    <p>
                        Male Recharge Management
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>


                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a
                            href="{{ route('admin.mali.operators.index') }}"
                            class="nav-link {{ request()->routeIs('admin.mali.operators.*') ? 'active' : '' }}"
                        >
                            <i class="far fa-circle nav-icon"></i>
                            <p>Male Operators</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a
                            href="{{ route('admin.male.recharges.pending') }}"
                            class="nav-link {{ request()->routeIs('admin.male.recharges.pending') ? 'active' : '' }}"
                        >
                            <i class="far fa-circle nav-icon"></i>
                            <p>List Male Recharges
                            </p>
                        </a>
                    </li>
                </ul>

            </li>

            <!-- // mobile Banking Management -->
            <li class="nav-item {{ $isMobileBanking ? 'menu-open' : '' }}">
                <a
                    href="#"
                    class="nav-link {{ $isMobileBanking ? 'active' : '' }}"
                >

                    <i class="nav-icon fas fa-wallet text-warning"></i>

                    <sup class="badge badge-danger ">
                        {{ $mobileBankingOrder ?? 0 }}
                    </sup>

                    <p>
                        Mobile Banking Management
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>


                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a
                            href="{{ route('admin.mobile.banking.list') }}"
                            class="nav-link {{ request()->routeIs('admin.mobile.banking.list', 'admin.mobile.banking.create', 'admin.mobile.banking.edit') ? 'active' : '' }}"
                        >
                            <i class="far fa-circle nav-icon"></i>
                            <p>List Mobile Banking
                            </p>
                        </a>

                        <a
                            href="{{ route('admin.mobile.banking.orders.list') }}"
                            class="nav-link {{ request()->routeIs('admin.mobile.banking.orders.list', 'admin.mobile_banking_orders.*') ? 'active' : '' }}"
                        >
                            <i class="far fa-circle nav-icon"></i>
                            <p>Order Mobile Banking
                            </p>
                        </a>

                    </li>
                </ul>

            </li>

            <!-- // iBanking Management -->
            <li class="nav-item {{ $isIBanking ? 'menu-open' : '' }}">
                <a
                    href="#"
                    class="nav-link {{ $isIBanking ? 'active' : '' }}"
                >

                    <i class="nav-icon fas fa-university text-success"></i>

                    <sup class="badge badge-danger ">
                        {{ $iBank ?? 0 }}
                    </sup>


                    <p>
                        iBanking Management
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>


                <ul class="nav nav-treeview">

                    <li class="nav-item">
                        <a
                            href="{{ route('admin.ibanking.rate') }}"
                            class="nav-link {{ request()->routeIs('admin.ibanking.rate') ? 'active' : '' }}"
                        >
                            <i class="far fa-circle nav-icon"></i>
                            <p>Update Rate</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a
                            href="{{ route('admin.ibanking.list') }}"
                            class="nav-link {{ request()->routeIs('admin.ibanking.list', 'admin.ibanking.create', 'admin.ibanking.edit', 'admin.iBanking.*') ? 'active' : '' }}"
                        >
                            <i class="far fa-circle nav-icon"></i>
                            <p>List Bank
                            </p>
                        </a>

                        <a
                            href="{{ route('admin.ibanking.orders.list') }}"
                            class="nav-link {{ request()->routeIs('admin.ibanking.orders.list', 'admin.ibanking_orders.*') ? 'active' : '' }}"
                        >
                            <i class="far fa-circle nav-icon"></i>
                            <p>Order iBanking
                            </p>
                        </a>

                    </li>
                </ul>

            </li>

            <!-- App Setup Management -->
            <li class="nav-header">System</li>

            <li class="nav-item {{ $isAppSetup ? 'menu-open' : '' }}">
                <a
                    href="#"
                    class="nav-link {{ $isAppSetup ? 'active' : '' }}"
                >
                    <i class="nav-icon fas fa-cogs text-info"></i>

                    <p>
                        App Setup Management
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>

                <ul class="nav nav-treeview">

                    <li class="nav-item">
                        <a
                            href="{{ route('admin.setup.play_game') }}"
                            class="nav-link {{ request()->routeIs('admin.setup.play_game') ? 'active' : '' }}"
                        >
                            <i class="fas fa-edit nav-icon text-info"></i>
                            <p>Play Game</p>
                        </a>
                    </li>

                    @if ($appSetupExists)
                        <li class="nav-item">
                            <a
                                href="{{ route('admin.setup.content') }}"
                                class="nav-link {{ request()->routeIs('admin.setup.content') ? 'active' : '' }}"
                            >
                                <i class="fas fa-edit nav-icon text-info"></i>
                                <p>User Balance Content</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a
                                href="{{ route('admin.setup.content.register') }}"
                                class="nav-link {{ request()->routeIs('admin.setup.content.register') ? 'active' : '' }}"
                            >
                                <i class="fas fa-edit nav-icon text-info"></i>
                                <p>Register Balance Content</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a
                                href="{{ route('admin.setup.social') }}"
                                class="nav-link {{ request()->routeIs('admin.setup.social') ? 'active' : '' }}"
                            >
                                <i class="fas fa-edit nav-icon text-info"></i>
                                <p>Social Link Update</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a
                                href="{{ route('admin.notification.message') }}"
                                class="nav-link {{ request()->routeIs('admin.notification.message') ? 'active' : '' }}"
                            >
                                <i class="fas fa-edit nav-icon text-info"></i>
                                <p>Notification Message Update</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a
                                href="{{ route('admin.change.password') }}"
                                class="nav-link {{ request()->routeIs('admin.change.password') ? 'active' : '' }}"
                            >
                                <i class="fas fa-edit nav-icon text-info"></i>
                                <p>Change Password</p>
                            </a>
                        </li>
                    @endif


                </ul>

            </li>



            <li class="nav-item logout-item">
                <form
                    method="POST"
                    action="{{ route('admin.logout') }}"
                >
                    @csrf
                    <a
                        href="{{ route('admin.logout') }}"
                        class="nav-link"
                        onclick="event.preventDefault(); this.closest('form').submit();"
                    >
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Logout</p>
                    </a>
                </form>
            </li>



        </ul>
    </nav>
    <!-- /.sidebar-menu -->
</div>
