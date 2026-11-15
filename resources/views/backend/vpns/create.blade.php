@extends('master.backend')

@section('content')
    <div class="content-wrapper">
        <section class="content-header"><div class="container-fluid"><h1>Create VPN Account</h1></div></section>
        <section class="content"><div class="container-fluid"><div class="card"><div class="card-body">
            @if ($vpnNames->isEmpty())
                <div class="alert alert-warning">Create an active VPN name before adding an account.</div>
                <a href="{{ route('admin.vpn_names.create') }}" class="btn btn-success">Create VPN Name</a>
            @else
                <form action="{{ route('admin.vpns.store') }}" method="POST">
                    @csrf
                    @include('backend.vpns._form', ['submitLabel' => 'Create VPN Account'])
                </form>
            @endif
        </div></div></div></section>
    </div>
@endsection
