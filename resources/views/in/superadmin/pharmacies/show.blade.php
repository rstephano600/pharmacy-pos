@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2 class="mb-3">Pharmacy Details</h2>

    <div class="card p-3">
        <div class="d-flex align-items-center mb-3">
            <img src="{{ $pharmacy->pharmacy_logo ? asset('storage/'.$pharmacy->pharmacy_logo) : asset('default_pharmacy_logo.png') }}"
                 width="80" class="rounded me-3">
            <h4>{{ $pharmacy->name }}</h4>
        </div>

        <ul class="list-group mb-3">
            <li class="list-group-item"><strong>License Number:</strong> {{ $pharmacy->license_number }}</li>
            <li class="list-group-item"><strong>Country:</strong> {{ $pharmacy->country }}</li>
            <li class="list-group-item"><strong>Region:</strong> {{ $pharmacy->region }}</li>
            <li class="list-group-item"><strong>District:</strong> {{ $pharmacy->district }}</li>
            <li class="list-group-item"><strong>Location:</strong> {{ $pharmacy->location }}</li>
            <li class="list-group-item"><strong>Working Hours:</strong> {{ $pharmacy->working_hours }}</li>
            <li class="list-group-item"><strong>Phone:</strong> {{ $pharmacy->contact_phone }}</li>
            <li class="list-group-item"><strong>Email:</strong> {{ $pharmacy->contact_email }}</li>
            <li class="list-group-item"><strong>License Expiry:</strong> {{ $pharmacy->license_expiry?->format('Y-m-d') ?? 'N/A' }}</li>
            <li class="list-group-item"><strong>Status:</strong> 
                <span class="badge {{ $pharmacy->is_active ? 'bg-success' : 'bg-danger' }}">
                    {{ $pharmacy->is_active ? 'Active' : 'Inactive' }}
                </span>
            </li>
        </ul>

        <a href="{{ route('superadmin.pharmacies.index') }}" class="btn btn-secondary">Back</a>
    </div>
</div>
@endsection
