@extends('layouts.app')

@section('title', 'Suppliers Management')
@section('header', 'Suppliers Management')

@section('header-button')
    <a href="{{ route('suppliers.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-circle me-1"></i> Add New Supplier
    </a>
@endsection

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">All Suppliers</h6>
        <span class="badge bg-info">{{ $suppliers->total() }} suppliers</span>
    </div>
    <div class="card-body">
        <!-- Search and Filter Section -->
        <div class="row mb-4">
            <div class="col-md-8">
                <form method="GET" action="{{ route('suppliers.index') }}" class="row g-2">
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0">
                                <i class="bi bi-search text-muted"></i>
                            </span>
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="Search suppliers by name, contact person..." 
                                   class="form-control border-0 bg-light">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-funnel me-1"></i> Filter
                        </button>
                    </div>
                </form>
            </div>
            <div class="col-md-4 text-end">
                <div class="btn-group">
                    <button class="btn btn-outline-secondary btn-sm" onclick="printTable()">
                        <i class="bi bi-printer me-1"></i> Print
                    </button>
                    <button class="btn btn-outline-secondary btn-sm" onclick="exportToCSV()">
                        <i class="bi bi-download me-1"></i> Export
                    </button>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-start border-primary border-4 h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="bi bi-truck text-primary" style="font-size: 2rem;"></i>
                            </div>
                            <div>
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Suppliers
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $suppliers->total() }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-start border-success border-4 h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="bi bi-check-circle text-success" style="font-size: 2rem;"></i>
                            </div>
                            <div>
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Active Suppliers
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $suppliers->where('is_active', true)->count() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-start border-warning border-4 h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="bi bi-pause-circle text-warning" style="font-size: 2rem;"></i>
                            </div>
                            <div>
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Inactive Suppliers
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $suppliers->where('is_active', false)->count() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-start border-info border-4 h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="bi bi-building text-info" style="font-size: 2rem;"></i>
                            </div>
                            <div>
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Unique Pharmacies
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $suppliers->unique('pharmacy_id')->count() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Suppliers Table -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="suppliersTable">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Pharmacy</th>
                        <th>Supplier Details</th>
                        <th>Contact Information</th>
                        <th>Payment Terms</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $supplier)
                    <tr>
                        <td class="fw-bold">{{ $supplier->id }}</td>
                        <td>
                            <span class="badge bg-light text-dark">
                                <i class="bi bi-building me-1"></i>
                                {{ $supplier->pharmacy->name }}
                            </span>
                        </td>
                        <td>
                            <div>
                                <strong class="d-block">{{ $supplier->name }}</strong>
                                <small class="text-muted">
                                    <i class="bi bi-person me-1"></i>
                                    {{ $supplier->contact_person }}
                                </small>
                            </div>
                        </td>
                        <td>
                            <div>
                                <div class="text-truncate" style="max-width: 200px;">
                                    <i class="bi bi-telephone me-1 text-muted"></i>
                                    {{ $supplier->phone }}
                                </div>
                                @if($supplier->email)
                                <div class="text-muted small">
                                    <i class="bi bi-envelope me-1"></i>
                                    {{ $supplier->email }}
                                </div>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-info">
                                <i class="bi bi-credit-card me-1"></i>
                                {{ $supplier->payment_terms_label }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $supplier->is_active ? 'success' : 'danger' }}">
                                <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i>
                                {{ $supplier->status_label }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('suppliers.show', $supplier) }}" 
                                   class="btn btn-outline-primary" 
                                   title="View Details"
                                   data-bs-toggle="tooltip">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('suppliers.edit', $supplier) }}" 
                                   class="btn btn-outline-warning" 
                                   title="Edit Supplier"
                                   data-bs-toggle="tooltip">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('suppliers.destroy', $supplier) }}" 
                                      method="POST" 
                                      class="d-inline"
                                      onsubmit="return confirm('Are you sure you want to delete this supplier?')">
                                    @csrf 
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-outline-danger" 
                                            title="Delete Supplier"
                                            data-bs-toggle="tooltip">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="empty-state">
                                <i class="bi bi-truck text-muted" style="font-size: 4rem;"></i>
                                <h5 class="text-muted mt-3">No suppliers found</h5>
                                <p class="text-muted">There are no suppliers in the system yet.</p>
                                <a href="{{ route('suppliers.create') }}" class="btn btn-primary mt-2">
                                    <i class="bi bi-plus-circle me-1"></i> Add First Supplier
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($suppliers->hasPages())
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div class="text-muted">
                Showing {{ $suppliers->firstItem() }} to {{ $suppliers->lastItem() }} of {{ $suppliers->total() }} entries
            </div>
            <nav>
                {{ $suppliers->links('pagination::bootstrap-5') }}
            </nav>
        </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    .card {
        border: none;
        border-radius: 0.5rem;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }
    
    .table th {
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        color: #6e707e;
        background: #f8f9fc;
        border-bottom: 2px solid #e3e6f0;
    }
    
    .table td {
        vertical-align: middle;
        border-color: #e3e6f0;
    }
    
    .table tbody tr:hover {
        background-color: #f8f9fc;
        transform: translateY(-1px);
        transition: all 0.2s ease;
    }
    
    .badge {
        font-weight: 500;
        padding: 0.5rem 0.75rem;
    }
    
    .btn-group-sm > .btn {
        padding: 0.375rem 0.5rem;
        border-radius: 0.375rem;
    }
    
    .empty-state {
        padding: 2rem 0;
    }
    
    .border-start {
        border-left-width: 4px !important;
    }
    
    .input-group-text {
        background: #f8f9fc;
        border: none;
    }
    
    .form-control:focus, .form-select:focus {
        box-shadow: none;
        border-color: #4e73df;
    }
    
    .table-responsive {
        border-radius: 0.5rem;
        overflow: hidden;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Search functionality
    const searchInput = document.querySelector('input[name="search"]');
    const supplierRows = document.querySelectorAll('tbody tr');
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            supplierRows.forEach(row => {
                if (row.querySelector('.empty-state')) return;
                
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    // Print functionality
    window.printTable = function() {
        const printWindow = window.open('', '_blank');
        const tableContent = document.getElementById('suppliersTable').outerHTML;
        
        printWindow.document.write(`
            <html>
                <head>
                    <title>Suppliers List</title>
                    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
                    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
                    <style>
                        body { padding: 20px; }
                        .table { width: 100%; }
                        .badge { padding: 0.25rem 0.5rem; }
                    </style>
                </head>
                <body>
                    <h2 class="mb-4">Suppliers List</h2>
                    ${tableContent}
                    <div class="mt-4 text-muted">Generated on: ${new Date().toLocaleDateString()}</div>
                </body>
            </html>
        `);
        
        printWindow.document.close();
        printWindow.print();
    };

    // Export to CSV functionality
    window.exportToCSV = function() {
        const rows = [];
        const headers = [];
        
        // Get headers
        document.querySelectorAll('#suppliersTable thead th').forEach(th => {
            headers.push(th.textContent.trim());
        });
        rows.push(headers.join(','));
        
        // Get data rows
        document.querySelectorAll('#suppliersTable tbody tr').forEach(tr => {
            if (tr.style.display === 'none') return;
            
            const row = [];
            tr.querySelectorAll('td').forEach(td => {
                let text = td.textContent.trim();
                // Remove icon texts and clean up
                text = text.replace(/View|Edit|Delete/g, '').trim();
                // Handle commas in CSV
                text = text.includes(',') ? `"${text}"` : text;
                row.push(text);
            });
            rows.push(row.join(','));
        });
        
        const csvContent = rows.join('\n');
        const blob = new Blob([csvContent], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        
        a.setAttribute('hidden', '');
        a.setAttribute('href', url);
        a.setAttribute('download', `suppliers_${new Date().toISOString().split('T')[0]}.csv`);
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    };

    // Add loading state to search
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            this.style.backgroundImage = 'url("data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'%236c757d\' width=\'20px\' height=\'20px\'%3E%3Cpath d=\'M0 0h24v24H0z\' fill=\'none\'/%3E%3Cpath d=\'M12 4V1L8 5l4 4V6c3.31 0 6 2.69 6 6 0 1.01-.25 1.97-.7 2.8l1.46 1.46C19.54 15.03 20 13.57 20 12c0-4.42-3.58-8-8-8zm0 14c-3.31 0-6-2.69-6-6 0-1.01.25-1.97.7-2.8L5.24 7.74C4.46 8.97 4 10.43 4 12c0 4.42 3.58 8 8 8v3l4-4-4-4v3z\'/%3E%3C/svg%3E")';
            this.style.backgroundRepeat = 'no-repeat';
            this.style.backgroundPosition = 'right 10px center';
            this.style.backgroundSize = '20px';
            
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                this.style.backgroundImage = 'none';
            }, 1000);
        });
    }
});
</script>
@endpush