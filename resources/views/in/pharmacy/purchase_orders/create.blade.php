@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Create Purchase Order</h2>
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
    <form action="{{ route('purchase_orders.store') }}" method="POST">
        @csrf

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

        {{-- SUPPLIER --}}
        <div class="mb-3">
            <label class="form-label">Supplier</label>
            <select name="supplier_id" class="form-select">
                <option value="">-- Select Supplier --</option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected':'' }}>
                        {{ $supplier->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- ORDER DATE --}}
        <div class="mb-3">
            <label class="form-label">Order Date</label>
            <input type="date" name="order_date" class="form-control" value="{{ old('order_date', date('Y-m-d')) }}" required>
        </div>

        {{-- EXPECTED DELIVERY --}}
        <div class="mb-3">
            <label class="form-label">Expected Delivery</label>
            <input type="date" name="expected_delivery_date" class="form-control" value="{{ old('expected_delivery_date') }}">
        </div>

        {{-- PAYMENT TERMS --}}
        <div class="mb-3">
            <label class="form-label">Payment Terms</label>
            <input type="text" name="payment_terms" class="form-control" value="{{ old('payment_terms') }}">
        </div>

        {{-- NOTES --}}
        <div class="mb-3">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-control">{{ old('notes') }}</textarea>
        </div>

        {{-- DELIVERY ADDRESS --}}
        <div class="mb-3">
            <label class="form-label">Delivery Address (JSON)</label>
            <textarea name="delivery_address" class="form-control">{{ old('delivery_address') }}</textarea>
        </div>

        <hr>
        <h4>Purchase Order Items</h4>

        <table class="table table-bordered" id="itemsTable">
            <thead>
                <tr>
                    <th>Medicine</th>
                    <th>Quantity Ordered</th>
                    <th>Unit Cost</th>
                    <th>Total Cost</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                {{-- Initial row --}}
                <tr>
                    <td>
                        <select name="items[0][medicine_id]" class="form-select" required>
                            <option value="">-- Select Medicine --</option>
                            @foreach($medicines as $medicine)
                                <option value="{{ $medicine->id }}">{{ $medicine->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="number" name="items[0][quantity_ordered]" class="form-control qty" min="1" value="1" required>
                    </td>
                    <td>
                        <input type="number" step="0.01" name="items[0][unit_cost]" class="form-control unit" value="0" required>
                    </td>
                    <td>
                        <input type="number" step="0.01" name="items[0][total_cost]" class="form-control total" value="0" readonly>
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-danger remove-row">X</button>
                    </td>
                </tr>
            </tbody>
        </table>

        <button type="button" id="addRow" class="btn btn-sm btn-primary">+ Add Item</button>

        <hr>
        <button class="btn btn-success">Save Order</button>
        <a href="{{ route('purchase_orders.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>

{{-- JS for dynamic rows --}}
<script>
    let rowIdx = 1;

    document.getElementById('addRow').addEventListener('click', function() {
        let tableBody = document.querySelector('#itemsTable tbody');
        let newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td>
                <select name="items[${rowIdx}][medicine_id]" class="form-select" required>
                    <option value="">-- Select Medicine --</option>
                    @foreach($medicines as $medicine)
                        <option value="{{ $medicine->id }}">{{ $medicine->name }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="number" name="items[${rowIdx}][quantity_ordered]" class="form-control qty" min="1" value="1" required>
            </td>
            <td>
                <input type="number" step="0.01" name="items[${rowIdx}][unit_cost]" class="form-control unit" value="0" required>
            </td>
            <td>
                <input type="number" step="0.01" name="items[${rowIdx}][total_cost]" class="form-control total" value="0" readonly>
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-danger remove-row">X</button>
            </td>
        `;
        tableBody.appendChild(newRow);
        rowIdx++;
    });

    // Handle remove row
    document.addEventListener('click', function(e) {
        if(e.target && e.target.classList.contains('remove-row')) {
            e.target.closest('tr').remove();
        }
    });

    // Auto calculate total per row
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('qty') || e.target.classList.contains('unit')) {
            let row = e.target.closest('tr');
            let qty = parseFloat(row.querySelector('.qty').value) || 0;
            let unit = parseFloat(row.querySelector('.unit').value) || 0;
            row.querySelector('.total').value = (qty * unit).toFixed(2);
        }
    });
</script>
@endsection
