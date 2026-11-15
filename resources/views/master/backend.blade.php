<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin| Dashboard</title>

 

  <link rel="icon" type="image/x-icon" href="#">

  @unless(request()->routeIs('admin.dashboard'))
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  @endunless
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('backend/plugins/fontawesome-free/css/all.min.css')}}">
  @unless(request()->routeIs('admin.dashboard'))
  <!-- Optional page plugins -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="{{ asset('backend/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css')}}">
  <!-- iCheck -->
  <link rel="stylesheet" href="{{ asset('backend/plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
  <!-- JQVMap -->
  <link rel="stylesheet" href="{{ asset('backend/plugins/jqvmap/jqvmap.min.css')}}">
  @endunless
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('backend/dist/css/adminlte.min.css')}}">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="{{ asset('backend/plugins/overlayScrollbars/css/OverlayScrollbars.min.css')}}">
  @unless(request()->routeIs('admin.dashboard'))
  <!-- Daterange picker -->
  <link rel="stylesheet" href="{{ asset('backend/plugins/daterangepicker/daterangepicker.css')}}">
  <!-- summernote -->
  <link rel="stylesheet" href="{{ asset('backend/plugins/summernote/summernote-bs4.min.css')}}">
  @endunless

  <script src="{{ asset('backend/plugins/jquery/jquery.min.js')}}"></script>

@unless(request()->routeIs('admin.dashboard'))
<link rel="stylesheet" href="{{ asset('backend/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
  <link rel="stylesheet" href="{{ asset('backend/plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
  <link rel="stylesheet" href="{{ asset('backend/plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">

  <!-- DataTables  & Plugins -->
<script src="{{ asset('backend/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{ asset('backend/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{ asset('backend/plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{ asset('backend/plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
<script src="{{ asset('backend/plugins/datatables-buttons/js/dataTables.buttons.min.js')}}"></script>
<script src="{{ asset('backend/plugins/datatables-buttons/js/buttons.bootstrap4.min.js')}}"></script>
<script src="{{ asset('backend/plugins/jszip/jszip.min.js')}}"></script>
<script src="{{ asset('backend/plugins/pdfmake/pdfmake.min.js')}}"></script>
<script src="{{ asset('backend/plugins/pdfmake/vfs_fonts.js')}}"></script>
<script src="{{ asset('backend/plugins/datatables-buttons/js/buttons.html5.min.js')}}"></script>
<script src="{{ asset('backend/plugins/datatables-buttons/js/buttons.print.min.js')}}"></script>
<script src="{{ asset('backend/plugins/datatables-buttons/js/buttons.colVis.min.js')}}"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
	 <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
