<div class="mb-3">
    <label class="form-label">Pharmacy Name</label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $pharmacy->name ?? '') }}" required>
    @error('name') <div class="text-danger">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label">License Number</label>
    <input type="text" name="license_number" class="form-control" value="{{ old('license_number', $pharmacy->license_number ?? '') }}" required>
    @error('license_number') <div class="text-danger">{{ $message }}</div> @enderror
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Country</label>
        <input type="text" name="country" class="form-control" value="{{ old('country', $pharmacy->country ?? '') }}" required>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Region</label>
        <input type="text" name="region" class="form-control" value="{{ old('region', $pharmacy->region ?? '') }}" required>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">District</label>
        <input type="text" name="district" class="form-control" value="{{ old('district', $pharmacy->district ?? '') }}">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Location</label>
        <input type="text" name="location" class="form-control" value="{{ old('location', $pharmacy->location ?? '') }}">
    </div>
</div>

<div class="mb-3">
    <label class="form-label">Working Hours</label>
    <input type="text" name="working_hours" class="form-control" value="{{ old('working_hours', $pharmacy->working_hours ?? '') }}">
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Contact Phone</label>
        <input type="text" name="contact_phone" class="form-control" value="{{ old('contact_phone', $pharmacy->contact_phone ?? '') }}">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Contact Email</label>
        <input type="email" name="contact_email" class="form-control" value="{{ old('contact_email', $pharmacy->contact_email ?? '') }}">
    </div>
</div>

<div class="mb-3">
    <label class="form-label">License Expiry</label>
    <input type="date" name="license_expiry" class="form-control" value="{{ old('license_expiry', optional($pharmacy->license_expiry ?? null)->format('Y-m-d')) }}">
</div>

<div class="mb-3">
    <label class="form-label">Pharmacy Logo</label>
    <input type="file" name="pharmacy_logo" class="form-control">
    @if(!empty($pharmacy->pharmacy_logo))
        <img src="{{ asset('storage/'.$pharmacy->pharmacy_logo) }}" width="60" class="mt-2 rounded">
    @endif
</div>

<div class="mb-3">
    <label class="form-label">Status</label>
    <select name="is_active" class="form-control">
        <option value="1" {{ old('is_active', $pharmacy->is_active ?? 1) == 1 ? 'selected' : '' }}>Active</option>
        <option value="0" {{ old('is_active', $pharmacy->is_active ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
    </select>
</div>
