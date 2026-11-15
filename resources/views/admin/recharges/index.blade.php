@extends('master.backend')

@section('content')
@php
$appSetup = DB::table('app_setups')->first();
$rate = ($app && $app->mvr_to_bdt) ? $app->mvr_to_bdt : 7.98;
@endphp
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">

                    <h1><span class="text-danger" style="border-bottom: 1px dotted red;">BD Recharges List</span></h1>
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
                                        <th>User Name</th>
                                        <th>Mobile</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Requested At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($recharges as $key => $recharge)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                                <td>{{ $recharge->account->user->name ?? 'Unknown' }}</td>
                                            <td>{{ $recharge->mobile }}</td>
                                            <td>
                                                MVR: {{ number_format($recharge->amount, 2) }}<br>
                                                BDT: {{ number_format($recharge->amount * $rate, 2) }}
                                            </td>
                                            <td>
                                                @if ($recharge->status == 'pending')
                                                    <span class="badge badge-warning">Pending</span>
                                                @elseif ($recharge->status == 'approved')
                                                    <span class="badge badge-success">Success</span>
                                                @elseif ($recharge->status == 'rejected')
                                                    <span class="badge badge-danger">Rejected</span>
                                                @endif
                                            </td>
                                            <td>{{ $recharge->created_at->format('Y-m-d H:i') }}</td>
                                            <td>

    @if ($recharge->status === 'pending')
        <a href="{{ route('admin.recharges.approved', $recharge->id) }}" class="btn btn-sm btn-success">Approve</a>
        <a href="{{ route('admin.recharges.rejected', $recharge->id) }}" class="btn btn-sm btn-danger">Reject</a>
        @elseif ($recharge->status == 'approved')
        <a href="{{ route('admin.recharges.rejected', $recharge->id) }}" class="btn btn-sm btn-danger">Reject</a>
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
