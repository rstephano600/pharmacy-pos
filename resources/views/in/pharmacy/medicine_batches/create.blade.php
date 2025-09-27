@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Add Medicine Batch</h2>
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
    <form action="{{ route('medicine_batches.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label">Medicine</label>
            <select name="medicine_id" class="form-select" required>
                <option value="">-- Select Medicine --</option>
                @foreach($medicines as $medicine)
                    <option value="{{ $medicine->id }}" {{ old('medicine_id') == $medicine->id ? 'selected' : '' }}>
                        {{ $medicine->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Purchase Order Item (optional)</label>
            <select name="purchase_order_item_id" class="form-select">
                <option value="">-- Select PO Item --</option>
                @foreach($purchaseOrderItems as $item)
                    <option value="{{ $item->id }}" {{ old('purchase_order_item_id') == $item->id ? 'selected':'' }}>
                        PO #{{ $item->purchaseOrder->po_number }} — {{ $item->medicine->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Batch Number</label>
            <input type="text" name="batch_number" class="form-control" value="{{ old('batch_number') }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Manufacture Date</label>
            <input type="date" name="manufacture_date" class="form-control" value="{{ old('manufacture_date') }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Expiry Date</label>
            <input type="date" name="expiry_date" class="form-control" value="{{ old('expiry_date') }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Quantity Received</label>
            <input type="number" name="quantity_received" class="form-control" value="{{ old('quantity_received') }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Quantity Available</label>
            <input type="number" name="quantity_available" class="form-control" value="{{ old('quantity_available') }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Unit Cost</label>
            <input type="number" step="0.01" name="unit_cost" class="form-control" value="{{ old('unit_cost') }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Selling Price</label>
            <input type="number" step="0.01" name="selling_price" class="form-control" value="{{ old('selling_price') }}">
        </div>

        <div class="form-check mb-3">
            <input type="checkbox" name="is_expired" class="form-check-input" value="1" {{ old('is_expired') ? 'checked' : '' }}>
            <label class="form-check-label">Expired</label>
        </div>

        <button class="btn btn-success">Save Batch</button>
        <a href="{{ route('medicine_batches.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
