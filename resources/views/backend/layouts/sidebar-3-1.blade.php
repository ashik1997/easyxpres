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

            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-users text-success"></i>
                    <p>
                        Users Management
                        <i class="fas fa-angle-left right"></i>

                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">

                        <a href="{{ route('admin.users.list') }}" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>List Users</p>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-wallet text-warning"></i>
             
                    <p>
                        Balance Management
                        <i class="fas fa-angle-left right"></i>

                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">

                        <a href="{{ route('admin.balance.pending') }}" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Pending Balance</p>
                        </a>
                    </li>
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