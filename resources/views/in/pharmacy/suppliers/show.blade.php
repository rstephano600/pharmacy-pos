@extends('layouts.app')
@section('content')
<div class="container">
    <h1>Supplier Details</h1>
    <div class="card">
        <div class="card-body">
            <p><strong>Name:</strong> {{ $supplier->name }}</p>
            <p><strong>Pharmacy:</strong> {{ $supplier->pharmacy->name }}</p>
            <p><strong>Contact Person:</strong> {{ $supplier->contact_person }}</p>
            <p><strong>Phone:</strong> {{ $supplier->phone }}</p>
            <p><strong>Email:</strong> {{ $supplier->email }}</p>
            <p><strong>Address:</strong> {{ $supplier->address }}</p>
            <p><strong>Credit Days:</strong> {{ $supplier->credit_days }}</p>
            <p><strong>Credit Limit:</strong> {{ number_format($supplier->credit_limit,2) }}</p>
            <p><strong>Payment Terms:</strong> {{ $supplier->payment_terms_label }}</p>
            <p><strong>Status:</strong> {{ $supplier->status_label }}</p>
        </div>
    </div>
    <a href="{{ route('suppliers.index') }}" class="btn btn-secondary mt-3">Back</a>
</div>
@endsection
