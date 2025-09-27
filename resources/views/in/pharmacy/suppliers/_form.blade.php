@csrf

@if(auth()->user()->hasRole('super_admin'))
<div class="mb-3">
    <label class="form-label">Pharmacy</label>
    <select name="pharmacy_id" class="form-select" required>
        @foreach($pharmacies as $pharmacy)
            <option value="{{ $pharmacy->id }}"
                 {{ old('pharmacy_id', $medicine->pharmacy_id ?? '') == $pharmacy->id ? 'selected' : '' }}>
                {{ $pharmacy->name }}
            </option>
        @endforeach
    </select>
</div>
@else
    {{-- For normal users, store active pharmacy automatically --}}
    <input type="hidden" name="pharmacy_id" value="{{ session('active_pharmacy_id') }}">
@endif

<div class="mb-3">
    <label class="form-label">Name</label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $supplier->name ?? '') }}" required>
</div>

<div class="mb-3">
    <label class="form-label">Contact Person</label>
    <input type="text" name="contact_person" class="form-control" value="{{ old('contact_person', $supplier->contact_person ?? '') }}">
</div>

<div class="mb-3">
    <label class="form-label">Phone</label>
    <input type="text" name="phone" class="form-control" value="{{ old('phone', $supplier->phone ?? '') }}">
</div>

<div class="mb-3">
    <label class="form-label">Email</label>
    <input type="email" name="email" class="form-control" value="{{ old('email', $supplier->email ?? '') }}">
</div>

<div class="mb-3">
    <label class="form-label">Address</label>
    <textarea name="address" class="form-control">{{ old('address', $supplier->address ?? '') }}</textarea>
</div>

<div class="mb-3">
    <label class="form-label">Credit Days</label>
    <input type="number" name="credit_days" class="form-control" value="{{ old('credit_days', $supplier->credit_days ?? 0) }}">
</div>

<div class="mb-3">
    <label class="form-label">Credit Limit</label>
    <input type="number" step="0.01" name="credit_limit" class="form-control" value="{{ old('credit_limit', $supplier->credit_limit ?? 0) }}">
</div>

<div class="mb-3">
    <label class="form-label">Payment Terms</label>
    <select name="payment_terms" class="form-select">
        @foreach(['cash','credit','cod','other'] as $term)
            <option value="{{ $term }}" {{ old('payment_terms', $supplier->payment_terms ?? '') == $term ? 'selected':'' }}>
                {{ ucfirst($term) }}
            </option>
        @endforeach
    </select>
</div>

<div class="form-check mb-3">
    <input type="checkbox" name="is_active" class="form-check-input" value="1"
        {{ old('is_active', $supplier->is_active ?? true) ? 'checked':'' }}>
    <label class="form-check-label">Active</label>
</div>
