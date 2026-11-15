@extends('master.backend')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid"><h1>Create VPN Name</h1></div>
        </section>
        <section class="content">
            <div class="container-fluid">
                <div class="card"><div class="card-body">
                    <form action="{{ route('admin.vpn_names.store') }}" method="POST">
                        @csrf
                        @include('backend.vpn_names._form', ['submitLabel' => 'Create VPN Name'])
                    </form>
                </div></div>
            </div>
        </section>
    </div>
@endsection
