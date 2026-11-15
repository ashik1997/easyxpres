@extends('master.backend')

@section('content')

<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><span class="text-danger" style="border-bottom: 1px dotted red;">Packages Edit</span></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <a href="{{ route('admin.subpackages.list') }}" class="btn btn-primary"> <i
                                class="fas fa-list"></i> Sub Packages List</a>
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
                    <div class="card">
                        <div class="card-body">
                            <form method="POST"
                                action="{{ route('admin.subpackages.update', $subpackage->id) }}">
                                @csrf
                                @method('PUT')

                                <div class="form-group">
                                    <label for="package_id">Select Package</label>
                                    <select class="form-control" id="package_id" name="package_id" required>
                                        <option value="" disabled>Select a package</option>
                                        @foreach ($packages as $package)
                                        <option value="{{ $package->id }}"
                                            {{ $subpackage->package_id == $package->id ? 'selected' : '' }}>
                                            {{ $package->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="title">Sub Package Title</label>
                                    <input type="text" class="form-control" id="title" name="title"
                                        value="{{ $subpackage->title }}" placeholder="Enter Sub Package Title" required>
                                </div>
                                <div class="form-group">
                                    <label>Amount</label>
                                    <input type="number"
                                        class="form-control"
                                        id="amount"
                                        name="amount"
                                        value="{{ $subpackage->amount }}"
                                        placeholder="Enter Amount"
                                        min="0"
                                        step="any"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label>Commission</label>
                                    <input type="number"
                                        class="form-control"
                                        id="commission"
                                        name="commission"
                                        value="{{ $subpackage->commission }}"
                                        placeholder="Enter Commission"
                                        min="0"
                                        step="any"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label>Offer Price</label>
                                    <input type="number"
                                        class="form-control"
                                        id="offer_price"
                                        name="offer_price"
                                        value="{{ $subpackage->offer_price }}"
                                        placeholder="Enter Offer Price"
                                        min="0"
                                        step="any"
                                        required readonly>
                                </div>
                                <button type="submit" class="btn btn-success">Update Sub Package</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    // Auto-calculate offer price
    document.getElementById('amount').addEventListener('input', calculateOfferPrice);
    document.getElementById('commission').addEventListener('input', calculateOfferPrice);
    function calculateOfferPrice() {
        let amount = parseFloat(document.getElementById('amount').value) || 0;
        let commission = parseFloat(document.getElementById('commission').value) || 0;

        let offerPrice = amount - commission;
        document.getElementById('offer_price').value = offerPrice >= 0 ? offerPrice.toFixed(2) : 0;
    }

</script>
@endsection