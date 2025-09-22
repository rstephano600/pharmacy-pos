@extends('layouts.app')

@section('title', 'Medicine Details - ' . $medicine->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header Card -->
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="card-title mb-0">
                            <i class="fas fa-pills"></i> {{ $medicine->name }}
                            @if($medicine->strength)
                                <span class="badge bg-info ms-2">{{ $medicine->strength }}</span>
                            @endif
                        </h3>
                        <small class="text-muted">Medicine Details</small>
                    </div>
                    <div class="btn-group">
                        <a href="{{ route('medicines.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                        <a href="{{ route('medicines.edit', $medicine) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit Medicine
                        </a>
                        <button type="button" 
                                class="btn btn-{{ $medicine->is_active ? 'outline-secondary' : 'outline-success' }}" 
                                onclick="toggleStatus({{ $medicine->id }})">
                            <i class="fas fa-{{ $medicine->is_active ? 'pause' : 'play' }}"></i> 
                            {{ $medicine->is_active ? 'Deactivate' : 'Activate' }}
                        </button>
                        <button type="button" 
                                class="btn btn-outline-danger" 
                                onclick="deleteMedicine({{ $medicine->id }}, '{{ $medicine->name }}')">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Main Information -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0"><i class="fas fa-info-circle"></i> Basic Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="fw-bold text-muted" style="width: 40%;">Medicine Name:</td>
                                            <td>{{ $medicine->name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold text-muted">Generic Name:</td>
                                            <td>{{ $medicine->generic_name ?? 'Not specified' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold text-muted">Brand Name:</td>
                                            <td>{{ $medicine->brand_name ?? 'Not specified' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold text-muted">Category:</td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    {{ $medicine->category->name ?? 'Uncategorized' }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold text-muted">Pharmacy:</td>
                                            <td>
                                                <span class="badge bg-primary">
                                                    {{ $medicine->pharmacy->name ?? 'Not assigned' }}
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="fw-bold text-muted" style="width: 40%;">Strength:</td>
                                            <td>{{ $medicine->strength ?? 'Not specified' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold text-muted">Form:</td>
                                            <td>
                                                <span class="badge bg-info">
                                                    {{ $medicine->form_display }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold text-muted">Prescription Type:</td>
                                            <td>
                                                <span class="badge bg-warning text-dark">
                                                    {{ $medicine->prescription_type_display }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold text-muted">Storage Type:</td>
                                            <td>
                                                <span class="badge bg-dark">
                                                    {{ $medicine->storage_type_display }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold text-muted">Status:</td>
                                            <td>
                                                <span class="badge bg-{{ $medicine->is_active ? 'success' : 'danger' }}">
                                                    <i class="fas fa-{{ $medicine->is_active ? 'check' : 'times' }}"></i>
                                                    {{ $medicine->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Barcode & Pricing -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0"><i class="fas fa-barcode"></i> Barcode & Pricing</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-muted">Barcode:</label>
                                        @if($medicine->barcode)
                                            <div class="d-flex align-items-center">
                                                <code class="bg-light p-2 rounded me-2">{{ $medicine->barcode }}</code>
                                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="copyBarcode()">
                                                    <i class="fas fa-copy"></i> Copy
                                                </button>
                                            </div>
                                        @else
                                            <span class="text-muted">Not assigned</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-muted">Unit Price:</label>
                                        <div>
                                            <h4 class="text-primary mb-0">${{ number_format($medicine->unit_price, 2) }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Stock Information -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0"><i class="fas fa-boxes"></i> Stock Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-12 mb-3">
                                    <div class="border rounded p-3">
                                        <h6 class="text-muted mb-2">Reorder Level</h6>
                                        <h3 class="text-warning mb-0">{{ number_format($medicine->reorder_level) }}</h3>
                                        <small class="text-muted">units</small>
                                    </div>
                                </div>
                            </div>
                            
                            @if($medicine->isBelowReorderLevel())
                                <div class="alert alert-warning" role="alert">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong>Low Stock Alert!</strong> 
                                    This medicine is below the reorder level.
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Record Information -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0"><i class="fas fa-clock"></i> Record Information</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td class="fw-bold text-muted">Medicine ID:</td>
                                    <td><code>#{{ $medicine->id }}</code></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Created:</td>
                                    <td>
                                        {{ $medicine->created_at->format('M d, Y') }}<br>
                                        <small class="text-muted">{{ $medicine->created_at->format('h:i A') }}</small>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Last Updated:</td>
                                    <td>
                                        {{ $medicine->updated_at->format('M d, Y') }}<br>
                                        <small class="text-muted">{{ $medicine->updated_at->format('h:i A') }}</small>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Days Since Created:</td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            {{ $medicine->created_at->diffForHumans() }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0"><i class="fas fa-bolt"></i> Quick Actions</h5>
                        </div>
                        <div class="card-body d-grid gap-2">
                            <button type="button" class="btn btn-outline-primary" onclick="printDetails()">
                                <i class="fas fa-print"></i> Print Details
                            </button>
                            <button type="button" class="btn btn-outline-info" onclick="generateReport()">
                                <i class="fas fa-file-alt"></i> Generate Report
                            </button>
                            @if($medicine->barcode)
                                <button type="button" class="btn btn-outline-secondary" onclick="printBarcode()">
                                    <i class="fas fa-barcode"></i> Print Barcode
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Warning!</strong> This action cannot be undone.
                </div>
                Are you sure you want to delete <strong id="medicine-name">{{ $medicine->name }}</strong>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="delete-form" method="POST" action="{{ route('medicines.destroy', $medicine) }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Delete Medicine
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Toggle Status Modal -->
<div class="modal fade" id="toggleModal" tabindex="-1" aria-labelledby="toggleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="toggleModalLabel">Confirm Status Change</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to <strong>{{ $medicine->is_active ? 'deactivate' : 'activate' }}</strong> this medicine?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('medicines.toggle-status', $medicine) }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-{{ $medicine->is_active ? 'pause' : 'play' }}"></i> 
                        {{ $medicine->is_active ? 'Deactivate' : 'Activate' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function deleteMedicine(id, name) {
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

function toggleStatus(id) {
    new bootstrap.Modal(document.getElementById('toggleModal')).show();
}

function copyBarcode() {
    const barcode = '{{ $medicine->barcode }}';
    navigator.clipboard.writeText(barcode).then(function() {
        // Show toast notification
        showToast('Barcode copied to clipboard!', 'success');
    });
}

function printDetails() {
    window.print();
}

function generateReport() {
    // Implement report generation logic
    showToast('Report generation feature coming soon!', 'info');
}

function printBarcode() {
    // Implement barcode printing logic
    showToast('Barcode printing feature coming soon!', 'info');
}

function showToast(message, type = 'info') {
    // Simple toast notification
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} position-fixed top-0 end-0 m-3`;
    toast.style.zIndex = '9999';
    toast.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check' : 'info'}-circle"></i> ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

// Print styles
window.addEventListener('beforeprint', function() {
    document.body.classList.add('printing');
});

window.addEventListener('afterprint', function() {
    document.body.classList.remove('printing');
});
</script>
@endpush

@push('styles')
<style>
.table-borderless td {
    border: none;
    padding: 0.5rem 0.75rem;
}

.badge {
    font-size: 0.8em;
    padding: 0.5em 0.75em;
}

code {
    font-size: 0.9em;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
}

.text-primary {
    color: #0d6efd !important;
}

.text-muted {
    color: #6c757d !important;
}

.border {
    border: 1px solid #dee2e6 !important;
}

.alert {
    border-radius: 0.375rem;
}

/* Print styles */
@media print {
    .btn, .modal, .card-header .btn-group {
        display: none !important;
    }
    
    .card {
        box-shadow: none;
        border: 1px solid #000;
    }
    
    body.printing .card-body {
        padding: 1rem;
    }
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
    
    .table-responsive {
        font-size: 0.9rem;
    }
}
</style>
@endpush
@endsection