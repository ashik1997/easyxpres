@extends('master.backend')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 align-items-center">
                <div class="col-sm-6">
                    <h1><span class="text-info" style="border-bottom: 1px dotted #17a2b8;">Add Balance History</span></h1>
                </div>
                <div class="col-sm-6 text-sm-right mt-2 mt-sm-0">
                    <span class="badge badge-info p-2">Total Records: {{ $transactions->count() }}</span>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <table id="example1" class="table table-bordered table-striped w-100">
                        <thead>
                            <tr>
                                <th data-priority="2">SL</th>
                                <th>User Name</th>
                                <th>Account Number</th>
                                <th>Amount</th>
                                <th>Balance After</th>
                                <th>Transaction ID</th>
                                <th>Note</th>
                                <th>Status</th>
                                <th>Date &amp; Time</th>
                                <th data-priority="1">Screenshot</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transactions as $key => $transaction)
                                @php
                                    $statusClass = match ($transaction->status) {
                                        'approved' => 'badge-success',
                                        'rejected' => 'badge-danger',
                                        default => 'badge-warning',
                                    };
                                @endphp
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $transaction->account?->user?->name ?? 'N/A' }}</td>
                                    <td>{{ $transaction->account?->account_number ?? 'N/A' }}</td>
                                    <td class="text-nowrap">{{ number_format((float) $transaction->amount, 2) }}</td>
                                    <td class="text-nowrap">{{ number_format((float) $transaction->balance_after, 2) }}</td>
                                    <td>{{ $transaction->transaction_id }}</td>
                                    <td>{{ $transaction->note ?? 'N/A' }}</td>
                                    <td><span class="badge {{ $statusClass }}">{{ ucfirst($transaction->status) }}</span></td>
                                    <td class="text-nowrap" data-order="{{ $transaction->created_at?->timestamp ?? 0 }}">
                                        {{ $transaction->created_at?->format('d M Y, h:i A') ?? 'N/A' }}
                                    </td>
                                    <td>
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
                                                title="View Screenshot">
                                                <i class="fas fa-eye"></i> View
                                            </button>
                                        @else
                                            <span class="text-muted">No Image</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

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
                <img id="transactionScreenshot" src="" class="img-fluid rounded" alt="Transaction Screenshot" style="max-height: 70vh;">
            </div>
        </div>
    </div>
</div>

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
@endsection
