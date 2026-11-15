<div class="sidebar">
    <!-- Sidebar user panel (optional) -->


    <!-- SidebarSearch Form -->



    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

            <li class="nav-item">
                <a href="{{ URL('admin/dashboard')}}" class="nav-link">
                    <i class="nav-icon fas fa-th"></i>
                    <p>
                        Dashboard
                        <!-- <span class="right badge badge-danger">New</span> -->
                    </p>
                </a>
            </li>

            <li class="nav-item {{ request()->routeIs('admin.users.*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-users text-success"></i>
                    <p>
                        Users Management
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>

                       @php
    $UserCount = DB::table('users')
        ->where('status', 'pending')
        ->count();
@endphp

                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('admin.users.list') }}"
                            class="nav-link {{ request()->routeIs('admin.users.list') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>List Users
                                <span class="badge badge-warning ">
        {{ $UserCount ?? 0 }}
    </span>
                            </p>
                        </a>
                    </li>
                </ul>
            </li>


            <li class="nav-item {{ request()->routeIs('admin.balance.*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ request()->routeIs('admin.balance.*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-wallet text-warning"></i>
                    <p>
                        Balance Management
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>

                               @php
    $BalanceCount = DB::table('transactions')
        ->where('status', 'pending')
        ->count();
@endphp

                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('admin.balance.pending') }}"
                            class="nav-link {{ request()->routeIs('admin.balance.pending') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>List Balance
                                 <span class="badge badge-warning ">
        {{ $BalanceCount ?? 0 }}
    </span>
                            </p>
                        </a>

                     

                    </li>
                </ul>
            </li>

            <!-- // Package Management -->
            <li class="nav-item {{ request()->routeIs('admin.packages.*','admin.package.*','admin.subpackages.*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ request()->routeIs('admin.packages.*','admin.subpackages.*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-box-open text-info"></i>
                    <p>
                        Package Management
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>

                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('admin.packages.list') }}"
                            class="nav-link {{ request()->routeIs('admin.packages.list') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Packages List </p>
                        </a>

                        

                        <a href="{{ route('admin.subpackages.list') }}"
                            class="nav-link {{ request()->routeIs('admin.subpackages.list') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Sub Package List</p>
                        </a>

                                 @php
    $PackageCount = DB::table('package_orderls')
        ->where('status', 'pending')
        ->count();
@endphp

                    
                        <a href="{{ route('admin.package.orders') }}"
                            class="nav-link {{ request()->routeIs('admin.package.orders') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Package Orders
                                   <span class="badge badge-warning ">
        {{ $PackageCount ?? 0 }}
    </span>
                            </p>
                        </a>

                </ul>
            </li>

            <!-- // Recharge Management -->
            <li class="nav-item {{ request()->routeIs('admin.recharges.*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ request()->routeIs('admin.recharges.*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-mobile-alt text-primary"></i>
                    <p>
                        BD Recharge Management
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>

                     @php
    $RechargeCount = DB::table('recharges')
        ->where('status', 'pending')
        ->count();
@endphp

                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('admin.recharges.pending') }}"
                            class="nav-link {{ request()->routeIs('admin.recharges.pending') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>List BD Recharges
                                <span class="badge badge-warning ">
        {{ $RechargeCount ?? 0 }}
    </span>
                            </p>
                        </a>

                        

                    </li>
                </ul>

                </li>

                <!-- // Male Recharge Management -->
            <li class="nav-item {{ request()->routeIs('admin.male.recharges.*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ request()->routeIs('admin.male.recharges.*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-mobile-alt text-warning"></i>
                    <p>
                        Male Recharge Management
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>

               @php
    $maleRechargeCount = DB::table('male_recharges')
        ->where('status', 'pending')
        ->count();
@endphp
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('admin.male.recharges.pending') }}"
                            class="nav-link {{ request()->routeIs('admin.male.recharges.pending') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>List Male Recharges <span class="badge badge-warning ">
        {{ $maleRechargeCount ?? 0 }}
    </span>
                            </p>
                        </a>

                        

                    </li>
                </ul>

                </li>

                 <!-- // mobile Banking Management -->
            <li class="nav-item {{ request()->routeIs('admin.mobile.banking.*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ request()->routeIs('admin.mobile.banking.*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-wallet text-warning"></i>

                    <p>
                        Mobile Banking Management
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>

             
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('admin.mobile.banking.list') }}"
                            class="nav-link {{ request()->routeIs('admin.mobile.banking.list') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>List Mobile Banking
                            </p>
                        </a>

                        <a href=""
                            class="nav-link }">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Order Mobile Banking
                            </p>
                        </a>

                    </li>
                </ul>

                </li>

    <!-- App Setup Management -->
            <li class="nav-item {{ request()->routeIs('admin.setup.content.*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ request()->routeIs('admin.setup.content.*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-cogs text-info"></i>

                    <p>
                        App Setup Management
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>

                <ul class="nav nav-treeview">
                   @php
    $appSetupExists = DB::table('app_setups')->where('id', 1)->exists();
@endphp

@if($appSetupExists)
<li class="nav-item">
    <a href="{{ route('admin.setup.content') }}"
       class="nav-link {{ request()->routeIs('admin.setup.content') ? 'active' : '' }}">
        <i class="fas fa-edit nav-icon text-info"></i>
        <p>Content Change</p>
    </a>
</li>
@endif


                </ul>

                </li>



            <li class="nav-item">
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <a href="{{ route('admin.logout') }}"
                        class="nav-link"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Logout</p>
                    </a>
                </form>
            </li>



        </ul>
    </nav>
    <!-- /.sidebar-menu -->
</div>