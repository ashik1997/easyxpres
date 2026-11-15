@extends('master.backend')

@section('content')
<style>
    .users-card { border: 0; border-radius: 12px; box-shadow: 0 4px 18px rgba(15, 23, 42, .08); }
    .users-card .card-header { background: #fff; border-bottom: 1px solid #edf0f4; }
    .users-table thead th { white-space: nowrap; vertical-align: middle; }
    .users-table td { vertical-align: middle; }
    .user-name { font-weight: 600; color: #1f2937; }
    .user-email { color: #6b7280; font-size: 13px; }
    .action-buttons { display: flex; align-items: center; gap: 6px; white-space: nowrap; }
    .action-buttons .btn { width: 34px; height: 34px; padding: 0; display: inline-flex; align-items: center; justify-content: center; border-radius: 7px; }
    .status-badge { min-width: 78px; padding: 7px 10px; border-radius: 20px; font-size: 12px; }
</style>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h1 class="mb-1">Users</h1>
                    <p class="text-muted mb-0">Manage user accounts, status and balance.</p>
                </div>
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#balanceModal">
                    <i class="fas fa-wallet mr-1"></i> Update Balance
                </button>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card users-card">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold">All Users</h3>
                </div>
                <div class="card-body">
                    <table id="example1" class="table table-hover users-table">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>User ID</th>
                                <th>User</th>
                                <th>Phone</th>
                                <th>Payment Proof</th>
                                <th>Register Fee</th>
                                <th>Balance</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $key => $user)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td><span class="badge badge-light">{{ $user->account->account_number ?? 'N/A' }}</span></td>
                                    <td>
                                        <div class="user-name">{{ $user->name }}</div>
                                        <div class="user-email">{{ $user->email ?? 'No email' }}</div>
                                    </td>
                                    <td>{{ $user->phone ?? 'N/A' }}</td>
                                    <td>
                                        @if($user->file_upload)
                                            <button type="button" class="btn btn-sm btn-outline-info" data-toggle="modal" data-target="#screenshotModal{{ $user->id }}">
                                                <i class="far fa-image mr-1"></i> View
                                            </button>
                                        @else
                                            <span class="text-muted">No image</span>
                                        @endif
                                    </td>
                                    <td>৳ {{ number_format($user->register_fee ?? 0, 2) }}</td>
                                    <td class="font-weight-bold">৳ {{ number_format($user->account->balance ?? 0, 2) }}</td>
                                    <td>
                                        @if($user->status === 'approved')
                                            <span class="badge badge-success status-badge"><i class="fas fa-check-circle mr-1"></i>Approved</span>
                                        @elseif($user->status === 'pending')
                                            <span class="badge badge-warning status-badge"><i class="far fa-clock mr-1"></i>Pending</span>
                                        @else
                                            <span class="badge badge-secondary status-badge"><i class="fas fa-ban mr-1"></i>Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button type="button" class="btn btn-outline-warning" data-toggle="modal" data-target="#passwordModal{{ $user->id }}" title="Change password" aria-label="Change password">
                                                <i class="fas fa-key"></i>
                                            </button>
                                            @if($user->status !== 'approved')
                                                <form method="POST" action="{{ route('admin.users.approve', $user->id) }}">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-success" title="Approve user" aria-label="Approve user" onclick="return confirm('Approve this user?')"><i class="fas fa-user-check"></i></button>
                                                </form>
                                            @endif
                                            @if($user->status !== 'rejected')
                                                <form method="POST" action="{{ route('admin.users.reject', $user->id) }}">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-danger" title="Deactivate user" aria-label="Deactivate user" onclick="return confirm('Deactivate this user?')"><i class="fas fa-user-slash"></i></button>
                                                </form>
                                            @endif
                                        </div>
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

@foreach($users as $user)
    <div class="modal fade" id="passwordModal{{ $user->id }}" tabindex="-1" role="dialog" aria-labelledby="passwordModalLabel{{ $user->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.users.password.update', $user->id) }}">
                    @csrf
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title" id="passwordModalLabel{{ $user->id }}">Change Password — {{ $user->name }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group mb-0">
                            <label>New Password</label>
                            <input type="password" name="password" class="form-control" minlength="4" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning"><i class="fas fa-save mr-1"></i> Update Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if($user->file_upload)
        <div class="modal fade" id="screenshotModal{{ $user->id }}" tabindex="-1" role="dialog" aria-labelledby="screenshotModalLabel{{ $user->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="screenshotModalLabel{{ $user->id }}">Payment Proof — {{ $user->name }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="{{ asset('storage/' . $user->file_upload) }}" class="img-fluid rounded" alt="Payment proof for {{ $user->name }}">
                    </div>
                </div>
            </div>
        </div>
    @endif
@endforeach

<div class="modal fade" id="balanceModal" tabindex="-1" role="dialog" aria-labelledby="balanceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.users.balance.update') }}">
                @csrf
                <div class="modal-header bg-primary">
                    <h4 class="modal-title" id="balanceModalLabel"><i class="fas fa-wallet mr-2"></i>Update Balance</h4>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>User ID</label>
                        <input type="text" name="account_number" class="form-control" placeholder="Enter user ID" required>
                    </div>
                    <div class="form-group">
                        <label>Transaction Type</label>
                        <select name="type" class="form-control" required>
                            <option value="add">Add Balance</option>
                            <option value="deduct">Deduct Balance</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Amount</label>
                        <input type="number" name="amount" class="form-control" min="1" step="0.01" placeholder="Enter amount" required>
                    </div>
                    <div class="form-group mb-0">
                        <label>Note</label>
                        <textarea name="note" class="form-control" rows="3" placeholder="Write a short note" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-check mr-1"></i> Update Balance</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
