@extends('master.backend')

@section('content')
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">

                        <h1>
                            <span
                                class="text-danger"
                                style="border-bottom: 1px dotted red;"
                            >Mali Operators List</span>
                        </h1>
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
                                            <th>SL</th>
                                            <th>Operator Name</th>
                                            <th>Code</th>
                                            <th>Commission</th>
                                            <th>Status</th>
                                            <th>Created At</th>
                                            <th>Actions</th>


                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($operators as $key => $operator)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ Str::headline($operator->name) }}</td>
                                                <td>{{ $operator->code }}</td>
                                                <td>{{ $operator->commission }}</td>
                                                <td>
                                                    @if ($operator->status == 'active')
                                                        <span class="badge badge-success">Active</span>
                                                    @else
                                                        <span class="badge badge-danger">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>{{ $operator->created_at->format('d/m/Y H:i') }}</td>
                                                <td>
                                                    <a
                                                        href="{{ route('admin.mali.operators.edit', $operator->id) }}"
                                                        class="btn btn-sm btn-primary"
                                                        title="Edit Operator"
                                                    >
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    @if ($operator->status === 'active')
                                                        <a
                                                            href="{{ route('admin.mali.operators.status', $operator->id) }}"
                                                            class="btn btn-sm btn-warning"
                                                        >Deactivate</a>
                                                    @else
                                                        <a
                                                            href="{{ route('admin.mali.operators.status', $operator->id) }}"
                                                            class="btn btn-sm btn-success"
                                                        >Activate</a>
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
