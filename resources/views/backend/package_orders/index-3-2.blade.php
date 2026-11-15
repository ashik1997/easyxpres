@extends('master.backend')

@section('content')

<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">

                    <h1><span class="text-danger" style="border-bottom: 1px dotted red;">Packages Order</span></h1>

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

                 <div class="row">
    @foreach($packages as $package)
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm rounded-lg">
                <div class="card-body text-center">
                    {{-- Package Image --}}
                    <a href="{{ url('admin/package/orders/' . $package->id) }}">
                        <img src="{{ asset('storage/' . $package->image_icon) }}"
                             alt="{{ $package->name }}"
                             class="img-fluid mb-3"
                             style="max-height: 100px;">
                    </a>

                    {{-- Package Name --}}
                    <h5 class="card-title">{{ $package->name }}</h5>

                    {{-- Optional: Package Description --}}
                    @if(!empty($package->description))
                        <p class="card-text text-muted">{{ $package->description }}</p>
                    @endif

                    {{-- Optional: Button --}}
                    <a href="{{ url('admin/package/orders/' . $package->id) }}" class="btn btn-primary btn-sm">
    View Orders
</a>

                </div>
            </div>
        </div>
    @endforeach
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