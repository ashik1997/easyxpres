@extends('master.backend')

@section('content')
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <div class="content-wrapper">
        <section class="content-header"><div class="container-fluid"><div class="row mb-2">
            <div class="col-sm-6"><h1>VPN Account List</h1></div>
            <div class="col-sm-6 text-sm-right"><a href="{{ route('admin.vpns.create') }}" class="btn btn-success"><i class="fas fa-plus"></i> Create VPN Account</a></div>
        </div></div></section>
        <section class="content"><div class="container-fluid"><div class="card"><div class="card-body"><div class="table-responsive">
            <table id="example1" class="table table-bordered table-striped">
                <thead><tr><th>SL</th><th>VPN</th><th>Username</th><th>Password</th><th>Expiry</th><th>Payment</th><th>Status</th><th>Buyer</th><th>Action</th></tr></thead>
                <tbody>
                    @foreach ($vpns as $vpn)
                        @php($expired = $vpn->expire_date->isBefore(today()))
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $vpn->vpnName->name }}</td>
                            <td>{{ $vpn->user_name }}</td>
                            <td>{{ $vpn->password }}</td>
                            <td><span class="badge badge-{{ $expired ? 'danger' : 'info' }}">{{ $vpn->expire_date->format('d M Y') }}</span></td>
                            <td>MVR {{ number_format((float) $vpn->payment, 2) }}</td>
                            <td>
                                @if ($vpn->buy_now_status)
                                    <span class="badge badge-danger">Sold</span>
                                @elseif ($expired)
                                    <span class="badge badge-warning">Expired</span>
                                @else
                                    <span class="badge badge-{{ $vpn->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($vpn->status) }}</span>
                                @endif
                            </td>
                            <td>{{ $vpn->user?->account?->account_number ?? '—' }}</td>
                            <td class="text-nowrap">
                                @if ($vpn->buy_now_status)
                                    <button class="btn btn-sm btn-secondary" disabled><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-sm btn-secondary" disabled><i class="fas fa-trash"></i></button>
                                @else
                                    <a href="{{ route('admin.vpns.edit', $vpn) }}" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('admin.vpns.destroy', $vpn) }}" method="POST" class="delete-form d-inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger delete-btn"><i class="fas fa-trash"></i></button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div></div></div></div></section>
    </div>
    <script>
        $(document).on('click', '.delete-btn', function (event) {
            event.preventDefault();
            const form = $(this).closest('form');
            swal({ title: 'Are you sure?', text: 'This VPN account will be deleted.', icon: 'warning', buttons: true, dangerMode: true })
                .then((confirmed) => confirmed && form.submit());
        });
    </script>
@endsection
