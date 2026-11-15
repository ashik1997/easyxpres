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
                    Register Balance Content Update
                </span>
            </h1>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">

            <div class="card">
                <div class="card-body">

                    <form action="{{ route('admin.setup.content.register.update') }}" method="POST">
                        @csrf

                        <div class="mb-3"
                                style="border:1px dashed #ccc; padding:10px; margin-bottom:20px;">
                                <label class="form-label">Register Fee </label>
                                <input type="number" step="0.01" name="register_fee" class="form-control"
                                    value="{{ old('register_fee', $appSetup->register_fee ?? '') }}"
                                    placeholder="Enter register fee ">

                        <div class="mb-3">
                            <label class="form-label">Add Register Balance Content</label>
                            <textarea name="registered_balance_content"
                                      id="registered_balance_content"
                                      class="form-control">
                                {{ old('registered_balance_content', $appSetup->registered_balance_content ?? '') }}
                            </textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            Update Register Content
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
        $('#registered_balance_content').summernote({
            height: 250,
            placeholder: 'Write registered balance content here...',
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
