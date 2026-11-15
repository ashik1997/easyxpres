@extends('master.backend')

@section('content')

<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">

                    <h1><span class="text-danger" style="border-bottom: 1px dotted red;">Mobile Banking Create</span></h1>

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
                            <form action="{{ route('admin.mobile.banking.store') }}"
                                method="POST"
                                enctype="multipart/form-data">
                                @csrf

                                <div class="form-group">
                                    <label for="name">Mobile Banking Name</label>
                                    <input type="text" name="name" id="name" class="form-control" required>
                                </div>

                                 <div class="form-group">
                                    <label for="rate">Rate (%)</label>
                                    <input type="text" name="rate" id="rate" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label for="image_icon">Image</label>
                                    <input type="file" name="image_icon" id="image_icon" class="form-control" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Create Package</button>
                            </form>
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
@endsection