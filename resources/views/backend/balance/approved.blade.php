@extends('master.backend')

@section('content')

<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">

                    <h1><span class="text-danger" style="border-bottom: 1px dotted red;">Approved Balance</span></h1>

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

                                        <th>Sl</th>
                                        <th>User Name</th>
                                        <th>Account Number</th>
                                        <th>Amount</th>
                                        <th>Transaction ID</th>
                                        <th>File Upload</th>
                                        <th>Status</th>
                                        <th>Action</th>


                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach($approvedTransactions as $key => $transaction)
                                    <tr>

                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $transaction->user->name ?? 'N/A' }}</td>
                                        <td>{{ $transaction->account->account_number ?? 'N/A' }}</td>
                                        <td>{{ $transaction->amount }}</td>
                                        <td>{{ $transaction->transaction_id }}</td>
                                        <td>
                                            @if($transaction->file_upload)
                                            <a href="{{ route('admin.transaction.download', $transaction->id) }}" class="text-blue-600 hover:underline">
                                                Download File
                                            </a>
                                            @else
                                            N/A
                                            @endif
                                        </td>

                                        <td class="badge bg-success">{{ ucfirst($transaction->status) }}
                                            
                                        </td>

                                        <td>
                                            <form method="POST" action="{{ route('admin.balance.changeStatus', $transaction->id) }}">
            @csrf
            <select name="status" onchange="this.form.submit()" class="border rounded px-2 py-1 text-sm">
                <!-- <option value="pending" {{ $transaction->status == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ $transaction->status == 'approved' ? 'selected' : '' }}>Approved</option> -->
                <option value="" selected disabled>-- Select Rejected --</option>
                <option value="rejected" {{ $transaction->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
        </form>
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
    $(document).on("click", "#delete", function(e) {
        e.preventDefault();
        var link = $(this).attr("href");
        swal({
                title: "Are you want to delete?",
                text: "Once Delete, This will be Permanently Delete!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((willDelete) => {
                if (willDelete) {
                    window.location.href = link;
                } else {
                    swal("Safe Data!");
                }
            });
    });
</script>




@endsection