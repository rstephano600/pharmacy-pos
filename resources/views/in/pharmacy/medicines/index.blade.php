@extends('layouts.app')

@section('title', 'Medicines Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-pills"></i> Medicines Management
                    </h3>
                    <div class="btn-group">
                        <a href="{{ route('medicines.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add New Medicine
                        </a>
                        <a href="{{ route('medicines.export', request()->query()) }}" class="btn btn-success">
                            <i class="fas fa-download"></i> Export CSV
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <form method="GET" action="{{ route('medicines.index') }}" class="row g-2">
                                <div class="col-md-3">
                                    <input type="text" 
                                           name="search" 
                                           class="form-control" 
                                           placeholder="Search medicines..." 
                                           value="{{ request('search') }}">
                                </div>
                                <div class="col-md-2">
                                    <select name="category_id" class="form-select">
                                        <option value="">All Categories</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" 
                                                    {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->category_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="prescription_type" class="form-select">
                                        <option value="">All Types</option>
                                        @foreach($prescriptionTypes as $key => $label)
                                            <option value="{{ $key }}" 
                                                    {{ request('prescription_type') == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="form" class="form-select">
                                        <option value="">All Forms</option>
                                        @foreach($forms as $key => $label)
                                            <option value="{{ $key }}" 
                                                    {{ request('form') == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="status" class="form-select">
                                        <option value="">All Status</option>
                                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                                <div class="col-md-1">
                                    <button type="submit" class="btn btn-outline-primary w-100">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Results Info -->
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <p class="text-muted mb-0">
                                Showing {{ $medicines->firstItem() ?? 0 }} to {{ $medicines->lastItem() ?? 0 }} 
                                of {{ $medicines->total() }} medicines
                            </p>
                        </div>
                        <div class="col-sm-6 text-end">
                            @if(request()->hasAny(['search', 'category_id', 'prescription_type', 'form', 'status']))
                                <a href="{{ route('medicines.index') }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-times"></i> Clear Filters
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Medicines Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Medicine Details</th>
                                    <th>Category</th>
                                    <th>Form & Type</th>
                                    <th>Storage</th>
                                    <th>Price</th>
                                    <th>Stock Info</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($medicines as $medicine)
                                    <tr>
                                        <td>
                                            <div>
                                                <strong>{{ $medicine->name }}</strong>
                                                @if($medicine->strength)
                                                    <span class="badge bg-info ms-1">{{ $medicine->strength }}</span>
                                                @endif
                                                <br>
                                                @if($medicine->generic_name)
                                                    <small class="text-muted">Generic: {{ $medicine->generic_name }}</small><br>
                                                @endif
                                                @if($medicine->brand_name)
                                                    <small class="text-muted">Brand: {{ $medicine->brand_name }}</small><br>
                                                @endif
                                                @if(auth()->user()->hasRole('super_admin'))
                                                <small class="text-muted">{{ $medicine->pharmacy->name ?? 'N/A' }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $medicine->category->category_name ?? 'Uncategorized' }}</span>
                                        </td>
                                        <td>
                                            <div>
                                                <span class="badge bg-primary mb-1">{{ $medicine->form_display }}</span><br>
                                                <small class="text-muted">{{ $medicine->prescription_type_display }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $medicine->storage_type_display }}</small>
                                        </td>
                                        <td>
                                            <strong>{{ number_format($medicine->unit_price, 2) }}</strong>
                                        </td>
                                        <td>
                                            <div>
                                                <small class="text-muted">Reorder: {{ $medicine->reorder_level }}</small>
                                                @if($medicine->isBelowReorderLevel())
                                                    <br><span class="badge bg-warning text-dark">Low Stock</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $medicine->is_active ? 'success' : 'danger' }}">
                                                {{ $medicine->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('medicines.show', $medicine) }}" 
                                                   class="btn btn-outline-info" 
                                                   title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('medicines.edit', $medicine) }}" 
                                                   class="btn btn-outline-warning" 
                                                   title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-outline-{{ $medicine->is_active ? 'secondary' : 'success' }}" 
                                                        title="{{ $medicine->is_active ? 'Deactivate' : 'Activate' }}"
                                                        onclick="toggleStatus({{ $medicine->id }})">
                                                    <i class="fas fa-{{ $medicine->is_active ? 'pause' : 'play' }}"></i>
                                                </button>
                                                <button type="button" 
                                                        class="btn btn-outline-danger" 
                                                        title="Delete"
                                                        onclick="deleteMedicine({{ $medicine->id }}, '{{ $medicine->name }}')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-pills fa-3x mb-3"></i>
                                                <h5>No Medicines Found</h5>
                                                <p>No medicines match your current filters.</p>
                                                @if(request()->hasAny(['search', 'category_id', 'prescription_type', 'form', 'status']))
                                                    <a href="{{ route('medicines.index') }}" class="btn btn-primary">
                                                        View All Medicines
                                                    </a>
                                                @else
                                                    <a href="{{ route('medicines.create') }}" class="btn btn-primary">
                                                        Add First Medicine
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($medicines->hasPages())
                        <div class="d-flex justify-content-center mt-3">
                            {{ $medicines->links() }}
                        </div>
                    @endif
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
                Are you sure you want to delete <strong id="medicine-name"></strong>? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="delete-form" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Medicine</button>
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
            <div class="modal-body" id="toggle-message">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="toggle-form" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-primary" id="toggle-btn">Confirm</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function deleteMedicine(id, name) {
    document.getElementById('medicine-name').textContent = name;
    document.getElementById('delete-form').action = `/medicines/${id}`;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

function toggleStatus(id) {
    document.getElementById('toggle-form').action = `/medicines/${id}/toggle-status`;
    new bootstrap.Modal(document.getElementById('toggleModal')).show();
}

// Auto-submit form on filter change (optional)
document.querySelectorAll('select[name]').forEach(select => {
    select.addEventListener('change', function() {
        // Uncomment to auto-submit on filter change
        // this.closest('form').submit();
    });
});

// Show success/error messages
@if(session('success'))
    const successToast = new bootstrap.Toast(document.querySelector('.toast-success'));
    successToast.show();
@endif

@if(session('error'))
    const errorToast = new bootstrap.Toast(document.querySelector('.toast-error'));
    errorToast.show();
@endif
</script>
@endpush

@push('styles')
<style>
.table th {
    font-weight: 600;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.badge {
    font-size: 0.75em;
    font-weight: 500;
}

.btn-group-sm > .btn, .btn-sm {
    padding: 0.375rem 0.5rem;
    font-size: 0.8125rem;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
}

.table-responsive {
    border-radius: 0.375rem;
}

.form-control, .form-select {
    border-radius: 0.375rem;
}

.text-muted {
    color: #6c757d !important;
}

.table-striped > tbody > tr:nth-of-type(odd) > td {
    background-color: rgba(0, 0, 0, 0.025);
}

.table-hover > tbody > tr:hover > td {
    background-color: rgba(0, 0, 0, 0.075);
}
</style>
@endpush
@endsection