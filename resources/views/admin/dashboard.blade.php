@extends('master.backend')

@section('content')
<style>
  .dashboard-shell {
    --dash-ink: #0f172a; --dash-muted: #64748b; --dash-line: #e8edf4;
    min-height: calc(100vh - 57px); background: #f6f8fb; color: var(--dash-ink);
    font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
  }
  .dashboard-content { padding: 28px 30px 36px; }
  .dashboard-header { display: flex; align-items: flex-start; justify-content: space-between; gap: 20px; margin-bottom: 24px; }
  .dashboard-eyebrow { margin-bottom: 7px; color: #f97316; font-size: 11px; font-weight: 800; letter-spacing: .14em; text-transform: uppercase; }
  .dashboard-title { margin: 0; color: #0f172a; font-size: 28px; font-weight: 750; letter-spacing: -.035em; }
  .dashboard-subtitle { margin: 7px 0 0; color: #64748b; font-size: 13px; }
  .dashboard-date { display: flex; align-items: center; gap: 10px; padding: 10px 13px; border: 1px solid #e8edf4; border-radius: 12px; background: #fff; color: #475569; font-size: 12px; font-weight: 600; box-shadow: 0 4px 14px rgba(15,23,42,.035); }
  .dashboard-date i { color: #f97316; }
  .hero-grid { display: grid; grid-template-columns: minmax(0,1.75fr) minmax(260px,.85fr); gap: 18px; margin-bottom: 18px; }
  .hero-card { position: relative; min-height: 190px; overflow: hidden; padding: 27px 29px; border-radius: 20px; color: #fff; background: linear-gradient(135deg,#111d33 0%,#182943 62%,#243b5d 100%); box-shadow: 0 16px 35px rgba(15,23,42,.13); }
  .hero-card::after { position: absolute; top: -90px; right: -65px; width: 260px; height: 260px; border: 48px solid rgba(249,115,22,.12); border-radius: 50%; content: ''; }
  .hero-label { position: relative; z-index: 1; color: #a9b7ca; font-size: 12px; font-weight: 650; letter-spacing: .04em; }
  .hero-value { position: relative; z-index: 1; margin-top: 12px; font-size: 36px; font-weight: 760; letter-spacing: -.045em; line-height: 1; }
  .hero-value small { color: #fdba74; font-size: 13px; font-weight: 750; letter-spacing: .08em; }
  .hero-meta { position: absolute; z-index: 1; right: 29px; bottom: 27px; left: 29px; display: flex; gap: 22px; padding-top: 17px; border-top: 1px solid rgba(255,255,255,.12); }
  .hero-meta strong,.hero-meta span { display: block; } .hero-meta strong { font-size: 17px; font-weight: 720; } .hero-meta span { margin-top: 3px; color: #94a3b8; font-size: 10px; text-transform: uppercase; letter-spacing: .07em; }
  .attention-card { padding: 23px; border: 1px solid #fed7aa; border-radius: 20px; background: linear-gradient(145deg,#fffaf4,#fff); box-shadow: 0 10px 28px rgba(249,115,22,.07); }
  .attention-head { display: flex; align-items: center; justify-content: space-between; }
  .attention-icon { display: inline-flex; width: 39px; height: 39px; align-items: center; justify-content: center; border-radius: 12px; color: #c2410c; background: #ffedd5; }
  .attention-total { margin: 18px 0 3px; color: #9a3412; font-size: 35px; font-weight: 760; letter-spacing: -.04em; line-height: 1; }
  .attention-card h2 { margin: 0; color: #7c2d12; font-size: 14px; font-weight: 750; } .attention-card p { margin: 7px 0 18px; color: #9a684e; font-size: 11px; }
  .attention-link { display: inline-flex; align-items: center; gap: 8px; color: #c2410c; font-size: 11px; font-weight: 750; } .attention-link:hover { color: #9a3412; }
  .metric-grid { display: grid; grid-template-columns: repeat(4,minmax(0,1fr)); gap: 14px; margin-bottom: 24px; }
  .metric-card { position: relative; min-width: 0; overflow: hidden; padding: 18px; border: 1px solid #e8edf4; border-radius: 16px; background: #fff; box-shadow: 0 5px 18px rgba(15,23,42,.035); transition: transform .18s ease,box-shadow .18s ease; }
  .metric-card:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(15,23,42,.07); }
  .metric-top { display: flex; align-items: center; justify-content: space-between; gap: 10px; }
  .metric-icon { display: inline-flex; width: 37px; height: 37px; flex: 0 0 37px; align-items: center; justify-content: center; border-radius: 11px; color: var(--metric-color); background: var(--metric-bg); font-size: 14px; }
  .metric-arrow { color: #cbd5e1; font-size: 11px; }
  .metric-value { overflow: hidden; margin-top: 18px; color: #0f172a; font-size: 24px; font-weight: 760; letter-spacing: -.035em; line-height: 1.05; text-overflow: ellipsis; white-space: nowrap; }
  .metric-value small { color: #94a3b8; font-size: 9px; font-weight: 800; letter-spacing: .07em; } .metric-label { margin: 8px 0 0; color: #64748b; font-size: 11px; font-weight: 650; }
  .metric-card.users { --metric-color:#2563eb;--metric-bg:#eff6ff; } .metric-card.pending { --metric-color:#d97706;--metric-bg:#fffbeb; } .metric-card.approved { --metric-color:#059669;--metric-bg:#ecfdf5; } .metric-card.bd { --metric-color:#7c3aed;--metric-bg:#f5f3ff; }
  .metric-card.male { --metric-color:#db2777;--metric-bg:#fdf2f8; } .metric-card.package { --metric-color:#0891b2;--metric-bg:#ecfeff; } .metric-card.mobile { --metric-color:#ea580c;--metric-bg:#fff7ed; } .metric-card.bank { --metric-color:#4f46e5;--metric-bg:#eef2ff; }
  .section-panel { margin-bottom: 18px; padding: 22px; border: 1px solid #e8edf4; border-radius: 18px; background: #fff; box-shadow: 0 5px 18px rgba(15,23,42,.03); }
  .section-heading { display: flex; align-items: flex-end; justify-content: space-between; gap: 16px; margin-bottom: 17px; } .section-heading h2 { margin: 0; color: #0f172a; font-size: 16px; font-weight: 750; letter-spacing: -.02em; } .section-heading p { margin: 4px 0 0; color: #94a3b8; font-size: 11px; } .section-heading a { color: #f97316; font-size: 11px; font-weight: 750; white-space: nowrap; }
  .breakdown-grid { display: grid; grid-template-columns: repeat(3,minmax(0,1fr)); gap: 11px; }
  .breakdown-item { display: flex; min-width: 0; align-items: center; gap: 12px; padding: 13px; border: 1px solid #edf1f6; border-radius: 13px; background: #fbfcfe; }
  .breakdown-logo { display: inline-flex; width: 42px; height: 42px; flex: 0 0 42px; align-items: center; justify-content: center; overflow: hidden; border: 1px solid #edf1f6; border-radius: 12px; color: #64748b; background: #fff; font-size: 15px; } .breakdown-logo img { width: 28px; height: 28px; object-fit: contain; }
  .breakdown-copy { min-width: 0; flex: 1; } .breakdown-copy strong,.breakdown-copy span { display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; } .breakdown-copy strong { color: #334155; font-size: 12px; font-weight: 700; } .breakdown-copy span { margin-top: 4px; color: #94a3b8; font-size: 10px; }
  .breakdown-amount { flex: 0 0 auto; text-align: right; } .breakdown-amount strong { display: block; color: #0f172a; font-size: 13px; font-weight: 750; } .breakdown-amount small { color: #94a3b8; font-size: 9px; font-weight: 700; }
  .status-dot { display: inline-block; width: 6px; height: 6px; margin-right: 4px; border-radius: 50%; background: #10b981; } .status-dot.inactive { background: #94a3b8; }
  .empty-state { grid-column: 1/-1; padding: 24px; border: 1px dashed #dbe3ed; border-radius: 13px; color: #94a3b8; text-align: center; font-size: 12px; }
  .queue-grid { display: grid; grid-template-columns: repeat(6,minmax(0,1fr)); gap: 10px; } .queue-item { display: block; padding: 14px 13px; border: 1px solid #edf1f6; border-radius: 13px; background: #fbfcfe; } .queue-item:hover { border-color: #fed7aa; background: #fffaf5; } .queue-item strong { display: block; color: #1e293b; font-size: 19px; font-weight: 750; } .queue-item span { display: block; margin-top: 5px; overflow: hidden; color: #64748b; font-size: 10px; font-weight: 650; text-overflow: ellipsis; white-space: nowrap; }
  @media(max-width:1199.98px){.metric-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.breakdown-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.queue-grid{grid-template-columns:repeat(3,minmax(0,1fr))}}
  @media(max-width:767.98px){.dashboard-content{padding:20px 15px 28px}.dashboard-header{align-items:stretch;flex-direction:column;margin-bottom:18px}.dashboard-date{align-self:flex-start}.dashboard-title{font-size:24px}.hero-grid{grid-template-columns:1fr}.hero-card{min-height:205px;padding:23px}.hero-meta{right:23px;bottom:23px;left:23px}.metric-grid{gap:10px}.metric-card{padding:15px}.metric-value{font-size:20px}.section-panel{padding:17px}.breakdown-grid{grid-template-columns:1fr}.queue-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
  @media(max-width:380px){.metric-grid{grid-template-columns:1fr}}
</style>

<div class="content-wrapper dashboard-shell">
  <main class="dashboard-content">
    <header class="dashboard-header">
      <div><div class="dashboard-eyebrow">Admin overview</div><h1 class="dashboard-title">Dashboard Reports</h1><p class="dashboard-subtitle">A clear snapshot of users, balances and service performance.</p></div>
      <div class="dashboard-date"><i class="far fa-calendar-alt"></i>{{ now()->format('d M Y') }}</div>
    </header>

    <section class="hero-grid" aria-label="Business overview">
      <div class="hero-card">
        <div class="hero-label">Total customer wallet balance</div>
        <div class="hero-value">{{ number_format((float)$summary->wallet_balance, 2) }} <small>MVR</small></div>
        <div class="hero-meta"><div><strong>{{ number_format((int)$summary->total_users) }}</strong><span>Total users</span></div><div><strong>{{ number_format((int)$summary->today_activity) }}</strong><span>Today's activity</span></div><div><strong>{{ number_format((int)$summary->approved_users) }}</strong><span>Approved users</span></div></div>
      </div>
      <div class="attention-card">
        <div class="attention-head"><div class="attention-icon"><i class="fas fa-inbox"></i></div><span class="badge badge-warning">Live queue</span></div>
        <div class="attention-total">{{ number_format($pendingTotal) }}</div><h2>Items need attention</h2><p>Pending approvals across users, balance and service orders.</p>
        @if($nextPending)
          <a class="attention-link" href="{{ $nextPending['url'] }}">{{ $nextPending['label'] }} <i class="fas fa-arrow-right"></i></a>
        @else
          <span class="attention-link"><i class="fas fa-check-circle"></i> All caught up</span>
        @endif
      </div>
    </section>

    @php
      $metrics = [
        ['users','fas fa-users',$summary->total_users,null,'Total Users',route('admin.users.list')],
        ['pending','far fa-clock',$summary->pending_users,null,'Pending Users',route('admin.users.list')],
        ['approved','fas fa-user-check',$summary->approved_users,null,'Approved Users',route('admin.users.list')],
        ['bd','fas fa-mobile-alt',$summary->bd_recharge_total,'BDT','Total BD Recharge',route('admin.recharges.pending')],
        ['male','fas fa-sim-card',$summary->male_recharge_total,'MVR','Total Maldives Recharge',route('admin.male.recharges.pending')],
        ['package','fas fa-box-open',$summary->package_total,'MVR','Total Package Orders',route('admin.package.orders')],
        ['mobile','fas fa-wallet',$summary->mobile_banking_total,'MVR','Total Mobile Banking',route('admin.mobile.banking.orders.list')],
        ['bank','fas fa-university',$summary->ibanking_total,'MVR','Total iBanking',route('admin.ibanking.orders.list')],
      ];
    @endphp
    <section class="metric-grid" aria-label="Key metrics">
      @foreach($metrics as [$class,$icon,$value,$currency,$label,$url])
        <a href="{{ $url }}" class="metric-card {{ $class }}" aria-label="View {{ $label }}"><div class="metric-top"><span class="metric-icon"><i class="{{ $icon }}"></i></span><i class="fas fa-arrow-right metric-arrow"></i></div><div class="metric-value">{{ $currency ? number_format((float)$value,2) : number_format((int)$value) }} @if($currency)<small>{{ $currency }}</small>@endif</div><p class="metric-label">{{ $label }}</p></a>
      @endforeach
    </section>

    <section class="section-panel" aria-labelledby="queue-title">
      <div class="section-heading"><div><h2 id="queue-title">Pending workload</h2><p>Requests waiting for admin action</p></div></div>
      <div class="queue-grid">
        <a class="queue-item" href="{{ route('admin.users.list') }}"><strong>{{ $summary->pending_users }}</strong><span>User approvals</span></a>
        <a class="queue-item" href="{{ route('admin.balance.pending') }}"><strong>{{ $summary->pending_balance }}</strong><span>Balance requests</span></a>
        <a class="queue-item" href="{{ route('admin.recharges.pending') }}"><strong>{{ $summary->pending_bd_recharge }}</strong><span>BD recharges</span></a>
        <a class="queue-item" href="{{ route('admin.male.recharges.pending') }}"><strong>{{ $summary->pending_male_recharge }}</strong><span>Maldives recharges</span></a>
        <a class="queue-item" href="{{ route('admin.package.orders') }}"><strong>{{ $summary->pending_packages }}</strong><span>Package orders</span></a>
        <a class="queue-item" href="{{ (int)$summary->pending_mobile_banking > 0 ? route('admin.mobile.banking.orders.list') : route('admin.ibanking.orders.list') }}"><strong>{{ (int)$summary->pending_mobile_banking + (int)$summary->pending_ibanking }}</strong><span>Banking orders</span></a>
      </div>
    </section>

    <section class="section-panel" aria-labelledby="packages-title">
      <div class="section-heading"><div><h2 id="packages-title">Package performance</h2><p>Approved order value by active package</p></div><a href="{{ route('admin.package.orders') }}">View all orders</a></div>
      <div class="breakdown-grid">
        @forelse($packages as $package)
          <div class="breakdown-item"><span class="breakdown-logo">@if($package->image_icon)<img src="{{ asset('storage/'.$package->image_icon) }}" alt="" loading="lazy" decoding="async">@else<i class="fas fa-box"></i>@endif</span><div class="breakdown-copy"><strong>{{ $package->name }}</strong><span>{{ number_format($package->order_count) }} approved orders</span></div><div class="breakdown-amount"><strong>{{ number_format((float)$package->total_amount,2) }}</strong><small>MVR</small></div></div>
        @empty<div class="empty-state">No active packages found.</div>@endforelse
      </div>
    </section>

    <section class="section-panel" aria-labelledby="mobile-title">
      <div class="section-heading"><div><h2 id="mobile-title">Mobile banking performance</h2><p>Approved order value by provider</p></div><a href="{{ route('admin.mobile.banking.orders.list') }}">View all orders</a></div>
      <div class="breakdown-grid">
        @forelse($mobileBanking as $mobile)
          <div class="breakdown-item"><span class="breakdown-logo">@if($mobile->image_icon)<img src="{{ asset('storage/'.$mobile->image_icon) }}" alt="" loading="lazy" decoding="async">@else<i class="fas fa-wallet"></i>@endif</span><div class="breakdown-copy"><strong>{{ $mobile->name }}</strong><span><i class="status-dot {{ $mobile->status === 'active' ? '' : 'inactive' }}"></i>{{ ucfirst($mobile->status) }} · {{ number_format($mobile->order_count) }} orders</span></div><div class="breakdown-amount"><strong>{{ number_format((float)$mobile->total_amount,2) }}</strong><small>MVR</small></div></div>
        @empty<div class="empty-state">No mobile banking providers found.</div>@endforelse
      </div>
    </section>

    <section class="section-panel" aria-labelledby="banks-title">
      <div class="section-heading"><div><h2 id="banks-title">iBanking performance</h2><p>Approved transfer value by bank</p></div><a href="{{ route('admin.ibanking.orders.list') }}">View all orders</a></div>
      <div class="breakdown-grid">
        @forelse($banks as $bank)
          <div class="breakdown-item"><span class="breakdown-logo"><i class="fas fa-university"></i></span><div class="breakdown-copy"><strong>{{ $bank->bank_name }}</strong><span><i class="status-dot {{ $bank->status === 'active' ? '' : 'inactive' }}"></i>{{ ucfirst($bank->status) }} · {{ number_format($bank->order_count) }} orders</span></div><div class="breakdown-amount"><strong>{{ number_format((float)$bank->total_amount,2) }}</strong><small>MVR</small></div></div>
        @empty<div class="empty-state">No banks found.</div>@endforelse
      </div>
    </section>
  </main>
</div>
@endsection
