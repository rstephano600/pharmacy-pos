@extends('layouts.app')

@section('title', isset($medicine) ? 'Edit Medicine' : 'Add New Medicine')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        {{ isset($medicine) ? 'Edit Medicine' : 'Add New Medicine' }}
                    </h3>
                    <a href="{{ route('medicines.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
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
                <div class="card-body">
                    <form action="{{ isset($medicine) ? route('medicines.update', $medicine) : route('medicines.store') }}" 
                          method="POST" 
                          class="row g-3">
                        @csrf
                        @if(isset($medicine))
                            @method('PUT')
                        @endif

                        <!-- Pharmacy Selection -->
                        <div class="col-md-6">
                            <label for="pharmacy_id" class="form-label">Pharmacy <span class="text-danger">*</span></label>
                            <select name="pharmacy_id" 
                                    id="pharmacy_id" 
                                    class="form-select @error('pharmacy_id') is-invalid @enderror" 
                                    required>
                                <option value="">Select Pharmacy</option>
                                @foreach($pharmacies as $pharmacy)
                                    <option value="{{ $pharmacy->id }}" 
                                            {{ (old('pharmacy_id', $medicine->pharmacy_id ?? '') == $pharmacy->id) ? 'selected' : '' }}>
                                        {{ $pharmacy->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('pharmacy_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Category Selection -->
                        <div class="col-md-6">
                            <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                            <select name="category_id" 
                                    id="category_id" 
                                    class="form-select @error('category_id') is-invalid @enderror" 
                                    required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" 
                                            {{ (old('category_id', $medicine->category_id ?? '') == $category->id) ? 'selected' : '' }}>
                                        {{ $category->category_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Medicine Name -->
                        <div class="col-md-12">
                            <label for="name" class="form-label">Medicine Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $medicine->name ?? '') }}" 
                                   placeholder="Enter medicine name"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Generic Name -->
                        <div class="col-md-6">
                            <label for="generic_name" class="form-label">Generic Name</label>
                            <input type="text" 
                                   name="generic_name" 
                                   id="generic_name" 
                                   class="form-control @error('generic_name') is-invalid @enderror" 
                                   value="{{ old('generic_name', $medicine->generic_name ?? '') }}" 
                                   placeholder="Enter generic name">
                            @error('generic_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Brand Name -->
                        <div class="col-md-6">
                            <label for="brand_name" class="form-label">Brand Name</label>
                            <input type="text" 
                                   name="brand_name" 
                                   id="brand_name" 
                                   class="form-control @error('brand_name') is-invalid @enderror" 
                                   value="{{ old('brand_name', $medicine->brand_name ?? '') }}" 
                                   placeholder="Enter brand name">
                            @error('brand_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Strength -->
                        <div class="col-md-4">
                            <label for="strength" class="form-label">Strength</label>
                            <input type="text" 
                                   name="strength" 
                                   id="strength" 
                                   class="form-control @error('strength') is-invalid @enderror" 
                                   value="{{ old('strength', $medicine->strength ?? '') }}" 
                                   placeholder="e.g., 500mg, 10ml">
                            @error('strength')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Form -->
                        <div class="col-md-4">
                            <label for="form" class="form-label">Form <span class="text-danger">*</span></label>
                            <select name="form" 
                                    id="form" 
                                    class="form-select @error('form') is-invalid @enderror" 
                                    required>
                                <option value="">Select Form</option>
                                @foreach($forms as $key => $label)
                                    <option value="{{ $key }}" 
                                            {{ (old('form', $medicine->form ?? '') == $key) ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('form')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Prescription Type -->
                        <div class="col-md-4">
                            <label for="prescription_type" class="form-label">Prescription Type <span class="text-danger">*</span></label>
                            <select name="prescription_type" 
                                    id="prescription_type" 
                                    class="form-select @error('prescription_type') is-invalid @enderror" 
                                    required>
                                <option value="">Select Type</option>
                                @foreach($prescriptionTypes as $key => $label)
                                    <option value="{{ $key }}" 
                                            {{ (old('prescription_type', $medicine->prescription_type ?? '') == $key) ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('prescription_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Storage Type -->
                        <div class="col-md-6">
                            <label for="storage_type" class="form-label">Storage Type <span class="text-danger">*</span></label>
                            <select name="storage_type" 
                                    id="storage_type" 
                                    class="form-select @error('storage_type') is-invalid @enderror" 
                                    required>
                                <option value="">Select Storage Type</option>
                                @foreach($storageTypes as $key => $label)
                                    <option value="{{ $key }}" 
                                            {{ (old('storage_type', $medicine->storage_type ?? '') == $key) ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('storage_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Barcode -->
                        <div class="col-md-6">
                            <label for="barcode" class="form-label">Barcode</label>
                            <div class="input-group">
                                <input type="text" 
                                       name="barcode" 
                                       id="barcode" 
                                       class="form-control @error('barcode') is-invalid @enderror" 
                                       value="{{ old('barcode', $medicine->barcode ?? '') }}" 
                                       placeholder="Enter or scan barcode">
                                <button type="button" class="btn btn-outline-secondary" id="generate-barcode">
                                    <i class="fas fa-barcode"></i> Generate
                                </button>
                            </div>
                            @error('barcode')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Unit Price -->
                        <div class="col-md-4">
                            <label for="unit_price" class="form-label">Unit Price <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" 
                                       name="unit_price" 
                                       id="unit_price" 
                                       class="form-control @error('unit_price') is-invalid @enderror" 
                                       value="{{ old('unit_price', $medicine->unit_price ?? '') }}" 
                                       step="0.01" 
                                       min="0" 
                                       placeholder="0.00"
                                       required>
                            </div>
                            @error('unit_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Reorder Level -->
                        <div class="col-md-4">
                            <label for="reorder_level" class="form-label">Reorder Level <span class="text-danger">*</span></label>
                            <input type="number" 
                                   name="reorder_level" 
                                   id="reorder_level" 
                                   class="form-control @error('reorder_level') is-invalid @enderror" 
                                   value="{{ old('reorder_level', $medicine->reorder_level ?? 0) }}" 
                                   min="0" 
                                   placeholder="Minimum stock level"
                                   required>
                            @error('reorder_level')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Active Status -->
                        <div class="col-md-4">
                            <label for="is_active" class="form-label">Status</label>
                            <div class="form-check form-switch mt-2">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" 
                                       name="is_active" 
                                       id="is_active" 
                                       class="form-check-input @error('is_active') is-invalid @enderror" 
                                       value="1" 
                                       {{ old('is_active', $medicine->is_active ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active Medicine
                                </label>
                            </div>
                            @error('is_active')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Buttons -->
                        <div class="col-12">
                            <hr>
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('medicines.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> 
                                    {{ isset($medicine) ? 'Update Medicine' : 'Create Medicine' }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Generate random barcode
    document.getElementById('generate-barcode').addEventListener('click', function() {
        const timestamp = Date.now();
        const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
        const barcode = timestamp.toString() + random;
        document.getElementById('barcode').value = barcode;
    });

    // Form validation feedback
    const form = document.querySelector('form');
    const inputs = form.querySelectorAll('input[required], select[required]');
    
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value.trim() === '') {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            }
        });
    });

    // Price formatting
    const priceInput = document.getElementById('unit_price');
    priceInput.addEventListener('blur', function() {
        if (this.value) {
            this.value = parseFloat(this.value).toFixed(2);
        }
    });

    // Search enhancement for select boxes
    const selectElements = document.querySelectorAll('select');
    selectElements.forEach(select => {
        // Add search functionality if you're using a library like Select2
        // $(select).select2({ theme: 'bootstrap-5' });
    });
});
</script>
@endpush

@push('styles')
<style>
.form-label {
    font-weight: 600;
    color: #495057;
}

.text-danger {
    color: #dc3545 !important;
}

.input-group-text {
    background-color: #f8f9fa;
    border-color: #dee2e6;
}

.form-check-input:checked {
    background-color: #198754;
    border-color: #198754;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
}

.btn {
    border-radius: 0.375rem;
    font-weight: 500;
}

.btn-primary {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.btn-secondary {
    background-color: #6c757d;
    border-color: #6c757d;
}

.is-invalid {
    border-color: #dc3545;
}

.is-valid {
    border-color: #198754;
}

.invalid-feedback {
    display: block;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.875em;
    color: #dc3545;
}
</style>
@endpush
@endsection