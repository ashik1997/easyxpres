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
                    Play Game Update
                </span>
            </h1>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">

            <div class="card">
                <div class="card-body">

                    <form action="{{ route('admin.setup.play_game.update') }}" method="POST">
                        @csrf

                      
                        <div class="mb-3">
                            <label class="form-label">Play Game Status</label>
                            <select name="play_game_status" class="form-control">
                                <option value="1" {{ (old('play_game_status', $appSetup->play_game_status ?? '') == 1) ? 'selected' : '' }}>On</option>
                                <option value="0" {{ (old('play_game_status', $appSetup->play_game_status ?? '') == 0) ? 'selected' : '' }}>Off</option>
                            </select>
                        </div>


                        <div class="mb-3">
                            <label class="form-label">Play Game Link</label>
                            <input type="url" name="play_game_link" class="form-control" value="{{ old('play_game_link', $appSetup->play_game_link ?? '') }}" placeholder="Enter play game link">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Add Play Game Content</label>
                            <textarea name="play_game_content"
                                      id="play_game_content"
                                      class="form-control">
                                {{ old('play_game_content', $appSetup->play_game_content ?? '') }}
                            </textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            Update Play Game 
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
        $('#play_game_content').summernote({
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
