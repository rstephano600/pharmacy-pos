@extends('layouts.app')

@section('content')
<div class="container">
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
    <h2>Edit Purchase Order #{{ $purchaseOrder->po_number }}</h2>
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
    <form action="{{ route('purchase_orders.update', $purchaseOrder) }}" method="POST">
        @csrf
        @method('PUT')


        {{-- PHARMACY --}}
        @if(auth()->user()->hasRole('super_admin'))
        <div class="mb-3">
            <label class="form-label">Pharmacy</label>
            <select name="pharmacy_id" class="form-select" required>
                @foreach($pharmacies as $pharmacy)
                    <option value="{{ $pharmacy->id }}"
                         {{ old('pharmacy_id') == $pharmacy->id ? 'selected' : '' }}>
                        {{ $pharmacy->name }}
                    </option>
                @endforeach
            </select>
        </div>
        @else
            {{-- Normal users: use active pharmacy --}}
            <input type="hidden" name="pharmacy_id" value="{{ session('active_pharmacy_id') }}">
        @endif

        <div class="mb-3">
            <label class="form-label">Supplier</label>
            <select name="supplier_id" class="form-select">
                <option value="">-- Select Supplier --</option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}" {{ old('supplier_id', $purchaseOrder->supplier_id) == $supplier->id ? 'selected':'' }}>
                        {{ $supplier->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Order Date</label>
            <input type="date" name="order_date" class="form-control" value="{{ old('order_date', $purchaseOrder->order_date) }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Expected Delivery</label>
            <input type="date" name="expected_delivery_date" class="form-control" value="{{ old('expected_delivery_date', $purchaseOrder->expected_delivery_date) }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Payment Terms</label>
            <input type="text" name="payment_terms" class="form-control" value="{{ old('payment_terms', $purchaseOrder->payment_terms) }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-control">{{ old('notes', $purchaseOrder->notes) }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Delivery Address (JSON)</label>
            <textarea name="delivery_address" class="form-control">{{ old('delivery_address', $purchaseOrder->delivery_address) }}</textarea>
        </div>

        <hr>
        <h4>Purchase Order Items</h4>

        <table class="table table-bordered" id="items-table">
            <thead>
                <tr>
                    <th>Medicine</th>
                    <th>Quantity Ordered</th>
                    <th>Unit Cost</th>
                    <th>Total Cost</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach(old('items', $purchaseOrder->items) as $i => $item)
                <tr>
                    <td>
                        <select name="items[{{ $i }}][medicine_id]" class="form-select" required>
                            <option value="">-- Select Medicine --</option>
                            @foreach($medicines as $medicine)
                                <option value="{{ $medicine->id }}" {{ $item['medicine_id'] ?? $item->medicine_id == $medicine->id ? 'selected' : '' }}>
                                    {{ $medicine->name }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="number" name="items[{{ $i }}][quantity_ordered]" class="form-control quantity" value="{{ $item['quantity_ordered'] ?? $item->quantity_ordered }}" required></td>
                    <td><input type="number" name="items[{{ $i }}][unit_cost]" step="0.01" class="form-control unit_cost" value="{{ $item['unit_cost'] ?? $item->unit_cost }}" required></td>
                    <td><input type="number" class="form-control total_cost" value="{{ ($item['quantity_ordered'] ?? $item->quantity_ordered) * ($item['unit_cost'] ?? $item->unit_cost) }}" readonly></td>
                    <td><button type="button" class="btn btn-danger btn-sm remove-item">Remove</button></td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <button type="button" class="btn btn-secondary mb-3" id="add-item">Add Item</button>

        <br>
        <button class="btn btn-success">Update Order</button>
        <a href="{{ route('purchase_orders.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let index = {{ count(old('items', $purchaseOrder->items)) }};
    
    document.getElementById('add-item').addEventListener('click', function() {
        let row = `<tr>
            <td>
                <select name="items[${index}][medicine_id]" class="form-select" required>
                    <option value="">-- Select Medicine --</option>
                    @foreach($medicines as $medicine)
                        <option value="{{ $medicine->id }}">{{ $medicine->name }}</option>
                    @endforeach
                </select>
            </td>
            <td><input type="number" name="items[${index}][quantity_ordered]" class="form-control quantity" value="1" required></td>
            <td><input type="number" name="items[${index}][unit_cost]" step="0.01" class="form-control unit_cost" value="0.00" required></td>
            <td><input type="number" class="form-control total_cost" value="0.00" readonly></td>
            <td><button type="button" class="btn btn-danger btn-sm remove-item">Remove</button></td>
        </tr>`;
        document.querySelector('#items-table tbody').insertAdjacentHTML('beforeend', row);
        index++;
        calculateTotals();
    });

    document.querySelector('#items-table').addEventListener('input', function(e) {
        if (e.target.classList.contains('quantity') || e.target.classList.contains('unit_cost')) {
            let row = e.target.closest('tr');
            let quantity = parseFloat(row.querySelector('.quantity').value) || 0;
            let cost = parseFloat(row.querySelector('.unit_cost').value) || 0;
            row.querySelector('.total_cost').value = (quantity * cost).toFixed(2);
        }
    });

    document.querySelector('#items-table').addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-item')) {
            e.target.closest('tr').remove();
        }
    });
});
</script>
@endpush

@endsection
