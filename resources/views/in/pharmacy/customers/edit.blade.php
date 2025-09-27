@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Edit Customer</h2>
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Whoops!</strong> There were some problems with your input.
                <ul>
                    @foreach ($errors->all() as $error)
                       <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    <form action="{{ route('customers.update', $customer) }}" method="POST">
        @csrf @method('PUT')

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
            <input type="text" name="name" class="form-control" required value="{{ old('name', $customer->name) }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control" value="{{ old('phone', $customer->phone) }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $customer->email) }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Customer Type</label>
            <select name="customer_type" class="form-select" required>
                <option value="individual" {{ $customer->customer_type=='individual'?'selected':'' }}>Individual</option>
                <option value="hospital" {{ $customer->customer_type=='hospital'?'selected':'' }}>Hospital</option>
                <option value="clinic" {{ $customer->customer_type=='clinic'?'selected':'' }}>Clinic</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Insurance Provider</label>
            <input type="text" name="insurance_provider" class="form-control" value="{{ old('insurance_provider', $customer->insurance_provider) }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Insurance Number</label>
            <input type="text" name="insurance_number" class="form-control" value="{{ old('insurance_number', $customer->insurance_number) }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Address (JSON)</label>
            <textarea name="address" class="form-control">{{ old('address', $customer->address) }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Demographics (JSON)</label>
            <textarea name="demographics" class="form-control">{{ old('demographics', $customer->demographics) }}</textarea>
        </div>

        <button class="btn btn-success">Update</button>
        <a href="{{ route('customers.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
