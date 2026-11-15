@extends('master.backend')

@section('content')

<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">

                    <h1><span class="text-danger" style="border-bottom: 1px dotted red;">Mobile Banking List</span></h1>

                </div>


                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <a href="{{ route('admin.mobile.banking.create') }}" class="btn btn-success"> <i
                                class="fas fa-plus"></i> Create New Mobile Banking</a>
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
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>

                                        <th>Sl</th>
                                        <th>Image Icon</th>
                                        <th>Mobile Banking Name</th>
                                        <th>Rate (%)</th>
                                        <th>Status</th>
                                        <th>Status Change</th>
                                        <th>Action</th>



                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($mobileBanking as $package)
                                    <tr>

                                        
                                        <td>{{ $loop->index + 1 }}</td>
                                        <td>
                                            <img src="{{ asset('storage/' . $package->image_icon) }}"
                                                alt="{{ $package->name }}"
                                                width="50"
                                                height="50">
                                        </td>
                                        <td>{{ $package->name }}</td>
                                        <td>{{ $package->rate }}</td>
                                        <td>
                                            @if ($package->status === 'active')
                                            <span class="badge badge-success">Active</span>
                                            @else
                                            <span class="badge badge-danger">Inactive</span>
                                            @endif
                                        </td>

                                        <td>
                                            @if ($package->status === 'active')
                                            <a href="{{ route('admin.mobile.banking.changeStatus', $package->id) }}"
                                                class="btn btn-sm btn-danger"
                                                title="Click to Inactive">
                                                <i class="fas fa-arrow-down"></i>
                                            </a>
                                            @else
                                            <a href="{{ route('admin.mobile.banking.changeStatus', $package->id) }}"
                                                class="btn btn-sm btn-success"
                                                title="Click to Active">
                                                <i class="fas fa-arrow-up"></i>
                                            </a>
                                            @endif
                                        </td>

                                            <td>
                                           
                                             <a href="{{ route('admin.mobile.banking.edit', $package->id) }}"
                                                class="btn btn-sm btn-primary"
                                                title="Edit Package">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                           

                                            <form action="{{ route('admin.mobile.banking.destroy', $package->id) }}"
                                                method="POST"
                                                class="delete-form"
                                                style="display:inline;">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                    class="btn btn-sm btn-danger delete-btn"
                                                    title="Delete Data">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
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
    $(document).on("click", ".delete-btn", function (e) {
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