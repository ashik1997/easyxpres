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
                    Social Link Update
                </span>
            </h1>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">

            <div class="card">
                <div class="card-body">

                    <form action="{{ route('admin.setup.social.update') }}" method="POST">
                        @csrf

                        {{-- whatsapp --}}
                        <div class="mb-3">
                            <label class="form-label" for="whatsapp">WhatsApp support number</label>
                            <input type="tel" name="whatsapp" id="whatsapp" class="form-control" value="{{ $appSetup->whatsapp ?? '' }}" placeholder="e.g. +960 777 1234" inputmode="tel">
                            <small class="form-text text-muted">Use the full international number, including country code. Leave blank to hide the support button.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Facebook</label>
                               <input type="text" name="facebook" id="facebook" class="form-control" value="{{ $appSetup->facebook }}" placeholder="Facebook Link" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Youtube</label>
                               <input type="text" name="youtube" id="youtube" class="form-control" value="{{ $appSetup->youtube }}" placeholder="Youtube Link" required>
                        </div>

                         <div class="mb-3">
                            <label class="form-label">Telegram</label>
                               <input type="text" name="telegram" id="telegram" class="form-control" value="{{ $appSetup->telegram }}" placeholder="Telegram Link" required>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            Update Social
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
