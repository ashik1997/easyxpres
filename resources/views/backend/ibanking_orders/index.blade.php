@extends('master.backend')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="d-flex flex-wrap align-items-center justify-content-between">
                <div>
                    <h1 class="mb-1">iBanking Orders</h1>
                    <p class="text-muted mb-0">Review recipient details, documents and transfer status.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Transfer requests</h3>
                    <span class="badge badge-info">{{ $packageOrders->count() }} total</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="example1" class="table table-hover table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Account Holder</th>
                                    <th>Bank / Branch</th>
                                    <th>Account Number</th>
                                    <th>Amount</th>
                                    <th>Documents</th>
                                    <th>Status</th>
                                    <th>Ordered At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($packageOrders as $order)
                                    @php
                                        $holderName = $order->account_holder_name ?: optional(optional($order->account)->user)->name;
                                        $bankName = optional($order->bank)->bank_name ?: 'Bank Deleted';
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="font-weight-bold">{{ $holderName ?: 'Not provided' }}</div>
                                            <small class="text-muted">Order #{{ $order->id }}</small>
                                        </td>
                                        <td>
                                            <div class="font-weight-bold">{{ $bankName }}</div>
                                            <small class="text-muted">
                                                <i class="fas fa-location-dot mr-1"></i>{{ $order->branch ?: 'Branch not provided' }}
                                            </small>
                                        </td>
                                        <td class="font-weight-bold">{{ $order->account_no }}</td>
                                        <td class="text-nowrap">
                                            <div>{{ number_format((float) $order->amount, 2) }} MVR</div>
                                            <small class="text-success">{{ number_format((float) ($order->bdt_amount ?? 0), 2) }} BDT</small>
                                        </td>
                                        <td class="text-nowrap">
                                            @if ($order->document_attachment)
                                                <div class="mb-1">
                                                    <a href="{{ route('admin.ibanking_orders.attachment', [$order->id, 'document']) }}" target="_blank" rel="noopener" class="btn btn-info btn-sm">
                                                        <i class="fas fa-eye mr-1"></i>View document
                                                    </a>
                                                    <a href="{{ route('admin.ibanking_orders.attachment.download', [$order->id, 'document']) }}" class="btn btn-outline-info btn-sm" title="Download document">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                </div>
                                            @else
                                                <span class="text-muted">No document</span>
                                            @endif
                                            @if ($order->upload_slip)
                                                <div>
                                                    <a href="{{ route('admin.ibanking_orders.attachment', [$order->id, 'slip']) }}" target="_blank" rel="noopener" class="small">
                                                        <i class="fas fa-file-image mr-1"></i>View processing slip
                                                    </a>
                                                    <a href="{{ route('admin.ibanking_orders.attachment.download', [$order->id, 'slip']) }}" class="small ml-2" title="Download processing slip">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($order->status === 'pending')
                                                <span class="badge badge-warning">Pending</span>
                                            @elseif ($order->status === 'approved')
                                                <span class="badge badge-success">Approved</span>
                                            @else
                                                <span class="badge badge-danger">Rejected</span>
                                            @endif
                                        </td>
                                        <td class="text-nowrap">{{ optional($order->created_at)->format('d M Y, h:i A') }}</td>
                                        <td class="text-nowrap">
                                            @if ($order->status === 'pending')
                                                <a href="{{ route('admin.ibanking_orders.approve', $order->id) }}" class="btn btn-success btn-sm">Approve</a>
                                                <a href="{{ route('admin.ibanking_orders.reject', $order->id) }}" class="btn btn-danger btn-sm">Reject</a>
                                            @elseif ($order->status === 'approved')
                                                <a href="{{ route('admin.ibanking_orders.reject', $order->id) }}" class="btn btn-danger btn-sm">Reject</a>
                                            @else
                                                <span class="text-muted">No actions</span>
                                            @endif
                                            <button type="button" class="btn btn-primary btn-sm mt-1" data-toggle="modal" data-target="#slipModal" data-id="{{ $order->id }}">
                                                <i class="fas fa-upload mr-1"></i>Slip
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="9" class="text-center text-muted py-5">No iBanking orders found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="slipModal" tabindex="-1" role="dialog" aria-labelledby="slipModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="slipForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="slipModalLabel">Upload processing slip</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <label for="upload_slip">Slip image</label>
                    <input id="upload_slip" type="file" class="form-control" name="upload_slip" accept=".jpg,.jpeg,.png" required>
                    <small class="form-text text-muted">JPG or PNG, maximum 2 MB.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Upload slip</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $('#slipModal').on('show.bs.modal', function (event) {
        const orderId = $(event.relatedTarget).data('id');
        const actionUrl = @json(route('admin.ibanking_orders.upload_slip', ':id')).replace(':id', orderId);
        $('#slipForm').attr('action', actionUrl);
    });
</script>
@endsection
