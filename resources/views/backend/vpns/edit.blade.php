@extends('master.backend')

@section('content')
    <div class="content-wrapper">
        <section class="content-header"><div class="container-fluid"><h1>Update VPN Account</h1></div></section>
        <section class="content"><div class="container-fluid"><div class="card"><div class="card-body">
            <form action="{{ route('admin.vpns.update', $vpn) }}" method="POST">
                @csrf @method('PUT')
                @include('backend.vpns._form', ['submitLabel' => 'Update VPN Account'])
            </form>
        </div></div></div></section>
    </div>
@endsection
