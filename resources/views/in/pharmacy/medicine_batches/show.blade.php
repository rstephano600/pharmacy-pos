@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Medicine Batch #{{ $medicineBatch->id }}</h2>

    <div class="card mb-3">
        <div class="card-body">
            <p><strong>Medicine:</strong> {{ $medicineBatch->medicine->name }}</p>
            <p><strong>Purchase Order Item:</strong> 
                {{ $medicineBatch->purchaseOrderItem?->purchaseOrder?->po_number ?? '—' }}
            </p>
            <p><strong>Batch Number:</strong> {{ $medicineBatch->batch_number ?? '—' }}</p>
            <p><strong>Manufacture Date:</strong> {{ $medicineBatch->manufacture_date ?? '—' }}</p>
            <p><strong>Expiry Date:</strong> {{ $medicineBatch->expiry_date ?? '—' }}</p>
            <p><strong>Quantity Received:</strong> {{ $medicineBatch->quantity_received }}</p>
            <p><strong>Quantity Available:</strong> {{ $medicineBatch->quantity_available }}</p>
            <p><strong>Unit Cost:</strong> {{ number_format($medicineBatch->unit_cost, 2) }}</p>
            <p><strong>Selling Price:</strong> {{ number_format($medicineBatch->selling_price ?? 0, 2) }}</p>
            <p><strong>Expired:</strong> {{ $medicineBatch->is_expired ? 'Yes' : 'No' }}</p>
            <p><strong>Received By:</strong> {{ $medicineBatch->receiver->name }}</p>
            <p><strong>Created At:</strong> {{ $medicineBatch->created_at->format('Y-m-d H:i') }}</p>
            <p><strong>Updated At:</strong> {{ $medicineBatch->updated_at->format('Y-m-d H:i') }}</p>
        </div>
    </div>

    <a href="{{ route('medicine_batches.index') }}" class="btn btn-secondary">Back to List</a>
    <a href="{{ route('medicine_batches.edit', $medicineBatch) }}" class="btn btn-warning">Edit</a>
</div>
@endsection
