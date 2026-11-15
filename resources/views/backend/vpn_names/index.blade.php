@extends('master.backend')

@section('content')
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6"><h1>VPN Name List</h1></div>
                    <div class="col-sm-6 text-sm-right">
                        <a href="{{ route('admin.vpn_names.create') }}" class="btn btn-success">
                            <i class="fas fa-plus"></i> Create VPN Name
                        </a>
                    </div>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="container-fluid"><div class="card"><div class="card-body">
                <div class="table-responsive">
                    <table id="example1" class="table table-bordered table-striped">
                        <thead><tr><th>SL</th><th>Name</th><th>Status</th><th>Accounts</th><th>Available</th><th>Action</th></tr></thead>
                        <tbody>
                            @foreach ($vpnNames as $vpnName)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $vpnName->name }}</td>
                                    <td><span class="badge badge-{{ $vpnName->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($vpnName->status) }}</span></td>
                                    <td>{{ $vpnName->vpns_count }}</td>
                                    <td>{{ $vpnName->available_vpns_count }}</td>
                                    <td class="text-nowrap">
                                        <a href="{{ route('admin.vpn_names.edit', $vpnName) }}" class="btn btn-sm btn-primary" title="Edit"><i class="fas fa-edit"></i></a>
                                        <form action="{{ route('admin.vpn_names.destroy', $vpnName) }}" method="POST" class="delete-form d-inline">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-danger delete-btn" title="Delete" @disabled($vpnName->vpns_count > 0)><i class="fas fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div></div></div>
        </section>
    </div>
    <script>
        $(document).on('click', '.delete-btn', function (event) {
            event.preventDefault();
            const form = $(this).closest('form');
            swal({ title: 'Are you sure?', text: 'This VPN name will be deleted.', icon: 'warning', buttons: true, dangerMode: true })
                .then((confirmed) => confirmed && form.submit());
        });
    </script>
@endsection
