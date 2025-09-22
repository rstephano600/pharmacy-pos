@extends('layouts.app')

@section('title', 'Manage Users - Super Admin')
@section('header', 'User Management')

@section('header-button')
    <a href="{{ route('superadmin.users.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-circle me-1"></i> Add New User
    </a>
@endsection

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">All Users</h6>
        <span class="badge bg-info">{{ $users->total() }} users</span>
    </div>
    <div class="card-body">
        <!-- Search and Filters -->
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-text bg-light border-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" class="form-control border-0 bg-light" 
                           placeholder="Search users by name, email, username..." 
                           id="searchInput"
                           style="border-radius: 0.375rem;">
                    <button class="btn btn-primary" type="button" id="searchButton">
                        <i class="bi bi-search me-1"></i> Search
                    </button>
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-flex gap-2">
                    <select class="form-select form-select-sm" id="roleFilter">
                        <option value="">All Roles</option>
                        <option value="super_admin">Super Admin</option>
                        <option value="admin">Admin</option>
                        <option value="pharmacy_owner">Pharmacy Owner</option>
                        <option value="pharmacist">Pharmacist</option>
                        <option value="pharmacy_technician">Pharmacy Technician</option>
                        <option value="user">User</option>
                    </select>
                    <select class="form-select form-select-sm" id="statusFilter">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-2">
            <div class="col-xl-2 col-md-4 col-6">
                <div class="card border-start border-primary border-4 h-100">
                    <div class="card-body">
                        <div class="text-center">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $users->total() }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-6">
                <div class="card border-start border-success border-4 h-100">
                    <div class="card-body">
                        <div class="text-center">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Active</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $users->where('is_active', true)->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-6">
                <div class="card border-start border-warning border-4 h-100">
                    <div class="card-body">
                        <div class="text-center">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Admins</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $users->whereIn('role', ['super_admin', 'admin'])->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-6">
                <div class="card border-start border-info border-4 h-100">
                    <div class="card-body">
                        <div class="text-center">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Staff</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $users->whereIn('role', ['pharmacist', 'pharmacy_technician'])->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-6">
                <div class="card border-start border-secondary border-4 h-100">
                    <div class="card-body">
                        <div class="text-center">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Owners</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $users->where('role', 'pharmacy_owner')->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-6">
                <div class="card border-start border-dark border-4 h-100">
                    <div class="card-body">
                        <div class="text-center">
                            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">Users</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $users->where('role', 'user')->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="table-responsive">
        <div class="table-responsive2">
            <table class="table table-bordered table-hover" id="usersTable">
                <thead class="table-light">
                    <tr>
                        <th>User</th>
                        <th>Contact Info</th>
                        <th>Role</th>
                        <!-- <th>Pharmacy</th> -->
                        <th>Status</th>
                        <th>Last Activity</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr class="user-row" data-role="{{ $user->role }}" data-status="{{ $user->is_active ? 'active' : 'inactive' }}">
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar me-3">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->full_name) }}&background=4e73df&color=fff&size=64" 
                                         class="rounded-circle" width="40" height="40" alt="{{ $user->full_name }}">
                                </div>
                                <div>
                                    <strong class="d-block">{{ $user->full_name }}</strong>
                                    <small class="text-muted">{{ $user->username }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div>
                                <div class="text-truncate" style="max-width: 200px;">
                                    <i class="bi bi-envelope me-1 text-muted"></i>
                                    {{ $user->email ?? 'No email' }}
                                </div>
                                <div class="text-muted small">
                                    <i class="bi bi-telephone me-1"></i>
                                    {{ $user->phone ?? 'No phone' }}
                                </div>
                            </div>
                        </td>
                        <td>
                            @php
                                $roleColors = [
                                    'super_admin' => 'danger',
                                    'admin' => 'warning',
                                    'pharmacy_owner' => 'primary',
                                    'pharmacist' => 'success',
                                    'pharmacy_technician' => 'info',
                                    'user' => 'secondary'
                                ];
                            @endphp
                            <span class="badge bg-{{ $roleColors[$user->role] ?? 'secondary' }}">
                                <i class="bi bi-person-badge me-1"></i>
                                {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                            </span>
                        </td>
                       <!-- <td>
                         @if($user->pharmacies->isNotEmpty())
                             @foreach($user->pharmacies as $pharmacy)
                              <span class="badge bg-info">{{ $pharmacy->name }}</span>
                            @endforeach
                            @else
                                <span class="badge bg-secondary">— No pharmacy —</span>
                            @endif
                        </td> -->
                        <td>
                            <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }}">
                                <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i>
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <div class="text-muted small">
                                @if($user->last_login)
                                    <div><i class="bi bi-clock me-1"></i> {{ $user->last_login->diffForHumans() }}</div>
                                    <div>{{ $user->last_login->format('M d, Y H:i') }}</div>
                                @else
                                    <span class="text-muted">Never logged in</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('superadmin.users.show', $user) }}" class="btn btn-outline-primary" title="View Details" data-bs-toggle="tooltip">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('superadmin.users.edit', $user) }}" class="btn btn-outline-success" title="Edit User" data-bs-toggle="tooltip">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('superadmin.users.destroy', $user) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Delete User" data-bs-toggle="tooltip" onclick="return confirm('Are you sure you want to delete this user?')">
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
                                <i class="bi bi-person-x text-muted" style="font-size: 4rem;"></i>
                                <h5 class="text-muted mt-3">No users found</h5>
                                <p class="text-muted">There are no users in the system yet.</p>
                                <a href="{{ route('superadmin.users.create') }}" class="btn btn-primary mt-2">
                                    <i class="bi bi-plus-circle me-1"></i> Add First User
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div class="text-muted">
                Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} entries
            </div>
            <nav>
                {{ $users->links('pagination::bootstrap-5') }}
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
        /* border-radius: 0.5rem; */
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
    
    .user-row:hover {
        background-color: #f8f9fc;
        transform: translateY(-1px);
        transition: all 0.2s ease;
    }
    
    .avatar img {
        object-fit: cover;
        border: 2px solid #e3e6f0;
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
    
    .form-control:focus {
        box-shadow: none;
        border-color: #4e73df;
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
    const searchInput = document.getElementById('searchInput');
    const roleFilter = document.getElementById('roleFilter');
    const statusFilter = document.getElementById('statusFilter');
    const userRows = document.querySelectorAll('.user-row');

    function filterUsers() {
        const searchTerm = searchInput.value.toLowerCase();
        const roleValue = roleFilter.value;
        const statusValue = statusFilter.value;

        userRows.forEach(row => {
            const text = row.textContent.toLowerCase();
            const rowRole = row.getAttribute('data-role');
            const rowStatus = row.getAttribute('data-status');

            const matchesSearch = searchTerm === '' || text.includes(searchTerm);
            const matchesRole = roleValue === '' || rowRole === roleValue;
            const matchesStatus = statusValue === '' || rowStatus === statusValue;

            row.style.display = (matchesSearch && matchesRole && matchesStatus) ? '' : 'none';
        });
    }

    // Event listeners
    searchInput.addEventListener('input', filterUsers);
    roleFilter.addEventListener('change', filterUsers);
    statusFilter.addEventListener('change', filterUsers);

    // Search button
    document.getElementById('searchButton').addEventListener('click', filterUsers);

    // Clear filters on escape key
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            this.value = '';
            filterUsers();
        }
    });

    // Add loading state to search
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
});
</script>
@endpush