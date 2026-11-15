@extends('master.backend')

@section('content')

<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">

                    <h1><span class="text-danger" style="border-bottom: 1px dotted red;">List Balance</span></h1>

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
                            <table id="example1" class="table table-bordered table-striped w-100">
                                <thead>
                                    <tr>

                                        <th data-priority="2">Sl</th>
                                        <th>User Name</th>
                                        <th>User ID</th>
                                        <th>Amount</th>
                                        <th>Transaction ID</th>
                                        <th data-priority="3">Screenshot</th>
                                        <th>Note</th>
                                        <th>Status</th>
                                        <th data-priority="1">Action</th>


                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach($pendingTransactions as $key => $transaction)
                                    <tr>

                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $transaction->account?->user?->name ?? 'N/A' }}</td>
                                        <td>{{ $transaction->account?->account_number ?? 'N/A' }}</td>
                                        <td>{{ $transaction->amount }}</td>
                                        <td>{{ $transaction->transaction_id }}</td>
                                        <td>
                                            @if($transaction->file_upload)
                                                <span class="badge badge-info">Available</span>
                                            @else
                                                <span class="text-muted">No Image</span>
                                            @endif
                                        </td>

<td>
{{ $transaction->note ?? 'N/A' }}
</td>



                            
                                        <td>
                                            @if ($transaction->status === 'approved')
                                                <span class="badge badge-success">Success</span>
                                            @else
                                                <span class="badge badge-warning">{{ ucfirst($transaction->status) }}</span>
                                            @endif
                                        </td>

                                        <td class="text-nowrap balance-actions">

            @if ($transaction->file_upload)
                @php
                    $image = asset('storage/' . ltrim(str_replace('public/', '', $transaction->file_upload), '/'));
                @endphp
                <button type="button"
                    class="btn btn-sm btn-info"
                    data-toggle="modal"
                    data-target="#screenshotModal"
                    data-image="{{ $image }}"
                    data-transaction="{{ $transaction->transaction_id }}"
                    title="View Screenshot"
                    aria-label="View Screenshot">
                    <i class="fas fa-eye"></i><span class="d-none d-lg-inline"> View</span>
                </button>
            @endif

                                           

            @if ($transaction->status === 'pending')
        <a href="{{ route('admin.balance.approved', $transaction->id) }}" class="btn btn-sm btn-success" title="Approve" aria-label="Approve"><i class="fas fa-check"></i><span class="d-none d-lg-inline"> Approve</span></a>
        <a href="{{ route('admin.balance.rejected', $transaction->id) }}" class="btn btn-sm btn-danger" title="Reject" aria-label="Reject"><i class="fas fa-times"></i><span class="d-none d-lg-inline"> Reject</span></a>
        @elseif ($transaction->status == 'approved')
        <a href="{{ route('admin.balance.rejected', $transaction->id) }}" class="btn btn-sm btn-danger" title="Reject" aria-label="Reject"><i class="fas fa-times"></i><span class="d-none d-lg-inline"> Reject</span></a>
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

<!-- Shared Transaction Screenshot Modal -->
<div class="modal fade" id="screenshotModal" tabindex="-1" role="dialog" aria-labelledby="screenshotModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title text-white" id="screenshotModalLabel">Transaction Screenshot</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center p-2 p-sm-3">
                <img id="transactionScreenshot" src="" class="img-fluid rounded" alt="Transaction Screenshot" style="max-height:70vh;">
            </div>
        </div>
    </div>
</div>

<style>
    .balance-actions .btn { margin: 1px; }

    @media (max-width: 767.98px) {
        .content-wrapper .card-body { padding: .75rem; }
        .balance-actions { min-width: 96px; }
        #screenshotModal .modal-dialog { margin: .75rem; }
    }
</style>

<script>
    $('#screenshotModal').on('show.bs.modal', function (event) {
        const button = $(event.relatedTarget);
        const transactionId = button.data('transaction');

        $(this).find('#transactionScreenshot').attr('src', button.data('image'));
        $(this).find('#screenshotModalLabel').text('Transaction Screenshot - ' + transactionId);
    }).on('hidden.bs.modal', function () {
        $(this).find('#transactionScreenshot').attr('src', '');
    });
</script>

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
