<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <!-- Left navbar links -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="javascript:void(0)"><i class="fas fa-bars"></i></a>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
      <a href="{{ url('/') }}" class="nav-link">Home</a>
    </li>
    <li class="nav-item">
     
    </li>
    <li class="nav-item" title="Clear Cache">
      <a class="nav-link pr-0 pl-0" href="{{ url('/clear-cache') }}">
        <span class="btn btn-danger btn-sm"><i class="fas fa-sync"></i><span class="d-sm-none"> Clear Cache</span></span>
      </a>
    </li>
  </ul>

  <!-- SEARCH FORM -->
  <form id="invoice-search-form" class="form-inline d-none d-sm-inline-block ml-3">
    <div class="input-group input-group-sm">
        <input id="invoice-id" class="form-control form-control-navbar" type="search" placeholder="Enter Invoice ID" aria-label="Search">
        <div class="input-group-append">
            <button class="btn btn-navbar" type="submit">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </div>
  </form>
  <!-- Right navbar links -->
  <ul class="navbar-nav ml-auto">
    @can('view notification')
    <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="javascript:void(0)" title="Notifications">
            <i class="far fa-bell"></i>
            <span class="badge badge-warning navbar-badge">{{ auth()->user()->unreadNotifications->count() }}</span>
        </a>
        @if(auth()->user()->unreadNotifications->count()>0)
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
            <span class="dropdown-item dropdown-header">{{ auth()->user()->unreadNotifications->count() }} Notifications</span>
            <div class="dropdown-divider"></div>

            @foreach(auth()->user()->notifications as $notification)
                <a href="javascript:void(0)" @can('update notification')
                   id="notification-{{ $notification->id }}"
                   class="dropdown-item {{ $notification->read_at ? 'bg-light' : 'bg-danger' }}"
                   data-id="{{ $notification->id }}" @endcan>
                    <i class="fas fa-exclamation-circle mr-2"></i> {{ $notification->data['message'] }}
                    <span class="float-right text-muted text-sm">{{ $notification->created_at->diffForHumans() }}</span>
                </a>
            @endforeach

            <div class="dropdown-divider"></div>
            <a href="{{ route('notifications.index') }}" class="dropdown-item dropdown-footer">See All Notifications</a>
        </div>
        @endif
    </li>
    @endcan
    <li class="nav-item d-none d-sm-inline-block">
      <a class="nav-link" href="{{ url('help') }}" target="_blank">
        <i class="fas fa-question-circle"></i><span class="d-sm-none"> Support</span>
      </a>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
     
    </li>

    {{-- profile --}}
    <li class="nav-item">
      
    </li>
    <li class="nav-item">
      <a class="nav-link" href="{{ route('logout') }}"
         onclick="event.preventDefault();document.getElementById('logout-form').submit();"><i class="fas fa-sign-out-alt"></i><span class="d-sm-none"> Log out</span></a>
      <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
          @csrf
      </form>
    </li>
  </ul>
</nav>
<!-- end navbar -->
