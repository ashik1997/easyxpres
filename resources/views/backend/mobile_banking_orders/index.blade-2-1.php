@extends('master.backend')

@section('content')

<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">

                    <h1><span class="text-danger" style="border-bottom: 1px dotted red;">Mobile Banking Order</span></h1>

                </div>


             

            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">

                    <!-- /.card -->

                    <div class="card">

                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>

                                        <th>SL</th>
                                        <th>Icon</th>
                                        <th>User</th>
                                        <th>Mobile Banking</th>
                                        <th>Mobile Number</th>
                                        <th>Amount</th>
                                        <th>Admin Note</th>
                                        <th>Status</th>
                                        <th>Ordered At</th>
                                        <th>Action</th>
                                    



                                    </tr>
                                </thead>
                                <tbody>

                                  
                                    @foreach($packageOrders as $key => $order)
                                    <tr>

                                        <td>{{ $key + 1 }}</td>

                                         @php
                                        $mobileId = $order->mobile_banking_id;
                                        $mobileDetail = DB::table('mobile_bankings')->where('id', $mobileId)->first();
                                     
                                        
                                        @endphp
                                        
                                        <td>
                                            <img src="{{ asset('storage/' . $mobileDetail->image_icon) }}"
                                                alt="{{ $mobileDetail->name }}"
                                                width="50"
                                                height="50">
                                        </td>

                                           @php
    $accountID = $order->account_id;
    $userID = DB::table('accounts')->where('id', $accountID)->value('user_id');
    $user = DB::table('users')->where('id', $userID)->first();
@endphp

<td>{{ $user->name ?? 'N/A' }}</td>

                                        <td>{{ $order->mobile->name }}</td>

                                            <td>{{ $order->number }}</td>
                                        <td>{{ $order->amount }}</td>
                                        <td>{{ $order->note_admin ?? 'N/A' }}</td>
 <td>
                                            @if($order->status == 'pending')
                                                <span class="badge badge-warning">Pending</span>
                                            @elseif($order->status == 'approved')
                                                <span class="badge badge-success">Approved</span>
                                            @else
                                                <span class="badge badge-danger">Rejected</span>
                                            @endif
                                        </td>
                                                                                <td>{{ $order->created_at->format('d M Y, h:i A') }}</td>

                                        <td>
                                            @if($order->status == 'pending')
                                                <a href="{{ route('admin.mobile_banking_orders.approve', $order->id) }}" class="btn btn-success btn-sm">Approve</a>
                                            @elseif($order->status == 'approved')
                                                <a href="{{ route('admin.mobile_banking_orders.reject', $order->id) }}" class="btn btn-danger btn-sm">Reject</a>
                                            @else
                                               <span class="text-muted">No actions</span>
                                            @endif
                                        </td>

                                  
                                     

                                    </tr>
                                    @endforeach
                               

                                </tbody>

                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>

<script>
    $(document).on("click", ".delete-btn", function (e) {
        e.preventDefault();

        let form = $(this).closest("form");

        swal({
            title: "Are you sure?",
            text: "Once deleted, this data cannot be recovered!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                form.submit(); // ✅ CORRECT
            } else {
                swal("Your data is safe!");
            }
        });
    });
</script>



@endsection