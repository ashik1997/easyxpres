@extends('master.backend')

@section('content')

<div class="content-wrapper">

    <!-- Content Header -->
    <section class="content-header">
      <div class="container-fluid">
        <h1>
            <span class="text-danger" style="border-bottom: 1px dotted red;">
                List Users
            </span>
        </h1>
      </div>
    </section>

    <!-- Flash Message -->
    <section class="content">
      <div class="container-fluid">

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        @endif

        <div class="card">
          <div class="card-body">

            <table id="example1" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Sl</th>
                  <th>Account Number</th>
                  <th>Name</th>
                  <th>Phone</th>
                  <th>Email</th>
                  <th>Balance</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>

              <tbody>
                @foreach($users as $key => $user)
                <tr>

                  <td>{{ $key + 1 }}</td>
                  <td>{{ $user->account->account_number ?? 'N/A' }}</td>
                  <td>{{ $user->name }}</td>
                  <td>{{ $user->phone ?? 'N/A'}}</td>
                  <td>{{ $user->email ?? 'N/A' }}</td>
                  <td>৳ {{ number_format($user->account->balance ?? 0, 2) }}</td>

                  <td>
                    @if($user->status == 'approved')
                        <span class="badge badge-success">Approved</span>
                    @elseif($user->status == 'pending')
                        <span class="badge badge-warning">Pending</span>
                    @else
                        <span class="badge badge-secondary">Unknown</span>
                    @endif
                  </td>

                  <td>

                    {{-- Approve / Reject --}}
                    @if ($user->status === 'pending')

                        <form method="POST"
                              action="{{ route('admin.users.approve', $user->id) }}"
                              style="display:inline;">
                            @csrf
                            <button type="submit"
                                    class="btn btn-sm btn-success"
                                    onclick="return confirm('Approve this user?')">
                                Approve
                            </button>
                        </form>

                        <form method="POST"
                              action="{{ route('admin.users.reject', $user->id) }}"
                              style="display:inline;">
                            @csrf
                            <button type="submit"
                                    class="btn btn-sm btn-danger"
                                    onclick="return confirm('Reject this user?')">
                                Reject
                            </button>
                        </form>

                    @elseif ($user->status === 'approved')

                        <form method="POST"
                              action="{{ route('admin.users.reject', $user->id) }}"
                              style="display:inline;">
                            @csrf
                            <button type="submit"
                                    class="btn btn-sm btn-danger"
                                    onclick="return confirm('Reject this user?')">
                                Reject
                            </button>
                        </form>
                    @endif

                    {{-- Balance Modal Button --}}
                    @if($user->account)
                    <button class="btn btn-sm btn-primary"
                            data-toggle="modal"
                            data-target="#balanceModal"
                            data-id="{{ $user->account->id }}"
                            data-name="{{ $user->name }}"
                            data-balance="{{ $user->account->balance }}">
                        <i class="fas fa-wallet"></i> Balance
                    </button>
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


<!-- ===================== -->
<!-- Balance Update Modal -->
<!-- ===================== -->

<div class="modal fade" id="balanceModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <form method="POST" action="{{ route('admin.users.balance.update') }}">
        @csrf

        <div class="modal-header bg-primary">
          <h4 class="modal-title">Update Balance</h4>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>

        <div class="modal-body">

          <input type="hidden" name="account_id" id="account_id">

          <div class="form-group">
            <label>User Name</label>
            <input type="text" id="user_name"
                   class="form-control" readonly>
          </div>

          <div class="form-group">
            <label>Current Balance</label>
            <input type="text" id="current_balance"
                   class="form-control" readonly>
          </div>

          <div class="form-group">
            <label>Type</label>
            <select name="type" class="form-control" required>
              <option value="add">Add Balance</option>
              <option value="deduct">Deduct Balance</option>
            </select>
          </div>

          <div class="form-group">
            <label>Amount</label>
            <input type="number" name="amount"
                   class="form-control"
                   min="1" required>
          </div>

       
          <div class="form-group">
            <label>Note </label>
            <textarea name="note" class="form-control" rows="3" required></textarea>
          </div>

        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-success">
            Submit
          </button>
        </div>

      </form>

    </div>
  </div>
</div>


<!-- ===================== -->
<!-- Modal Script -->
<!-- ===================== -->

<script>
$('#balanceModal').on('show.bs.modal', function (event) {

    var button = $(event.relatedTarget);

    var accountId = button.data('id');
    var name      = button.data('name');
    var balance   = button.data('balance');

    var modal = $(this);

    modal.find('#account_id').val(accountId);
    modal.find('#user_name').val(name);
    modal.find('#current_balance').val(balance);
});
</script>

@endsection