@extends('master.backend')

@section('content')


<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
         
            <h1><span class="text-danger" style="border-bottom: 1px dotted red;">Dashboard Reports</span></h1>
          </div><!-- /.col -->

         
          
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">

          <!-- ./col sky-->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">

              @php
                use App\Models\User;
                $totalUsers = User::count();
              @endphp

                <h3>{{ $totalUsers }}</h3>

                <p>Total Users</p>
              </div>
              <div class="icon">
                <i class="ion ion-bag"></i>
              </div>
              <a href="{{ URL('admin/users') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
            <!-- ./col sky-->
          <!-- ./col green-->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
              <div class="inner">

              @php
                $pendingUsers = User::where('status', 'pending')->count();
              @endphp

                <h3>{{ $pendingUsers }}</h3>

                <p>Pending Users</p>
              </div>
              <div class="icon">
                <i class="ion ion-stats-bars"></i>
              </div>
              <a href="{{ URL('admin/users') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col green-->
          <!-- ./col Yellow -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
              <div class="inner">

              @php
                $approvedUsers = User::where('status', 'approved')->count();
              @endphp

                <h3>{{ $approvedUsers }}</h3>

                <p>Approved Users</p>
              </div>
              <div class="icon">
                <i class="ion ion-person-add"></i>
              </div>
              <a href="{{ URL('admin/users') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col Yellow -->
          <!-- ./col Red -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
              <div class="inner">
                <h3>{{ $totalUsers }}</h3>

                <p>Total Users</p>
              </div>
              <div class="icon">
                <i class="ion ion-pie-graph"></i>
              </div>
              <a href="{{ URL('admin/users') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col Red-->

             <div class="col-lg-1 col-6"></div>
           <!-- ./col Yellow -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
              <div class="inner">

              @php
                $totalTransactions = DB::table('transactions')->where('status', 'approved')->sum('amount');
              @endphp

                <h3>{{ $totalTransactions }} (MVR)</h3>

                <p>Total Balance</p>
              </div>
              <div class="icon">
                <i class="ion ion-cash"></i>
              </div>
              <a href="{{ URL('admin/balance/pending') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col Yellow -->

           <!-- ./col Red -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
              <div class="inner">
                @php
                $totalDBRecharges = DB::table('recharges')->where('status', 'approved')->sum('amount');
              @endphp

                <h3>{{ $totalDBRecharges }} (BDT)</h3>

                <p>Total BD Recharge</p>
              </div>
              <div class="icon">
                <i class="ion ion-cash"></i>
              </div>
              <a href="{{ URL('admin/recharges') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col Red-->

          <!-- ./col sky-->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">

              @php
                $totalMaleRecharges = DB::table('male_recharges')->where('status', 'approved')->sum('amount');
              @endphp

                <h3>{{ $totalMaleRecharges }} (MVR)</h3>

                <p>Total Male Recharge</p>
              </div>
              <div class="icon">
                  <i class="ion ion-cash"></i>
              </div>
              <a href="{{ URL('admin/male/recharges') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
            <!-- ./col sky-->
              <div class="col-lg-2 col-6"></div>

              <div class="col-lg-1 col-6"></div>
              <!-- ./col sky-->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">

             @php
                $totalPackageOrder = DB::table('package_orderls')->where('status', 'approved')->sum('amount');
              @endphp

                <h3>{{ $totalPackageOrder }} (MVR)</h3>

                <p>Total Packages Order</p>
              </div>
              <div class="icon">
               <i class="ion ion-cash"></i>
              </div>
              <a href="{{ URL('admin/package-orders') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
            <!-- ./col sky-->
          <!-- ./col green-->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
              <div class="inner">

              @php
                $totalMobileBankingOrder = DB::table('mobile_banking_orders')->where('status', 'approved')->sum('amount');
              @endphp

                <h3>{{ $totalMobileBankingOrder }} (MVR)</h3>

                <p>Total Mobile Banking Order</p>
              </div>
              <div class="icon">
               <i class="ion ion-cash"></i>
              </div>
              <a href="{{ URL('admin/mobile/banking/orders/list') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col green-->
          <!-- ./col Yellow -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
              <div class="inner">

              @php
                $totaliBankingOrder = DB::table('ibanking_orders')->where('status', 'approved')->sum('amount');
              @endphp

                <h3>{{ $totaliBankingOrder }} (MVR)</h3>

                <p>Total iBanking Order</p>
              </div>
              <div class="icon">
                 <i class="ion ion-cash"></i>
              </div>
              <a href="{{ URL('admin/ibanking/orders/list') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col Yellow -->
      
           <div class="col-lg-2 col-6"></div>

        </div>

      

      


     
        <!-- /.row -->
        <!-- Main row -->
       
        <!-- /.row (main row) -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>

  @endsection