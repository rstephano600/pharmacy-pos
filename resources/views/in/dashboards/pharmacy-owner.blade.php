@extends('layouts.app')

@section('title', 'Pharmacy Owner Dashboard')
@section('header', 'Pharmacy Management Dashboard')

@section('content')
<div class="row">
    <!-- Revenue Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-currency-dollar text-primary" style="font-size: 2rem;"></i>
                    </div>
                    <div>
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Monthly Revenue
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($monthlyStats['revenue']) }}</div>
                        <div class="small text-muted">Current Month</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Prescriptions Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-prescription2 text-success" style="font-size: 2rem;"></i>
                    </div>
                    <div>
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Prescriptions
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $monthlyStats['prescriptions'] }}</div>
                        <div class="small text-muted">This Month</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Staff Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-people text-info" style="font-size: 2rem;"></i>
                    </div>
                    <div>
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Staff Members
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $staffCount }}</div>
                        <div class="small text-muted">Total Staff</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-inventory text-warning" style="font-size: 2rem;"></i>
                    </div>
                    <div>
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Inventory Value
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($monthlyStats['inventory_value']) }}</div>
                        <div class="small text-muted">Current Stock</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Pharmacy Management -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">Pharmacy Overview - {{ $pharmacy->name }}</h6>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6>Pharmacy Information</h6>
                        <p><strong>License:</strong> {{ $pharmacy->license_number }}</p>
                        <p><strong>Location:</strong> {{ $pharmacy->location }}</p>
                        <p><strong>Contact:</strong> {{ $pharmacy->contact_phone }}</p>
                        <p><strong>Status:</strong> 
                            <span class="badge bg-{{ $pharmacy->is_active ? 'success' : 'warning' }}">
                                {{ $pharmacy->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h6>Performance Metrics</h6>
                        <div class="progress mb-2">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 75%">
                                Prescription Fill Rate: 75%
                            </div>
                        </div>
                        <div class="progress mb-2">
                            <div class="progress-bar bg-info" role="progressbar" style="width: 60%">
                                Customer Satisfaction: 60%
                            </div>
                        </div>
                        <div class="progress mb-2">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: 85%">
                                Inventory Turnover: 85%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Owner Actions -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">Owner Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="#" class="btn btn-outline-primary btn-sm text-start">
                        <i class="bi bi-pencil me-2"></i> Edit Pharmacy Profile
                    </a>
                    <a href="#" class="btn btn-outline-success btn-sm text-start">
                        <i class="bi bi-people me-2"></i> Manage Staff
                    </a>
                    <a href="#" class="btn btn-outline-info btn-sm text-start">
                        <i class="bi bi-graph-up me-2"></i> View Reports
                    </a>
                    <a href="#" class="btn btn-outline-warning btn-sm text-start">
                        <i class="bi bi-inventory me-2"></i> Inventory Management
                    </a>
                    <a href="#" class="btn btn-outline-danger btn-sm text-start">
                        <i class="bi bi-gear me-2"></i> Pharmacy Settings
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection