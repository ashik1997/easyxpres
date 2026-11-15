@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row">
    <div class="form-group col-md-6">
        <label for="vpn_name_id">VPN Name</label>
        <select name="vpn_name_id" id="vpn_name_id" class="form-control" required>
            <option value="">Select VPN</option>
            @foreach ($vpnNames as $vpnName)
                <option value="{{ $vpnName->id }}" @selected((string) old('vpn_name_id', $vpn->vpn_name_id ?? '') === (string) $vpnName->id)>
                    {{ $vpnName->name }}{{ $vpnName->status === 'inactive' ? ' (Inactive)' : '' }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="form-group col-md-6">
        <label for="expire_date">Expiry Date</label>
        <input type="date" name="expire_date" id="expire_date" class="form-control" min="{{ today()->format('Y-m-d') }}"
            value="{{ old('expire_date', isset($vpn) ? $vpn->expire_date->format('Y-m-d') : '') }}" required>
    </div>
    <div class="form-group col-md-6">
        <label for="user_name">Username</label>
        <input type="text" name="user_name" id="user_name" class="form-control" value="{{ old('user_name', $vpn->user_name ?? '') }}" required>
    </div>
    <div class="form-group col-md-6">
        <label for="password">Password</label>
        <input type="text" name="password" id="password" class="form-control" value="{{ old('password', $vpn->password ?? '') }}" required>
    </div>
    <div class="form-group col-md-12">
        <label for="link">Access Link</label>
        <input type="url" name="link" id="link" class="form-control" placeholder="https://example.com/login"
            value="{{ old('link', $vpn->link ?? '') }}" required>
    </div>
    <div class="form-group col-md-6">
        <label for="payment">Payment (MVR)</label>
        <input type="number" name="payment" id="payment" class="form-control" min="0" step="0.01"
            value="{{ old('payment', $vpn->payment ?? '') }}" required>
    </div>
    <div class="form-group col-md-6">
        <label for="status">Status</label>
        <select name="status" id="status" class="form-control" required>
            @foreach (['active' => 'Active', 'inactive' => 'Inactive'] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $vpn->status ?? 'active') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
</div>

<button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
<a href="{{ route('admin.vpns.index') }}" class="btn btn-secondary">Cancel</a>
