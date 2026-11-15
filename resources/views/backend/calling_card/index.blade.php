@extends('master.backend')

@section('content')
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">

                        <h1><span
                                class="text-danger"
                                style="border-bottom: 1px dotted red;"
                            >Calling Card List</span></h1>

                    </div>


                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <a
                                href="{{ route('admin.calling_card.create') }}"
                                class="btn btn-success"
                            > <i class="fas fa-plus"></i> Create New Calling Card</a>
                        </ol>
                    </div>

                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">

                        <!-- /.card -->

                        <div class="card">

                            <!-- /.card-header -->
                            <div class="card-body">
                                <table
                                    id="example1"
                                    class="table table-bordered table-striped"
                                >
                                    <thead>
                                        <tr>

                                            <th>Sl</th>
                                            <th>Operator Code</th>
                                            <th>User Id</th>
                                            <th>User Name</th>
                                            <th>Password</th>
                                            <th>Point</th>
                                            <th>Minute/Sec</th>
                                            <th>Payment</th>
                                            <th>Buy Status</th>
                                            <th>Action</th>



                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- ==== Controller & Method reference ==== --}}
                                        {{-- ====  Controller: App\Http\Controllers\CallingCardController ==== --}}
                                        {{-- ==== Method: indexCallingCards() ==== --}}

                                        @foreach ($callingCards as $calling)
                                            <tr
                                                class="{{ $calling->created_at->diffInHours(now()) < 24 ? 'bg-primary' : '' }}">
                                                <td>{{ $loop->index + 1 }}</td>
                                                <td>{{ $calling->card_no }}</td>
                                                <td>{{ @$calling->user->account->account_number }}</td>
                                                <td>{{ $calling->user_name }}</td>
                                                <td>{{ $calling->pin }}</td>
                                                <td>{{ $calling->point }}</td>
                                                <td>{{ $calling->minute_sec }}</td>
                                                <td>{{ $calling->payment ?? '0' }}</td>

                                                @if ($calling->buy_now_status === '1')
                                                    <td><span class="badge badge-danger">Stock Out</span></td>
                                                @else
                                                    <td><span class="badge badge-success">Not Buy</span></td>
                                                @endif

                                                <td>
                                                    @if ($calling->buy_now_status === '1')
                                                        <button
                                                            class="btn btn-sm btn-secondary"
                                                            disabled
                                                            title="Cannot Edit"
                                                        >
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                    @else
                                                        <a
                                                            href="{{ route('admin.calling_card.edit', $calling->id) }}"
                                                            class="btn btn-sm btn-primary"
                                                            title="Edit Calling Card"
                                                        >
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    @endif

                                                    @if ($calling->buy_now_status === 1)
                                                        <button
                                                            class="btn btn-sm btn-secondary"
                                                            disabled
                                                            title="Cannot Delete"
                                                        >
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    @else
                                                        <form
                                                            action="{{ route('admin.calling_card.destroy', $calling->id) }}"
                                                            method="POST"
                                                            class="delete-form"
                                                            style="display:inline;"
                                                        >
                                                            @csrf
                                                            @method('DELETE')

                                                            <button
                                                                type="submit"
                                                                class="btn btn-sm btn-danger delete-btn"
                                                                title="Delete Data"
                                                            >
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </td>




                                            </tr>
                                        @endforeach


                                    </tbody>

                                </table>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>

    <script>
        $(document).on("click", ".delete-btn", function(e) {
            e.preventDefault();

            let form = $(this).closest("form");

            swal({
                title: "Are you sure?",
                text: "Once deleted, this data cannot be recovered!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    form.submit(); // ✅ CORRECT
                } else {
                    swal("Your data is safe!");
                }
            });
        });
    </script>
@endsection