@endunless

  <style>
    :root {
      --admin-sidebar-bg: #0b1220;
      --admin-sidebar-surface: #141f33;
      --admin-sidebar-border: rgba(148, 163, 184, .13);
      --admin-sidebar-text: #cbd5e1;
      --admin-sidebar-muted: #718096;
      --admin-sidebar-accent: #f97316;
      --admin-sidebar-accent-soft: rgba(249, 115, 22, .14);
    }

    .admin-sidebar {
      background:
        radial-gradient(circle at 5% 0, rgba(249, 115, 22, .09), transparent 15rem),
        var(--admin-sidebar-bg) !important;
      border-right: 1px solid var(--admin-sidebar-border);
      box-shadow: 12px 0 35px rgba(2, 6, 23, .16) !important;
      overflow: hidden !important;
    }

    .admin-sidebar .brand-link {
      display: flex;
      height: 70px;
      align-items: center;
      gap: 12px;
      padding: 12px 15px;
      border-bottom: 1px solid var(--admin-sidebar-border);
      color: #fff;
      background: rgba(15, 23, 42, .72);
    }

    .admin-sidebar .brand-mark {
      display: inline-flex;
      width: 42px;
      height: 42px;
      flex: 0 0 42px;
      align-items: center;
      justify-content: center;
      border-radius: 13px;
      color: #fff;
      background: linear-gradient(145deg, #fb923c, #ea580c);
      box-shadow: 0 8px 22px rgba(249, 115, 22, .28);
      font-size: 17px;
    }

    .admin-sidebar .brand-copy {
      min-width: 0;
      line-height: 1.1;
    }

    .admin-sidebar .brand-name {
      display: block;
      overflow: hidden;
      font-size: 17px;
      font-weight: 700;
      letter-spacing: -.02em;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .admin-sidebar .brand-name em {
      color: var(--admin-sidebar-accent);
      font-style: normal;
    }

    .admin-sidebar .brand-subtitle {
      display: block;
      margin-top: 5px;
      color: #64748b;
      font-size: 9px;
      font-weight: 700;
      letter-spacing: .16em;
      text-transform: uppercase;
    }

    .admin-sidebar .sidebar {
      width: 100%;
      max-width: 100%;
      height: calc(100vh - 70px) !important;
      height: calc(100dvh - 70px) !important;
      box-sizing: border-box;
      padding: 12px 10px 70px;
      overflow-x: hidden !important;
      overflow-y: auto !important;
      -ms-overflow-style: none;
      scrollbar-width: none;
    }

    .admin-sidebar .sidebar::-webkit-scrollbar {
      display: none;
      width: 0;
      height: 0;
    }

    .admin-sidebar .os-scrollbar {
      display: none !important;
    }

    .admin-sidebar .os-host,
    .admin-sidebar .os-padding,
    .admin-sidebar .os-viewport {
      width: 100% !important;
      max-width: 100% !important;
      box-sizing: border-box;
      overflow-x: hidden !important;
      scrollbar-width: none;
    }

    .admin-sidebar .os-viewport::-webkit-scrollbar {
      display: none !important;
      width: 0 !important;
      height: 0 !important;
    }

    .admin-sidebar nav,
    .admin-sidebar .nav-sidebar {
      width: 100% !important;
      max-width: 100% !important;
      min-width: 0;
      overflow-x: hidden !important;
    }

    .admin-sidebar * {
      scrollbar-width: none;
    }

    .admin-sidebar *::-webkit-scrollbar {
      display: none !important;
      width: 0 !important;
      height: 0 !important;
    }

    .admin-sidebar .sidebar-context {
      display: flex;
      align-items: center;
      gap: 10px;
      margin: 2px 2px 15px;
      padding: 11px;
      border: 1px solid var(--admin-sidebar-border);
      border-radius: 13px;
      background: rgba(20, 31, 51, .7);
    }

    .admin-sidebar .sidebar-context-icon {
      display: inline-flex;
      width: 34px;
      height: 34px;
      flex: 0 0 34px;
      align-items: center;
      justify-content: center;
      border-radius: 10px;
      color: #fdba74;
      background: var(--admin-sidebar-accent-soft);
      font-size: 13px;
    }

    .admin-sidebar .sidebar-context-copy {
      min-width: 0;
    }

    .admin-sidebar .sidebar-context-copy strong,
    .admin-sidebar .sidebar-context-copy span {
      display: block;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .admin-sidebar .sidebar-context-copy strong {
      color: #f8fafc;
      font-size: 12px;
      font-weight: 650;
    }

    .admin-sidebar .sidebar-context-copy span {
      margin-top: 3px;
      color: #64748b;
      font-size: 10px;
    }

    .admin-sidebar .nav-header {
      padding: 12px 12px 7px;
      color: var(--admin-sidebar-muted);
      font-size: 9px;
      font-weight: 800;
      letter-spacing: .15em;
      text-transform: uppercase;
    }

    .admin-sidebar .nav-sidebar > .nav-item {
      margin-bottom: 3px;
    }

    .admin-sidebar .nav-sidebar .nav-link {
      position: relative;
      display: flex;
      min-height: 45px;
      align-items: center;
      margin: 0;
      padding: 8px 10px;
      border-radius: 11px;
      color: var(--admin-sidebar-text);
      transition: color .18s ease, background-color .18s ease, transform .18s ease;
    }

    .admin-sidebar .nav-sidebar .nav-link:hover {
      color: #fff;
      background: rgba(148, 163, 184, .09);
    }

    .admin-sidebar .nav-sidebar .nav-link > .nav-icon {
      display: inline-flex;
      width: 30px;
      height: 30px;
      flex: 0 0 30px;
      align-items: center;
      justify-content: center;
      margin: 0 10px 0 0;
      border-radius: 9px;
      color: #94a3b8 !important;
      background: rgba(148, 163, 184, .08);
      font-size: 13px;
      transition: color .18s ease, background-color .18s ease;
    }

    .admin-sidebar .nav-sidebar .nav-link p {
      display: flex;
      min-width: 0;
      flex: 1;
      align-items: center;
      font-size: 12px;
      font-weight: 550;
      line-height: 1.25;
      white-space: normal;
    }

    .admin-sidebar .nav-sidebar .nav-link > .badge {
      order: 3;
      min-width: 20px;
      margin: 0 22px 0 auto;
      padding: 4px 6px;
      border: 0;
      border-radius: 999px;
      color: #fed7aa;
      background: rgba(249, 115, 22, .16);
      font-size: 9px;
      font-weight: 800;
      line-height: 1;
    }

    .admin-sidebar .nav-sidebar .nav-link .right {
      top: auto;
      right: 10px;
      color: #64748b;
      font-size: 10px;
    }

    .admin-sidebar .nav-sidebar > .nav-item > .nav-link.active,
    .admin-sidebar .nav-sidebar > .nav-item.menu-open > .nav-link {
      color: #fff !important;
      background: linear-gradient(90deg, rgba(249, 115, 22, .18), rgba(249, 115, 22, .06)) !important;
      box-shadow: inset 0 0 0 1px rgba(251, 146, 60, .14) !important;
    }

    .admin-sidebar .nav-sidebar > .nav-item > .nav-link.active::before,
    .admin-sidebar .nav-sidebar > .nav-item.menu-open > .nav-link::before {
      position: absolute;
      top: 10px;
      bottom: 10px;
      left: -10px;
      width: 3px;
      border-radius: 0 4px 4px 0;
      background: var(--admin-sidebar-accent);
      box-shadow: 0 0 12px rgba(249, 115, 22, .55);
      content: '';
    }

    .admin-sidebar .nav-sidebar > .nav-item > .nav-link.active > .nav-icon,
    .admin-sidebar .nav-sidebar > .nav-item.menu-open > .nav-link > .nav-icon {
      color: #fff !important;
      background: var(--admin-sidebar-accent);
      box-shadow: 0 5px 14px rgba(249, 115, 22, .22);
    }

    .admin-sidebar .nav-treeview {
      margin: 5px 0 8px 20px;
      padding-left: 9px;
      border-left: 1px solid rgba(148, 163, 184, .15);
    }

    .admin-sidebar .nav-treeview > .nav-item > .nav-link {
      min-height: 37px;
      padding: 6px 9px;
      border-radius: 9px;
      color: #94a3b8;
    }

    .admin-sidebar .nav-treeview > .nav-item > .nav-link > .nav-icon {
      width: 18px;
      height: 18px;
      flex-basis: 18px;
      margin-right: 7px;
      color: #475569 !important;
      background: transparent;
      font-size: 6px;
    }

    .admin-sidebar .nav-treeview > .nav-item > .nav-link p {
      font-size: 11px;
      font-weight: 500;
    }

    .admin-sidebar .nav-treeview > .nav-item > .nav-link.active {
      color: #ffedd5 !important;
      background: rgba(249, 115, 22, .12) !important;
      box-shadow: none !important;
    }

    .admin-sidebar .nav-treeview > .nav-item > .nav-link.active > .nav-icon {
      color: var(--admin-sidebar-accent) !important;
    }

    .admin-sidebar .logout-item {
      margin-top: 10px;
      padding-top: 10px;
      border-top: 1px solid var(--admin-sidebar-border);
    }

    .admin-sidebar .logout-item .nav-link:hover {
      color: #fecaca;
      background: rgba(239, 68, 68, .1);
    }

    .admin-sidebar .logout-item .nav-link:hover > .nav-icon {
      color: #fca5a5 !important;
      background: rgba(239, 68, 68, .12);
    }

    .sidebar-mini.sidebar-collapse .admin-sidebar .brand-copy,
    .sidebar-mini.sidebar-collapse .admin-sidebar .sidebar-context-copy,
    .sidebar-mini.sidebar-collapse .admin-sidebar .nav-header {
      display: none;
    }

    .sidebar-mini.sidebar-collapse .admin-sidebar:hover .brand-copy,
    .sidebar-mini.sidebar-collapse .admin-sidebar:hover .sidebar-context-copy,
    .sidebar-mini.sidebar-collapse .admin-sidebar:hover .nav-header {
      display: block;
    }

    @media (max-width: 767.98px) {
      .admin-sidebar {
        box-shadow: 18px 0 40px rgba(2, 6, 23, .3) !important;
      }
    }

    @media (prefers-reduced-motion: reduce) {
      .admin-sidebar .nav-sidebar .nav-link {
        transition: none;
      }
    }
    /* Shared admin DataTable style */
    .dataTables_wrapper {
      color: #334155;
    }

    .dataTables_wrapper table.dataTable {
      border: 1px solid #e5e7eb !important;
      border-radius: 8px;
      overflow: hidden;
    }

    .dataTables_wrapper table.dataTable thead th {
      background: #f8fafc;
      border-bottom: 1px solid #e5e7eb !important;
      color: #475569;
      font-size: 12px;
      font-weight: 700;
      letter-spacing: .03em;
      text-transform: uppercase;
      vertical-align: middle;
      white-space: nowrap;
    }

    .dataTables_wrapper table.dataTable tbody td {
      border-color: #eef2f7;
      vertical-align: middle;
    }

    .dataTables_wrapper table.dataTable tbody tr:hover {
      background: #f8fafc;
    }

    .dataTables_wrapper .dataTables_filter input,
    .dataTables_wrapper .dataTables_length select {
      border: 1px solid #dbe2ea;
      border-radius: 7px;
      background: #fff;
      min-height: 36px;
    }

    .dataTables_wrapper .dataTables_filter input {
      min-width: 220px;
      padding: 6px 10px;
    }

    .dataTables_wrapper .dt-buttons .btn {
      border-radius: 7px !important;
      margin-right: 5px;
      margin-bottom: 5px;
    }

    .dataTables_wrapper .pagination .page-link {
      border-color: #e2e8f0;
      color: #475569;
    }

    .dataTables_wrapper .pagination .active .page-link {
      background: #2563eb;
      border-color: #2563eb;
      color: #fff;
    }

    .dataTables_wrapper table.dataTable td:last-child {
      white-space: nowrap;
    }

    .dataTables_wrapper table.dataTable td:last-child form {
      display: inline-block;
      margin: 2px;
    }

    .dataTables_wrapper table.dataTable td .btn-sm {
      border-radius: 7px;
      font-weight: 600;
      padding: .32rem .6rem;
      box-shadow: 0 1px 2px rgba(15, 23, 42, .08);
      transition: transform .15s ease, box-shadow .15s ease;
    }

    .dataTables_wrapper table.dataTable td:last-child > .btn,
    .dataTables_wrapper table.dataTable td:last-child > a {
      margin: 2px;
    }

    .dataTables_wrapper table.dataTable td .btn-sm:hover {
      box-shadow: 0 4px 10px rgba(15, 23, 42, .14);
      transform: translateY(-1px);
    }

    @media (max-width: 575.98px) {
      .dataTables_wrapper .dataTables_filter input {
        min-width: 0;
        width: calc(100% - 58px);
      }
    }
  </style>
     
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Navbar -->
  @include('backend.layouts.navbar')
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4 admin-sidebar">
    <!-- Brand Logo -->
    <a href="{{ route('admin.dashboard') }}" class="brand-link" aria-label="EasyXpress admin dashboard">
      <span class="brand-mark" aria-hidden="true"><i class="fas fa-bolt"></i></span>
      <span class="brand-copy">
        <span class="brand-name">Easy<em>Xpress</em></span>
        <span class="brand-subtitle">Admin console</span>
      </span>
    </a>

    <!-- Sidebar -->
   @include('backend.layouts.sidebar')
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  @yield('content')
  <!-- /.content-wrapper -->
  @include('backend.layouts.footer')

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->

@unless(request()->routeIs('admin.dashboard'))
<!-- Optional page plugins -->
<script src="{{ asset('backend/plugins/jquery-ui/jquery-ui.min.js')}}"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
@endunless
<!-- Bootstrap 4 -->
<script src="{{ asset('backend/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
@unless(request()->routeIs('admin.dashboard'))
<!-- ChartJS -->
<script src="{{ asset('backend/plugins/chart.js/Chart.min.js')}}"></script>
<!-- Sparkline -->
<script src="{{ asset('backend/plugins/sparklines/sparkline.js')}}"></script>
<!-- JQVMap -->
<script src="{{ asset('backend/plugins/jqvmap/jquery.vmap.min.js')}}"></script>
<script src="{{ asset('backend/plugins/jqvmap/maps/jquery.vmap.usa.js')}}"></script>
<!-- jQuery Knob Chart -->
<script src="{{ asset('backend/plugins/jquery-knob/jquery.knob.min.js')}}"></script>
<!-- daterangepicker -->
<script src="{{ asset('backend/plugins/moment/moment.min.js')}}"></script>
<script src="{{ asset('backend/plugins/daterangepicker/daterangepicker.js')}}"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="{{ asset('backend/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js')}}"></script>
<!-- Summernote -->
<script src="{{ asset('backend/plugins/summernote/summernote-bs4.min.js')}}"></script>
@endunless
<!-- overlayScrollbars -->
<script src="{{ asset('backend/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('backend/dist/js/adminlte.js')}}"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.querySelector('.admin-sidebar');

    if (!sidebar) return;

    const activeLeaf = sidebar.querySelector('.nav-treeview .nav-link.active')
      || sidebar.querySelector('.nav-sidebar > .nav-item > .nav-link.active');

    if (activeLeaf) {
      activeLeaf.setAttribute('aria-current', 'page');
    }
  });
</script>
@unless(request()->routeIs('admin.dashboard'))
<script>
  $(function () {
    $("#example1").DataTable({
      "responsive": true,
      "lengthChange": true,
      "autoWidth": false,
      "pageLength": 25,
      "order": [],
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
      "language": {
        "search": "Search:",
        "searchPlaceholder": "Type to search...",
        "lengthMenu": "Show _MENU_ entries",
        "emptyTable": "No records found"
      }
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    $('#example2').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
    });
  });
</script>
@endunless


@if(!request()->routeIs('admin.dashboard') && Session::has('success'))
    <script>
        toastr.success("{{ session("success") }}");
    </script>
    @endif

    @if(!request()->routeIs('admin.dashboard') && Session::has('error'))
    <script>
        toastr.error("{{ session("error") }}");
    </script>
    @endif

    

</body>
</html>
