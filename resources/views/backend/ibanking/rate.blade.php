@extends('master.backend')

@section('content')

{{-- Summernote CSS --}}
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">

{{-- jQuery (required) --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

{{-- Summernote JS --}}
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>

<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<div class="content-wrapper">

    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <h1>
                <span class="text-danger" style="border-bottom:1px dotted red;">
                     Update iBanking Rate
                </span>
            </h1>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">

            <div class="card">
                <div class="card-body">

                    <form action="{{ route('admin.ibanking.update') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Rate (%)</label>
                               <input type="text" name="rate" id="rate" class="form-control" value="{{ $appSetup->rate }}" required>
                        </div>

                       
                        <button type="submit" class="btn btn-primary">
                            Update Rate
                        </button>
                    </form>

                </div>
            </div>

        </div>
    </section>
</div>

{{-- Summernote Init --}}
<script>
    $(document).ready(function () {
        $('#add_balance_content').summernote({
            height: 250,
            placeholder: 'Write add balance content here...',
            toolbar: [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['fontsize', 'color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link']],
                ['view', ['codeview']]
            ]
        });
    });
</script>

{{-- Success Alert --}}
@if(session('success'))
<script>
    swal("Success", "{{ session('success') }}", "success");
</script>
@endif

@endsection
