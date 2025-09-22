@extends('layouts.app')

@section('title', 'Manage Pharmacies - Admin')
@section('header', 'Pharmacy Management')

@section('header-button')
    <a href="{{ route('superadmin.pharmacies.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-circle me-1"></i> Add New Pharmacy
    </a>
@endsection

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">All Pharmacies</h6>
    </div>
    <div class="card-body">
                <div class="row mb-4">
            <div class="col-md-6">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search pharmacies..." id="searchInput">
                    <button class="btn btn-outline-secondary" type="button">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex gap-2 justify-content-end">
                    <select class="form-select form-select-sm" style="width: auto;">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                    <select class="form-select form-select-sm" style="width: auto;">
                        <option value="">Sort by</option>
                        <option value="newest">Newest First</option>
                        <option value="oldest">Oldest First</option>
                    </select>
                </div>
            </div>
        </div>

        
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>License Number</th>
                        <th>Location</th>
                        <th>Staff</th>
                        <th>Status</th>
                        <th>License Expiry</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pharmacies as $pharmacy)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <img src="{{ asset('storage/' . $pharmacy->pharmacy_logo) }}" 
                                         alt="{{ $pharmacy->name }}" 
                                         class="rounded-circle" 
                                         width="40" 
                                         height="40"
                                         onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($pharmacy->name) }}&background=4e73df&color=fff'">
                                </div>
                                <div>
                                    <strong>{{ $pharmacy->name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $pharmacy->contact_email }}</small>
                                </div>
                            </div>
                        </td>
                        <td><code>{{ $pharmacy->license_number }}</code></td>
                        <td>
                            <small>
                                {{ $pharmacy->location }}<br>
                                <span class="text-muted">{{ $pharmacy->district }}, {{ $pharmacy->region }}</span>
                            </small>
                        </td>
                        <td>
                            <span class="badge bg-info">{{ $pharmacy->users_count }} users</span><br>
                            <span class="badge bg-success">{{ $pharmacy->staff_count }} staff</span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $pharmacy->is_active ? 'success' : 'danger' }}">
                                {{ $pharmacy->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            @if($pharmacy->license_expiry)
                                <span class="badge bg-{{ $pharmacy->isLicenseExpired() ? 'danger' : 'success' }}">
                                    {{ $pharmacy->license_expiry->format('M d, Y') }}
                                </span>
                            @else
                                <span class="badge bg-warning">Not set</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('superadmin.pharmacies.show', $pharmacy) }}" class="btn btn-outline-primary" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('superadmin.pharmacies.edit', $pharmacy) }}" class="btn btn-outline-success" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('superadmin.pharmacies.destroy', $pharmacy) }}" method="POST" onsubmit="return confirm('Are you sure?')" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <i class="bi bi-building text-muted" style="font-size: 3rem;"></i>
                            <h5 class="text-muted mt-3">No pharmacies found</h5>
                            <p class="text-muted">There are no pharmacies registered in the system yet.</p>
                            <a href="{{ route('superadmin.pharmacies.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-1"></i> Add First Pharmacy
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($pharmacies->hasPages())
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div class="text-muted">
                Showing {{ $pharmacies->firstItem() }} to {{ $pharmacies->lastItem() }} of {{ $pharmacies->total() }} entries
            </div>
            <nav>
                {{ $pharmacies->links('pagination::bootstrap-5') }}
            </nav>
        </div>
        @endif
    </div>
</div>


<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card border-left-primary h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-building text-primary" style="font-size: 2rem;"></i>
                    </div>
                    <div>
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Pharmacies
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pharmacies->total() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card border-left-success h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-check-circle text-success" style="font-size: 2rem;"></i>
                    </div>
                    <div>
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Active Pharmacies
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $pharmacies->where('is_active', true)->count() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card border-left-warning h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-exclamation-triangle text-warning" style="font-size: 2rem;"></i>
                    </div>
                    <div>
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Expired Licenses
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $pharmacies->filter(function($pharmacy) { return $pharmacy->isLicenseExpired(); })->count() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card border-left-info h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-people text-info" style="font-size: 2rem;"></i>
                    </div>
                    <div>
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Total Staff
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $pharmacies->sum('staff_count') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .table th {
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        color: #6e707e;
    }
    
    .table img {
        object-fit: cover;
    }
    
    .btn-group-sm > .btn {
        padding: 0.25rem 0.5rem;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('searchInput');
    const tableRows = document.querySelectorAll('tbody tr');
    
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        
        tableRows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });
    
    // Status badge styling
    const statusBadges = document.querySelectorAll('.badge');
    statusBadges.forEach(badge => {
        if (badge.textContent.includes('Active')) {
            badge.classList.add('bg-success');
        } else if (badge.textContent.includes('Inactive')) {
            badge.classList.add('bg-danger');
        } else if (badge.textContent.includes('Not set')) {
            badge.classList.add('bg-warning');
        }
    });
});
</script>
@endpush