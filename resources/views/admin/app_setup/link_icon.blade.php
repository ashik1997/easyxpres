@extends('master.backend')

@section('content')

{{-- jQuery (AdminLTE needs jQuery first) --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

{{-- Bootstrap JS & AdminLTE already loaded in master template --}}

{{-- Summernote --}}
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>

{{-- SweetAlert --}}
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

{{-- Font Awesome CSS (AdminLTE already has it, but add if missing) --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

<div class="content-wrapper">

    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <h1>
                <span class="text-danger" style="border-bottom:1px dotted red;">
                    Icon & Link Update
                </span>
            </h1>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">

            <div class="card">
                <div class="card-body">

                    <form action="{{ route('admin.setup.link_icon.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label>Image Icon</label>

                            <input type="file" name="icon" id="icon" class="form-control">

                            <div class="mt-2">
                                <img id="iconPreview"
                                    src="{{ isset($appSetup->icon) ? asset('storage/' . $appSetup->icon) : '' }}"
                                    alt="icon"
                                    width="80"
                                    height="80"
                                    style="object-fit:cover;border:1px solid #ddd;padding:3px;">
                            </div>
                        </div>

                        <div class="modal fade" id="iconModal" tabindex="-1">
                            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Select an Icon</h5>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row" id="iconList"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Link</label>
                            <input type="text" name="link" id="link" class="form-control" value="{{ $appSetup->link }}" required>
                        </div>



                        <button type="submit" class="btn btn-primary">
                            Update
                        </button>
                    </form>

                </div>
            </div>

        </div>
    </section>
</div>

{{-- Summernote Init --}}
<script>
    $(document).ready(function() {
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



<script>
    $(document).ready(function() {
        // List of icons
        const icons = [
            'fas fa-phone-volume', 'fas fa-user', 'fas fa-users', 'fas fa-user-circle', 'fas fa-user-cog', 'fas fa-user-shield',
            'fas fa-home', 'fas fa-building', 'fas fa-store', 'fas fa-warehouse', 'fas fa-school',
            'fas fa-cog', 'fas fa-tools', 'fas fa-wrench', 'fas fa-sliders-h', 'fas fa-tasks',
            'fas fa-envelope', 'fas fa-paper-plane', 'fas fa-inbox', 'fas fa-envelope-open', 'fas fa-mail-bulk',
            'fas fa-chart-line', 'fas fa-chart-bar', 'fas fa-chart-pie', 'fas fa-chart-area', 'fas fa-balance-scale',
            'fas fa-bell', 'fas fa-bell-slash', 'fas fa-broadcast-tower', 'fas fa-broadcast', 'fas fa-bolt',
            'fas fa-calendar', 'fas fa-calendar-alt', 'fas fa-calendar-check', 'fas fa-calendar-plus', 'fas fa-calendar-times',
            'fas fa-book', 'fas fa-book-open', 'fas fa-book-reader', 'fas fa-book-dead', 'fas fa-briefcase',
            'fas fa-camera', 'fas fa-camera-retro', 'fas fa-video', 'fas fa-video-slash', 'fas fa-film',
            'fas fa-car', 'fas fa-truck', 'fas fa-bus', 'fas fa-shuttle-van', 'fas fa-motorcycle',
            'fas fa-shopping-cart', 'fas fa-shopping-bag', 'fas fa-credit-card', 'fas fa-wallet', 'fas fa-receipt',
            'fas fa-lock', 'fas fa-key', 'fas fa-unlock', 'fas fa-shield-alt', 'fas fa-user-lock',
            'fas fa-phone', 'fas fa-mobile-alt', 'fas fa-fax', 'fas fa-headset', 'fas fa-comments',
            'fas fa-globe', 'fas fa-map-marker-alt', 'fas fa-map', 'fas fa-compass', 'fas fa-location-arrow',
            'fas fa-star', 'fas fa-star-half-alt', 'fas fa-heart', 'fas fa-heart-broken', 'fas fa-gift',
            'fas fa-trophy', 'fas fa-medal', 'fas fa-award', 'fas fa-certificate', 'fas fa-flag',
            'fas fa-lightbulb', 'fas fa-bolt', 'fas fa-fire', 'fas fa-water', 'fas fa-leaf',
            'fas fa-tree', 'fas fa-paw', 'fas fa-fish', 'fas fa-dog', 'fas fa-cat',
            'fas fa-bug', 'fas fa-spider', 'fas fa-robot', 'fas fa-rocket', 'fas fa-plane',
            'fas fa-helicopter', 'fas fa-fighter-jet', 'fas fa-ship', 'fas fa-subway', 'fas fa-train',
            'fas fa-bicycle', 'fas fa-bus-alt', 'fas fa-suitcase', 'fas fa-briefcase', 'fas fa-archive',
            'fas fa-box', 'fas fa-box-open', 'fas fa-boxes', 'fas fa-cube', 'fas fa-cubes',
            'fas fa-magic', 'fas fa-magnet', 'fas fa-compass', 'fas fa-map-signs', 'fas fa-map-pin',
            'fas fa-map-marked', 'fas fa-map-marked-alt', 'fas fa-map-marker', 'fas fa-map-marker-alt', 'fas fa-street-view',
            'fas fa-search', 'fas fa-search-plus', 'fas fa-search-minus', 'fas fa-filter', 'fas fa-sort',
            'fas fa-sort-up', 'fas fa-sort-down', 'fas fa-exchange-alt', 'fas fa-random', 'fas fa-sync',
            'fas fa-sync-alt', 'fas fa-redo', 'fas fa-undo', 'fas fa-redo-alt', 'fas fa-undo-alt',
            'fas fa-plus', 'fas fa-minus', 'fas fa-times', 'fas fa-check', 'fas fa-check-circle',
            'fas fa-check-square', 'fas fa-minus-circle', 'fas fa-plus-circle', 'fas fa-question', 'fas fa-question-circle',
            'fas fa-info', 'fas fa-info-circle', 'fas fa-exclamation', 'fas fa-exclamation-circle', 'fas fa-exclamation-triangle',
            'fas fa-bug', 'fas fa-skull', 'fas fa-skull-crossbones', 'fas fa-fire-alt', 'fas fa-water',
            'fas fa-leaf', 'fas fa-seedling', 'fas fa-tree', 'fas fa-paw', 'fas fa-fish',
            'fas fa-dog', 'fas fa-cat', 'fas fa-hippo', 'fas fa-dragon', 'fas fa-feather',
            'fas fa-crown', 'fas fa-gem', 'fas fa-ring', 'fas fa-diamond', 'fas fa-sun',
            'fas fa-moon', 'fas fa-star', 'fas fa-cloud', 'fas fa-cloud-rain', 'fas fa-cloud-showers-heavy',
            'fas fa-snowflake', 'fas fa-wind', 'fas fa-smog', 'fas fa-tint', 'fas fa-water',
            'fas fa-fire', 'fas fa-lightbulb', 'fas fa-bolt', 'fas fa-magnet', 'fas fa-wrench',
            'fas fa-tools', 'fas fa-hammer', 'fas fa-screwdriver', 'fas fa-gavel', 'fas fa-key',
            'fas fa-lock', 'fas fa-unlock', 'fas fa-shield-alt', 'fas fa-bell', 'fas fa-bell-slash',
            'fas fa-broadcast-tower', 'fas fa-broadcast', 'fas fa-volume-up', 'fas fa-volume-down', 'fas fa-volume-off',
            'fas fa-volume-mute', 'fas fa-headphones', 'fas fa-microphone', 'fas fa-microphone-slash', 'fas fa-phone',
            'fas fa-phone-alt', 'fas fa-mobile-alt', 'fas fa-fax', 'fas fa-envelope', 'fas fa-paper-plane',
            'fas fa-inbox', 'fas fa-envelope-open', 'fas fa-comment', 'fas fa-comment-alt', 'fas fa-comments',
            'fas fa-comment-dots', 'fas fa-share', 'fas fa-share-square', 'fas fa-share-alt', 'fas fa-share-alt-square',
            'fas fa-flag', 'fas fa-flag-checkered', 'fas fa-flag-usa', 'fas fa-flag-swallowtail', 'fas fa-flag-pennant',
            'fas fa-trophy', 'fas fa-medal', 'fas fa-award', 'fas fa-certificate', 'fas fa-gift',
            'fas fa-box', 'fas fa-box-open', 'fas fa-boxes', 'fas fa-cubes', 'fas fa-cube',
            'fas fa-briefcase', 'fas fa-archive', 'fas fa-suitcase', 'fas fa-suitcase-rolling'
        ];

        // Populate modal
        const container = $('#iconList');
        icons.forEach(icon => {
            container.append(`
            <div class="col-2 text-center mb-3">
                <i class="${icon} fa-2x icon-select" style="cursor:pointer;"></i>
                <div style="font-size:10px;">${icon}</div>
            </div>
        `);
        });

        // Open modal on button click
        $('#openIconModal').click(function() {
            $('#iconModal').modal('show');
        });

        // Icon click
        $(document).on('click', '.icon-select', function() {
            const selected = $(this).attr('class').replace('fa-2x icon-select', '').trim();
            $('#icon').val(selected);
            $('#openIconModal i').attr('class', selected);
            $('#iconModal').modal('hide');
        });
    });
</script>

<script>
    $(document).ready(function() {

        $('#icon').change(function(e) {

            let reader = new FileReader();

            reader.onload = function(e) {
                $('#iconPreview').attr('src', e.target.result);
            }

            reader.readAsDataURL(e.target.files[0]);

        });

    });
</script>

@endsection