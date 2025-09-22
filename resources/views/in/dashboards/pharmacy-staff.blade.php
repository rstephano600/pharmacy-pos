@extends('layouts.app')

@section('title', 'Pharmacy Staff Dashboard')
@section('header', 'Pharmacy Management System')

@section('header-button')
    <div class="btn-group">
        <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
            <i class="bi bi-lightning me-1"></i>Quick Actions
        </button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#"><i class="bi bi-cart-plus me-2"></i>New Sale</a></li>
            <li><a class="dropdown-item" href="#"><i class="bi bi-prescription me-2"></i>New Prescription</a></li>
            <li><a class="dropdown-item" href="#"><i class="bi bi-inventory me-2"></i>Check Stock</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="#"><i class="bi bi-person-plus me-2"></i>Add Patient</a></li>
        </ul>
    </div>
@endsection

@section('content')
<!-- Pharmacy Header -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center">
                    <div class="me-4">
                        <div class="avatar-lg">
                            <img src="{{ asset('storage/' . $pharmacy->pharmacy_logo) }}" 
                                 alt="{{ $pharmacy->name }}" 
                                 class="rounded-circle"
                                 width="80"
                                 height="80"
                                 onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($pharmacy->name) }}&background=4e73df&color=fff&size=80'">
                        </div>
                    </div>
                    <div>
                        <h2 class="mb-1">{{ $pharmacy->name }}</h2>
                        <p class="text-muted mb-1">
                            <i class="bi bi-geo-alt me-1"></i>{{ $pharmacy->location }}
                        </p>
                        <div class="d-flex gap-2 flex-wrap">
                            <span class="badge bg-success">
                                <i class="bi bi-check-circle me-1"></i>Active
                            </span>
                            <span class="badge bg-info">
                                <i class="bi bi-person-badge me-1"></i>{{ ucfirst(str_replace('_', ' ', Auth::user()->role)) }}
                            </span>
                            <span class="badge bg-primary">
                                <i class="bi bi-clock me-1"></i>{{ now()->format('h:i A') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-end">
                <div class="btn-group">
                    <a href="#" class="btn btn-outline-primary">
                        <i class="bi bi-activity me-1"></i>Today: {{ $todayStats['prescriptions'] }} Rx
                    </a>
                    <a href="#" class="btn btn-outline-success">
                        <i class="bi bi-cash-coin me-1"></i>Sales
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Stats -->
<div class="row mb-4" style="display:none;">
    <div class="col-xl-2 col-md-4 col-6 mb-3">
        <div class="card border-start border-primary border-4 h-100">
            <div class="card-body text-center">
                <i class="bi bi-prescription2 text-primary display-6 d-block mb-2"></i>
                <div class="text-xs text-uppercase text-primary fw-bold">Today's Rx</div>
                <div class="h4 mb-0 fw-bold">{{ $todayStats['prescriptions'] }}</div>
                <div class="small text-muted">{{ $todayStats['pending'] }} pending</div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6 mb-3">
        <div class="card border-start border-success border-4 h-100">
            <div class="card-body text-center">
                <i class="bi bi-cash-coin text-success display-6 d-block mb-2"></i>
                <div class="text-xs text-uppercase text-success fw-bold">Sales</div>
                <div class="h4 mb-0 fw-bold">${{ number_format($todayStats['sales'] ?? 0) }}</div>
                <div class="small text-muted">Today</div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6 mb-3">
        <div class="card border-start border-warning border-4 h-100">
            <div class="card-body text-center">
                <i class="bi bi-people text-warning display-6 d-block mb-2"></i>
                <div class="text-xs text-uppercase text-warning fw-bold">Patients</div>
                <div class="h4 mb-0 fw-bold">{{ $todayStats['patients'] }}</div>
                <div class="small text-muted">Served today</div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6 mb-3">
        <div class="card border-start border-danger border-4 h-100">
            <div class="card-body text-center">
                <i class="bi bi-exclamation-triangle text-danger display-6 d-block mb-2"></i>
                <div class="text-xs text-uppercase text-danger fw-bold">Low Stock</div>
                <div class="h4 mb-0 fw-bold"> </div>
                <div class="small text-muted">Items</div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6 mb-3">
        <div class="card border-start border-info border-4 h-100">
            <div class="card-body text-center">
                <i class="bi bi-cart-check text-info display-6 d-block mb-2"></i>
                <div class="text-xs text-uppercase text-info fw-bold">Orders</div>
                <div class="h4 mb-0 fw-bold">{{ $todayStats['completed'] }}</div>
                <div class="small text-muted">Completed</div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6 mb-3">
        <div class="card border-start border-secondary border-4 h-100">
            <div class="card-body text-center">
                <i class="bi bi-clock-history text-secondary display-6 d-block mb-2"></i>
                <div class="text-xs text-uppercase text-secondary fw-bold">Avg. Time</div>
                <div class="h4 mb-0 fw-bold">8.2min</div>
                <div class="small text-muted">Per customer</div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Main Content Area -->
    <div class="col-lg-8">
        <!-- Quick Access Grid -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-grid-3x3 me-2"></i>Quick Access
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Sales Management -->
                    <div class="col-md-3 col-6 mb-3">
                        <a href="#" class="btn btn-outline-primary w-100 h-100 py-3">
                            <i class="bi bi-cash-coin display-6 d-block mb-2"></i>
                            <span>Sales</span>
                            <small class="text-muted d-block">Management</small>
                        </a>
                    </div>

                    <!-- Medicine Categories -->
                    <div class="col-md-3 col-6 mb-3">
                        <a href="{{ route('medicine-categories.index') }}" class="btn btn-outline-info w-100 h-100 py-3">
                            <i class="bi bi-tags display-6 d-block mb-2"></i>
                            <span>Categories</span>
                            <small class="text-muted d-block">Medicines</small>
                        </a>
                    </div>

                    <!-- Medicines -->
                    <div class="col-md-3 col-6 mb-3">
                        <a href="{{ route('medicines.index') }}" class="btn btn-outline-success w-100 h-100 py-3">
                            <i class="bi bi-capsule display-6 d-block mb-2"></i>
                            <span>Medicines</span>
                            <small class="text-muted d-block">Inventory</small>
                        </a>
                    </div>

                    <!-- Inventory Management -->
                    <div class="col-md-3 col-6 mb-3">
                        <a href="#" class="btn btn-outline-warning w-100 h-100 py-3">
                            <i class="bi bi-inventory display-6 d-block mb-2"></i>
                            <span>Inventory</span>
                            <small class="text-muted d-block">Management</small>
                        </a>
                    </div>

                    <!-- Purchases -->
                    <div class="col-md-3 col-6 mb-3">
                        <a href="#" class="btn btn-outline-secondary w-100 h-100 py-3">
                            <i class="bi bi-cart-plus display-6 d-block mb-2"></i>
                            <span>Purchases</span>
                            <small class="text-muted d-block">Orders</small>
                        </a>
                    </div>

                    <!-- Suppliers -->
                    <div class="col-md-3 col-6 mb-3">
                        <a href="#" class="btn btn-outline-dark w-100 h-100 py-3">
                            <i class="bi bi-truck display-6 d-block mb-2"></i>
                            <span>Suppliers</span>
                            <small class="text-muted d-block">Vendors</small>
                        </a>
                    </div>

                    <!-- Customers/Patients -->
                    <div class="col-md-3 col-6 mb-3">
                        <a href="#" class="btn btn-outline-primary w-100 h-100 py-3">
                            <i class="bi bi-people display-6 d-block mb-2"></i>
                            <span>Patients</span>
                            <small class="text-muted d-block">Customers</small>
                        </a>
                    </div>

                    <!-- Reports -->
                    <div class="col-md-3 col-6 mb-3">
                        <a href="#" class="btn btn-outline-info w-100 h-100 py-3">
                            <i class="bi bi-graph-up display-6 d-block mb-2"></i>
                            <span>Reports</span>
                            <small class="text-muted d-block">Analytics</small>
                        </a>
                    </div>

                    <!-- Returns -->
                    <div class="col-md-3 col-6 mb-3">
                        <a href="#" class="btn btn-outline-danger w-100 h-100 py-3">
                            <i class="bi bi-arrow-return-left display-6 d-block mb-2"></i>
                            <span>Returns</span>
                            <small class="text-muted d-block">Refunds</small>
                        </a>
                    </div>

                    <!-- Alerts -->
                    <div class="col-md-3 col-6 mb-3">
                        <a href="#" class="btn btn-outline-warning w-100 h-100 py-3">
                            <i class="bi bi-bell display-6 d-block mb-2"></i>
                            <span>Alerts</span>
                            <small class="text-muted d-block">Notifications</small>
                        </a>
                    </div>

                    <!-- Settings -->
                    <div class="col-md-3 col-6 mb-3">
                        <a href="#" class="btn btn-outline-secondary w-100 h-100 py-3">
                            <i class="bi bi-gear display-6 d-block mb-2"></i>
                            <span>Settings</span>
                            <small class="text-muted d-block">Configuration</small>
                        </a>
                    </div>

                    <!-- Additional Functions -->
                    <div class="col-md-3 col-6 mb-3">
                        <a href="#" class="btn btn-outline-success w-100 h-100 py-3">
                            <i class="bi bi-prescription display-6 d-block mb-2"></i>
                            <span>Prescriptions</span>
                            <small class="text-muted d-block">Manage Rx</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity & Alerts -->
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="bi bi-activity me-2"></i>Recent Activity
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-badge bg-success"></div>
                                <div class="timeline-content">
                                    <span>New sale completed</span>
                                    <small class="text-muted">2 min ago</small>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-badge bg-info"></div>
                                <div class="timeline-content">
                                    <span>Prescription filled</span>
                                    <small class="text-muted">15 min ago</small>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-badge bg-warning"></div>
                                <div class="timeline-content">
                                    <span>Low stock alert</span>
                                    <small class="text-muted">30 min ago</small>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-badge bg-primary"></div>
                                <div class="timeline-content">
                                    <span>New patient registered</span>
                                    <small class="text-muted">1 hour ago</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-danger">
                            <i class="bi bi-exclamation-triangle me-2"></i>Priority Alerts
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning mb-2">
                            <i class="bi bi-capsule me-2"></i>
                            <strong>Low Stock:</strong> Amoxicillin (5 units left)
                        </div>
                        <div class="alert alert-danger mb-2">
                            <i class="bi bi-clock me-2"></i>
                            <strong>Expiring Soon:</strong> Insulin vials (3 days)
                        </div>
                        <div class="alert alert-info mb-2">
                            <i class="bi bi-prescription me-2"></i>
                            <strong>Pending Rx:</strong> 3 prescriptions waiting
                        </div>
                        <div class="alert alert-success mb-0">
                            <i class="bi bi-check-circle me-2"></i>
                            <strong>System:</strong> All systems operational
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar - Quick Tools -->
    <div class="col-lg-4">
        <!-- Quick Actions -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-lightning me-2"></i>Quick Tools
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="#" class="btn btn-primary">
                        <i class="bi bi-cart-plus me-2"></i>New Sale
                    </a>
                    <a href="#" class="btn btn-success">
                        <i class="bi bi-prescription me-2"></i>New Prescription
                    </a>
                    <a href="#" class="btn btn-info">
                        <i class="bi bi-search me-2"></i>Find Medicine
                    </a>
                    <a href="#" class="btn btn-warning">
                        <i class="bi bi-person-plus me-2"></i>Add Patient
                    </a>
                </div>
            </div>
        </div>

        <!-- Inventory Status -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-inventory me-2"></i>Inventory Overview
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Total Medicines</span>
                        <span>1,245</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: 85%"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Low Stock Items</span>
                        <span>12</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: 15%"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Out of Stock</span>
                        <span>3</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-danger" role="progressbar" style="width: 5%"></div>
                    </div>
                </div>
                <div class="text-center mt-3">
                    <a href="#" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-arrow-right me-1"></i>View Inventory
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Patients -->
        <div class="card">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-people me-2"></i>Recent Patients
                </h6>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>John Doe</strong>
                                <div class="text-muted small">Prescription: Amoxicillin</div>
                            </div>
                            <span class="badge bg-success">Completed</span>
                        </div>
                    </div>
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Jane Smith</strong>
                                <div class="text-muted small">Prescription: Insulin</div>
                            </div>
                            <span class="badge bg-warning">Pending</span>
                        </div>
                    </div>
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Mike Johnson</strong>
                                <div class="text-muted small">OTC: Pain relievers</div>
                            </div>
                            <span class="badge bg-success">Completed</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- System Status Footer -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center">
                <div class="row">
                    <div class="col-md-3">
                        <i class="bi bi-cpu text-primary"></i>
                        <div class="small">System Status</div>
                        <span class="badge bg-success">Online</span>
                    </div>
                    <div class="col-md-3">
                        <i class="bi bi-database text-info"></i>
                        <div class="small">Database</div>
                        <span class="badge bg-success">Active</span>
                    </div>
                    <div class="col-md-3">
                        <i class="bi bi-shield-check text-success"></i>
                        <div class="small">Security</div>
                        <span class="badge bg-success">Secure</span>
                    </div>
                    <div class="col-md-3">
                        <i class="bi bi-clock-history text-warning"></i>
                        <div class="small">Uptime</div>
                        <span class="badge bg-info">99.9%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .avatar-lg {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        overflow: hidden;
        border: 3px solid #4e73df;
    }
    
    .avatar-lg img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .border-start {
        border-left-width: 4px !important;
    }
    
    .card {
        border: none;
        border-radius: 0.5rem;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }
    
    .btn-outline-primary:hover,
    .btn-outline-success:hover,
    .btn-outline-info:hover,
    .btn-outline-warning:hover,
    .btn-outline-danger:hover {
        transform: translateY(-2px);
        transition: all 0.3s ease;
    }
    
    .timeline {
        position: relative;
        padding-left: 2rem;
    }
    
    .timeline-item {
        position: relative;
        padding-bottom: 1rem;
        border-left: 2px solid #e3e6f0;
        padding-left: 1rem;
        margin-left: 1rem;
    }
    
    .timeline-badge {
        position: absolute;
        left: -1.5rem;
        top: 0;
        width: 1rem;
        height: 1rem;
        border-radius: 50%;
        border: 2px solid white;
    }
    
    .timeline-content {
        margin-left: 0.5rem;
    }
    
    .progress {
        border-radius: 10px;
    }
    
    .list-group-item {
        border: none;
        border-bottom: 1px solid #e3e6f0;
        padding: 0.75rem 0;
    }
    
    .list-group-item:last-child {
        border-bottom: none;
    }
    
    .display-6 {
        font-size: 2rem;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animate progress bars
    const progressBars = document.querySelectorAll('.progress-bar');
    progressBars.forEach(bar => {
        const width = bar.style.width;
        bar.style.width = '0';
        setTimeout(() => {
            bar.style.transition = 'width 1s ease-in-out';
            bar.style.width = width;
        }, 500);
    });
    
    // Add hover effects to cards
    const cards = document.querySelectorAll('.card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', () => {
            card.style.transform = 'translateY(-2px)';
            card.style.boxShadow = '0 0.5rem 2rem 0 rgba(58, 59, 69, 0.2)';
        });
        
        card.addEventListener('mouseleave', () => {
            card.style.transform = 'translateY(0)';
            card.style.boxShadow = '0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15)';
        });
    });
    
    // Quick actions animation
    const quickActions = document.querySelectorAll('.btn-outline-primary, .btn-outline-success, .btn-outline-info, .btn-outline-warning');
    quickActions.forEach(btn => {
        btn.addEventListener('mouseenter', () => {
            btn.style.transform = 'scale(1.05)';
        });
        
        btn.addEventListener('mouseleave', () => {
            btn.style.transform = 'scale(1)';
        });
    });
});
</script>
@endpush