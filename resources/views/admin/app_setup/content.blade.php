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
                    User Balance Content Update
                </span>
            </h1>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">

            <div class="card">
                <div class="card-body">

                    <form action="{{ route('admin.setup.content.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Bkash Rate</label>
                            <input type="text"
                                   name="bkash_rate"
                                   value="{{ $appSetup->bkash_rate }}"
                                   class="form-control mb-3"
                                   placeholder="Bkash Rate">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Bank Rate</label>
                            <input type="text"
                                   name="bank_rate"
                                   value="{{ $appSetup->bank_rate }}"
                                   class="form-control mb-3"
                                   placeholder="Bank Rate">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">India Rate</label>
                            <input type="text"
                                   name="india_rate"
                                   value="{{ $appSetup->india_rate }}"
                                   class="form-control mb-3"
                                   placeholder="India Rate">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Nepal Rate</label>
                            <input type="text"
                                   name="nepal_rate"
                                   value="{{ $appSetup->nepal_rate }}"
                                   class="form-control mb-3"
                                   placeholder="Nepal Rate">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">MVR to BDT Conversion Rate 1MVR = BDT?</label>
                            <input type="number" name="mvr_to_bdt" value="{{ old('mvr_to_bdt', $appSetup->mvr_to_bdt) }}" class="form-control mb-3" placeholder="MVR to BDT Conversion Rate" min="0.000001" step="0.000001" required>
                            @error('mvr_to_bdt') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <h4 class="mb-3">Maldives Islamic Bank</h4>
                            <label class="form-label" for="mib_bank_name">Bank Name</label>
                            <input id="mib_bank_name" name="mib_bank_name" type="text" class="form-control mb-3" maxlength="100" required value="{{ old('mib_bank_name', $appSetup->mib_bank_name ?? 'Maldives Islamic Bank') }}">
                            <label class="form-label" for="mib_bank_logo">Bank Logo</label>
                            @if (!empty($appSetup->mib_bank_logo ?? null))
                                <img src="{{ asset('storage/'.$appSetup->mib_bank_logo) }}" alt="MIB logo" class="d-block mb-2 rounded border" style="width:72px;height:72px;object-fit:contain">
                            @endif
                            <input id="mib_bank_logo" name="mib_bank_logo" type="file" accept="image/jpeg,image/png,image/webp" class="form-control mb-3">
                            <small class="form-text text-muted">JPG, PNG or WebP · Maximum 2 MB. Leave empty to keep the current logo.</small>
                            @error('mib_bank_name') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                            @error('mib_bank_logo') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Maldives Islamic Bank Payment Instructions</label>
                            <textarea name="mib_balance_content"
                                      id="mib_balance_content"
                                      class="form-control">
                                {{ old('mib_balance_content', $appSetup->mib_balance_content ?? $appSetup->add_balance_content ?? '') }}
                            </textarea>
                            @error('mib_balance_content') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <hr>
                            <h4 class="mb-3">Bank of Maldives</h4>
                            <label class="form-label" for="bml_bank_name">Bank Name</label>
                            <input id="bml_bank_name" name="bml_bank_name" type="text" class="form-control mb-3" maxlength="100" required value="{{ old('bml_bank_name', $appSetup->bml_bank_name ?? 'Bank of Maldives') }}">
                            <label class="form-label" for="bml_bank_logo">Bank Logo</label>
                            @if (!empty($appSetup->bml_bank_logo ?? null))
                                <img src="{{ asset('storage/'.$appSetup->bml_bank_logo) }}" alt="BML logo" class="d-block mb-2 rounded border" style="width:72px;height:72px;object-fit:contain">
                            @endif
                            <input id="bml_bank_logo" name="bml_bank_logo" type="file" accept="image/jpeg,image/png,image/webp" class="form-control mb-3">
                            <small class="form-text text-muted">JPG, PNG or WebP · Maximum 2 MB. Leave empty to keep the current logo.</small>
                            @error('bml_bank_name') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                            @error('bml_bank_logo') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Bank of Maldives Payment Instructions</label>
                            <textarea name="bml_balance_content"
                                      id="bml_balance_content"
                                      class="form-control">
                                {{ old('bml_balance_content', $appSetup->bml_balance_content ?? '') }}
                            </textarea>
                            @error('bml_balance_content') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">
                            Update User Content
                        </button>
                    </form>

                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Dashboard Licence Information</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.setup.license.update') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="license_number">Licence Number</label>
                            <input
                                type="text"
                                name="license_number"
                                id="license_number"
                                value="{{ old('license_number', $appSetup->license_number ?? '') }}"
                                class="form-control @error('license_number') is-invalid @enderror"
                                maxlength="100"
                                placeholder="e.g. LIC-2026-001234"
                            >
                            <small class="form-text text-muted">This number will be displayed in the licence information panel on the user dashboard.</small>
                            @error('license_number')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-certificate mr-1"></i> Save Licence Number
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
        $('#mib_balance_content, #bml_balance_content').summernote({
            height: 250,
            placeholder: 'Write this bank payment instructions here...',
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
