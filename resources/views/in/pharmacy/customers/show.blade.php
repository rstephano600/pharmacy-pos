@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Customer Details</h2>

    <div class="card">
        <div class="card-body">
            <p><strong>Name:</strong> {{ $customer->name }}</p>
            <p><strong>Phone:</strong> {{ $customer->phone ?? '—' }}</p>
            <p><strong>Email:</strong> {{ $customer->email ?? '—' }}</p>
            <p><strong>Type:</strong> {{ ucfirst($customer->customer_type) }}</p>
            <p><strong>Insurance:</strong> {{ $customer->insurance_provider ?? '—' }}</p>
            <p><strong>Insurance No:</strong> {{ $customer->insurance_number ?? '—' }}</p>
            <p><strong>Address:</strong> {{ $customer->address ?? '—' }}</p>
            <p><strong>Demographics:</strong> {{ $customer->demographics ?? '—' }}</p>
            @role('super_admin')
                <p><strong>Pharmacy:</strong> {{ $customer->pharmacy->name ?? 'N/A' }}</p>
            @endrole
        </div>
    </div>

    <a href="{{ route('customers.index') }}" class="btn btn-secondary mt-3">Back</a>
    <a href="{{ route('customers.edit', $customer) }}" class="btn btn-warning mt-3">Edit</a>
</div>
@endsection
