@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Create Prescription</h2>
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
    <form action="{{ route('prescriptions.store') }}" method="POST">
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
            <label class="form-label">Customer</label>
            <select name="customer_id" class="form-select" required>
                <option value="">-- Select Customer --</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->phone }})</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Doctor Name</label>
            <input type="text" name="doctor_name" class="form-control" value="{{ old('doctor_name') }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Doctor License</label>
            <input type="text" name="doctor_license" class="form-control" value="{{ old('doctor_license') }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Diagnosis</label>
            <textarea name="diagnosis" class="form-control">{{ old('diagnosis') }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-control">{{ old('notes') }}</textarea>
        </div>

        <h4 class="mt-4">Prescription Items</h4>
        <table class="table table-bordered" id="itemsTable">
            <thead>
                <tr>
                    <th>Medicine</th>
                    <th>Quantity</th>
                    <th>Dosage</th>
                    <th>Frequency</th>
                    <th>Duration (days)</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <select name="items[0][medicine_id]" class="form-select" required>
                            <option value="">-- Select Medicine --</option>
                            @foreach($medicines as $medicine)
                                <option value="{{ $medicine->id }}">{{ $medicine->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="number" name="items[0][quantity]" class="form-control" min="1" required></td>
                    <td><input type="text" name="items[0][dosage]" class="form-control" required></td>
                    <td><input type="text" name="items[0][frequency]" class="form-control" required></td>
                    <td><input type="number" name="items[0][duration_days]" class="form-control" min="1" required></td>
                    <td><button type="button" class="btn btn-danger btn-sm removeRow">Remove</button></td>
                </tr>
            </tbody>
        </table>
        <button type="button" id="addRow" class="btn btn-secondary">+ Add Item</button>

        <div class="mt-4">
            <button class="btn btn-success">Save Prescription</button>
            <a href="{{ route('prescriptions.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let rowCount = 1;

    document.getElementById('addRow').addEventListener('click', function () {
        let table = document.querySelector('#itemsTable tbody');
        let newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td>
                <select name="items[${rowCount}][medicine_id]" class="form-select" required>
                    <option value="">-- Select Medicine --</option>
                    @foreach($medicines as $medicine)
                        <option value="{{ $medicine->id }}">{{ $medicine->name }}</option>
                    @endforeach
                </select>
            </td>
            <td><input type="number" name="items[${rowCount}][quantity]" class="form-control" min="1" required></td>
            <td><input type="text" name="items[${rowCount}][dosage]" class="form-control" required></td>
            <td><input type="text" name="items[${rowCount}][frequency]" class="form-control" required></td>
            <td><input type="number" name="items[${rowCount}][duration_days]" class="form-control" min="1" required></td>
            <td><button type="button" class="btn btn-danger btn-sm removeRow">Remove</button></td>
        `;
        table.appendChild(newRow);
        rowCount++;
    });

    document.querySelector('#itemsTable').addEventListener('click', function (e) {
        if (e.target.classList.contains('removeRow')) {
            e.target.closest('tr').remove();
        }
    });
});
</script>
@endsection
