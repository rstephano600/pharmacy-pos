@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('header', 'Admin Dashboard')

@section('content')
<div class="row">
    <!-- Total Pharmacies Card -->
    <div class="col-xl-3 col-md-6 mb-4">
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
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_pharmacies'] }}</div>
                        <div class="small text-muted">{{ $stats['active_pharmacies'] }} Active</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Users Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-people text-success" style="font-size: 2rem;"></i>
                    </div>
                    <div>
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Total Users
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_users'] }}</div>
                        <div class="small text-muted">Registered Users</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pharmacy Staff Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-person-badge text-info" style="font-size: 2rem;"></i>
                    </div>
                    <div>
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Pharmacy Staff
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pharmacy_staff'] }}</div>
                        <div class="small text-muted">Active Staff</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Pharmacies Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-clock-history text-warning" style="font-size: 2rem;"></i>
                    </div>
                    <div>
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Pending Approval
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending_pharmacies'] }}</div>
                        <div class="small text-muted">Pharmacies</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Pharmacies -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">Recent Pharmacies</h6>
            </div>
            <div class="card-body">
                <div class="list-group">
                    @foreach($recentPharmacies as $pharmacy)
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">{{ $pharmacy->name }}</h6>
                                <small class="text-muted">{{ $pharmacy->license_number }}</small>
                            </div>
                            <span class="badge bg-{{ $pharmacy->is_active ? 'success' : 'warning' }}">
                                {{ $pharmacy->is_active ? 'Active' : 'Pending' }}
                            </span>
                        </div>
                        <small class="text-muted">{{ $pharmacy->location }}</small>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Users -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">Recent Users</h6>
            </div>
            <div class="card-body">
                <div class="list-group">
                    @foreach($recentUsers as $user)
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">{{ $user->full_name }}</h6>
                                <small class="text-muted">{{ $user->email }}</small>
                            </div>
                            <span class="badge bg-info">{{ $user->role }}</span>
                        </div>
                        <small class="text-muted">Registered: {{ $user->created_at->diffForHumans() }}</small>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Quick Actions -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">Admin Actions</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 col-6 mb-3">
                        <a href="{{ route('admin.users') }}" class="btn btn-outline-primary w-100">
                            <i class="bi bi-people me-1"></i> Manage Users
                        </a>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <a href="{{ route('admin.pharmacies') }}" class="btn btn-outline-success w-100">
                            <i class="bi bi-building me-1"></i> Manage Pharmacies
                        </a>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <a href="{{ route('admin.reports') }}" class="btn btn-outline-info w-100">
                            <i class="bi bi-graph-up me-1"></i> View Reports
                        </a>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <a href="#" class="btn btn-outline-warning w-100">
                            <i class="bi bi-gear me-1"></i> Settings
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection