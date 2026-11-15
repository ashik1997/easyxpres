@extends('master.backend')

@section('content')
<style>
    .eps-card { border: 0; border-radius: 12px; box-shadow: 0 4px 18px rgba(15, 23, 42, .08); }
    .eps-card .card-header { background: #fff; border-bottom: 1px solid #edf0f4; }
    .eps-table thead th { white-space: nowrap; vertical-align: middle; }
    .eps-table td { vertical-align: middle; }
    .eps-user { font-weight: 600; color: #1f2937; }
    .eps-reference { display: block; max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .eps-status { min-width: 84px; padding: 7px 10px; border-radius: 20px; font-size: 12px; }
    .details-button { width: 34px; height: 34px; padding: 0; display: inline-flex; align-items: center; justify-content: center; border-radius: 7px; }
    .detail-label { color: #6b7280; font-size: 13px; margin-bottom: 3px; }
    .detail-value { color: #1f2937; word-break: break-word; }
</style>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h1 class="mb-1">EPS Payments</h1>
                    <p class="text-muted mb-0">Review automated add balance transactions.</p>
                </div>
                <span class="badge badge-success px-3 py-2"><i class="fas fa-circle mr-1"></i> Production</span>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card eps-card">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold">Transaction Audit</h3>
                </div>
                <div class="card-body">
                    <table id="example1" class="table table-hover eps-table">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>User</th>
                                <th>Account</th>
                                <th>Merchant Reference</th>
                                <th>Paid Amount</th>
                                <th>Balance Credit</th>
                                <th>Status</th>
                                <th>Callbacks</th>
                                <th>Created</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($payments as $key => $payment)
                                @php
                                    $badge = match ($payment->status) {
                                        \App\Enums\PaymentStatus::Successful => 'badge-success',
                                        \App\Enums\PaymentStatus::Failed => 'badge-danger',
                                        \App\Enums\PaymentStatus::Cancelled => 'badge-secondary',
                                        \App\Enums\PaymentStatus::Expired => 'badge-dark',
                                        \App\Enums\PaymentStatus::Processing => 'badge-info',
                                        default => 'badge-warning',
                                    };
                                @endphp
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td><span class="eps-user">{{ $payment->user->name ?? 'N/A' }}</span></td>
                                    <td><span class="badge badge-light">{{ $payment->account->account_number ?? 'N/A' }}</span></td>
                                    <td><code class="eps-reference" title="{{ $payment->transaction_id }}">{{ $payment->transaction_id }}</code></td>
                                    <td>{{ $payment->currency }} {{ number_format((float) $payment->amount, 2) }}</td>
                                    <td>
                                        <strong>{{ $payment->balance_currency }} {{ number_format((float) $payment->balance_amount, 2) }}</strong>
                                        @if ($payment->exchange_rate)
                                            <small class="d-block text-muted">1 MVR = BDT {{ number_format((float) $payment->exchange_rate, 6) }}</small>
                                        @endif
                                    </td>
                                    <td><span class="badge {{ $badge }} eps-status">{{ $payment->status->label() }}</span></td>
                                    <td class="text-center">{{ $payment->callback_count }}</td>
                                    <td class="text-nowrap">{{ $payment->created_at->format('d M Y, h:i A') }}</td>
                                    <td>
                                        <button type="button" class="btn btn-outline-primary details-button" data-toggle="modal" data-target="#paymentModal{{ $payment->id }}" title="View details" aria-label="View payment details">
                                            <i class="fas fa-eye"></i>
                                        </button>
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

@foreach ($payments as $payment)
    @php
        $badge = match ($payment->status) {
            \App\Enums\PaymentStatus::Successful => 'badge-success',
            \App\Enums\PaymentStatus::Failed => 'badge-danger',
            \App\Enums\PaymentStatus::Cancelled => 'badge-secondary',
            \App\Enums\PaymentStatus::Expired => 'badge-dark',
            \App\Enums\PaymentStatus::Processing => 'badge-info',
            default => 'badge-warning',
        };
    @endphp
    <div class="modal fade" id="paymentModal{{ $payment->id }}" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel{{ $payment->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="paymentModalLabel{{ $payment->id }}">EPS Payment Details</h5>
                        <small class="text-muted">{{ $payment->user->name ?? 'Unknown user' }}</small>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="detail-label">Merchant Reference</div>
                            <code class="detail-value">{{ $payment->transaction_id }}</code>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="detail-label">EPS Reference</div>
                            <code class="detail-value">{{ $payment->gateway_transaction_id ?? 'Not available' }}</code>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="detail-label">Checkout Session</div>
                            <code class="detail-value">{{ $payment->gateway_session_id ?? 'Not available' }}</code>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="detail-label">Status</div>
                            <span class="badge {{ $badge }} eps-status">{{ $payment->status->label() }}</span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="detail-label">Paid Amount</div>
                            <div class="detail-value font-weight-bold">{{ $payment->currency }} {{ number_format((float) $payment->amount, 2) }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="detail-label">Balance Credit</div>
                            <div class="detail-value font-weight-bold">{{ $payment->balance_currency }} {{ number_format((float) $payment->balance_amount, 2) }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="detail-label">Created</div>
                            <div class="detail-value">{{ $payment->created_at->format('d M Y, h:i A') }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="detail-label">Verified</div>
                            <div class="detail-value">{{ $payment->verified_at?->format('d M Y, h:i A') ?? 'Not verified yet' }}</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endforeach
@endsection
