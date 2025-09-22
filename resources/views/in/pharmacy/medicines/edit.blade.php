@extends('layouts.app')

@section('title', 'Edit Medicine - ' . $medicine->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="card-title mb-0">
                            <i class="fas fa-edit"></i> Edit Medicine
                        </h3>
                        <small class="text-muted">Update medicine information</small>
                    </div>
                    <div class="btn-group">
                        <a href="{{ route('medicines.show', $medicine) }}" class="btn btn-info">
                            <i class="fas fa-eye"></i> View Details
                        </a>
                        <a href="{{ route('medicines.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Display current medicine info -->
                    <div class="alert alert-info mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Current Medicine:</strong> {{ $medicine->name }}
                                @if($medicine->strength)
                                    <span class="badge bg-primary ms-1">{{ $medicine->strength }}</span>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <strong>Status:</strong> 
                                <span class="badge bg-{{ $medicine->is_active ? 'success' : 'danger' }}">
                                    {{ $medicine->is_active ? 'Active' : 'Inactive' }}
                                </span>
                                <strong class="ms-3">Price:</strong> ${{ number_format($medicine->unit_price, 2) }}
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('medicines.update', $medicine) }}" 
                          method="POST" 
                          class="row g-3" 
                          id="medicine-form">
                        @csrf
                        @method('PUT')

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
                                            {{ (old('pharmacy_id', $medicine->pharmacy_id) == $pharmacy->id) ? 'selected' : '' }}>
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
                                            {{ (old('category_id', $medicine->category_id) == $category->id) ? 'selected' : '' }}>
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
                                   value="{{ old('name', $medicine->name) }}" 
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
                                   value="{{ old('generic_name', $medicine->generic_name) }}" 
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
                                   value="{{ old('brand_name', $medicine->brand_name) }}" 
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
                                   value="{{ old('strength', $medicine->strength) }}" 
                                   placeholder="e.g., 500mg, 10ml">
                            <small class="form-text text-muted">
                                Current: {{ $medicine->strength ?? 'Not set' }}
                            </small>
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
                                            {{ (old('form', $medicine->form) == $key) ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">
                                Current: {{ $medicine->form_display }}
                            </small>
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
                                            {{ (old('prescription_type', $medicine->prescription_type) == $key) ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">
                                Current: {{ $medicine->prescription_type_display }}
                            </small>
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
                                            {{ (old('storage_type', $medicine->storage_type) == $key) ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">
                                Current: {{ $medicine->storage_type_display }}
                            </small>
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
                                       value="{{ old('barcode', $medicine->barcode) }}" 
                                       placeholder="Enter or scan barcode">
                                <button type="button" class="btn btn-outline-secondary" id="generate-barcode">
                                    <i class="fas fa-barcode"></i> Generate
                                </button>
                            </div>
                            <small class="form-text text-muted">
                                Current: {{ $medicine->barcode ?? 'Not set' }}
                            </small>
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
                                       value="{{ old('unit_price', $medicine->unit_price) }}" 
                                       step="0.01" 
                                       min="0" 
                                       placeholder="0.00"
                                       required>
                            </div>
                            <small class="form-text text-muted">
                                Current: ${{ number_format($medicine->unit_price, 2) }}
                            </small>
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
                                   value="{{ old('reorder_level', $medicine->reorder_level) }}" 
                                   min="0" 
                                   placeholder="Minimum stock level"
                                   required>
                            <small class="form-text text-muted">
                                Current: {{ $medicine->reorder_level }} units
                            </small>
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
                                       {{ old('is_active', $medicine->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active Medicine
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                Current: {{ $medicine->is_active ? 'Active' : 'Inactive' }}
                            </small>
                            @error('is_active')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Changes Summary -->
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">
                                        <i class="fas fa-info-circle"></i> Change Summary
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div id="changes-summary" class="text-muted">
                                        <em>Make changes to see what will be updated...</em>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="col-12">
                            <hr>
                            <div class="d-flex justify-content-between">
                                <div>
                                    <button type="button" class="btn btn-info" id="preview-changes">
                                        <i class="fas fa-eye"></i> Preview Changes
                                    </button>
                                </div>
                                <div class="btn-group">
                                    <a href="{{ route('medicines.show', $medicine) }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-warning" id="update-btn">
                                        <i class="fas fa-save"></i> Update Medicine
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Unsaved Changes Warning Modal -->
<div class="modal fade" id="unsavedChangesModal" tabindex="-1" aria-labelledby="unsavedChangesModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="unsavedChangesModalLabel">Unsaved Changes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    You have unsaved changes. Are you sure you want to leave this page?
                </div>
                <p>Your changes will be lost if you continue without saving.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Stay on Page</button>
                <button type="button" class="btn btn-warning" id="leave-anyway">Leave Anyway</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let originalData = {};
    let hasChanges = false;
    let pendingNavigation = null;

    // Store original form data
    const form = document.getElementById('medicine-form');
    const formData = new FormData(form);
    for (let [key, value] of formData.entries()) {
        originalData[key] = value;
    }

    // Generate random barcode
    document.getElementById('generate-barcode').addEventListener('click', function() {
        const timestamp = Date.now();
        const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
        const barcode = timestamp.toString() + random;
        document.getElementById('barcode').value = barcode;
        checkForChanges();
    });

    // Monitor form changes
    const inputs = form.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.addEventListener('change', checkForChanges);
        input.addEventListener('input', checkForChanges);
    });

    function checkForChanges() {
        hasChanges = false;
        const currentData = new FormData(form);
        const changes = [];

        for (let [key, value] of currentData.entries()) {
            if (originalData[key] !== value) {
                hasChanges = true;
                const label = document.querySelector(`label[for="${key}"]`)?.textContent?.replace('*', '').trim() || key;
                changes.push({
                    field: label,
                    from: originalData[key] || 'Not set',
                    to: value || 'Not set'
                });
            }
        }

        updateChangesSummary(changes);
        toggleUpdateButton();
    }

    function updateChangesSummary(changes) {
        const summaryDiv = document.getElementById('changes-summary');
        
        if (changes.length === 0) {
            summaryDiv.innerHTML = '<em>No changes detected...</em>';
            summaryDiv.className = 'text-muted';
        } else {
            let html = '<strong>Fields to be updated:</strong><ul class="mb-0 mt-2">';
            changes.forEach(change => {
                html += `<li><strong>${change.field}:</strong> "${change.from}" → "${change.to}"</li>`;
            });
            html += '</ul>';
            summaryDiv.innerHTML = html;
            summaryDiv.className = 'text-info';
        }
    }

    function toggleUpdateButton() {
        const updateBtn = document.getElementById('update-btn');
        if (hasChanges) {
            updateBtn.classList.remove('btn-secondary');
            updateBtn.classList.add('btn-warning');
            updateBtn.disabled = false;
        } else {
            updateBtn.classList.remove('btn-warning');
            updateBtn.classList.add('btn-secondary');
            updateBtn.disabled = true;
        }
    }

    // Preview changes
    document.getElementById('preview-changes').addEventListener('click', function() {
        checkForChanges();
        if (hasChanges) {
            showToast('Changes highlighted in the summary below', 'info');
        } else {
            showToast('No changes to preview', 'warning');
        }
    });

    // Prevent navigation with unsaved changes
    function preventNavigation(e) {
        if (hasChanges) {
            e.preventDefault();
            e.returnValue = '';
            return '';
        }
    }

    window.addEventListener('beforeunload', preventNavigation);

    // Handle navigation links with unsaved changes
    document.querySelectorAll('a[href]').forEach(link => {
        link.addEventListener('click', function(e) {
            if (hasChanges && !this.closest('form')) {
                e.preventDefault();
                pendingNavigation = this.href;
                new bootstrap.Modal(document.getElementById('unsavedChangesModal')).show();
            }
        });
    });

    // Handle leave anyway button
    document.getElementById('leave-anyway').addEventListener('click', function() {
        hasChanges = false; // Disable the warning
        if (pendingNavigation) {
            window.location.href = pendingNavigation;
        }
    });

    // Form validation feedback
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.hasAttribute('required') && this.value.trim() === '') {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });
    });

    // Price formatting
    const priceInput = document.getElementById('unit_price');
    priceInput.addEventListener('blur', function() {
        if (this.value) {
            this.value = parseFloat(this.value).toFixed(2);
            checkForChanges();
        }
    });

    // Initialize
    toggleUpdateButton();
});

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} position-fixed top-0 end-0 m-3`;
    toast.style.zIndex = '9999';
    toast.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check' : type === 'warning' ? 'exclamation' : 'info'}-circle"></i> ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 4000);
}
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

.form-text {
    font-size: 0.8em;
    margin-top: 0.25rem;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
}

.alert-info {
    background-color: #cff4fc;
    border-color: #b6effb;
    color: #055160;
}

.btn-warning {
    background-color: #ffc107;
    border-color: #ffc107;
    color: #000;
}

.bg-light {
    background-color: #f8f9fa !important;
}

.is-invalid {
    border-color: #dc3545;
}

.invalid-feedback {
    display: block;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.875em;
    color: #dc3545;
}

/* Highlight changed fields */
.field-changed {
    background-color: #fff3cd;
    border-color: #ffc107;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .btn-group {
        flex-direction: column;
        align-items: stretch;
    }
    
    .btn-group .btn {
        margin-bottom: 0.25rem;
        border-radius: 0.375rem !important;
    }
    
    .d-flex.justify-content-between {
        flex-direction: column;
        gap: 1rem;
    }
}
</style>
@endpush
@endsection