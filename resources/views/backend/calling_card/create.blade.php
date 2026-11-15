@extends('master.backend')

@section('content')

<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">

                    <h1><span class="text-danger" style="border-bottom: 1px dotted red;">Calling Card Create</span></h1>

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
                            <form action="{{ route('admin.calling_card.store') }}"
                                method="POST">
                                @csrf

                                <div class="form-group">
                                    <label for="card_no">Operator Code</label>
                                    <input type="text" name="card_no" id="card_no" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label for="user_name">User Name </label>
                                    <input type="text" name="user_name" id="user_name" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label for="pin">Password </label>
                                    <input type="text" name="pin" id="pin" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label for="payment">Payment </label>
                                    <input type="text" name="payment" id="payment" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label for="point">Point </label>
                                    <input type="text" name="point" id="point" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label for="minute_sec">Minute/Second </label>
                                    <input type="text" name="minute_sec" id="minute_sec" class="form-control" placeholder="mm:ss" required>
                                </div>
                               
                                <button type="submit" class="btn btn-primary">Create Calling Card</button>
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