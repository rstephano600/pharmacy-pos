@extends('layouts.app')

@section('title', 'Super Admin Dashboard')
@section('header', 'Super Admin Dashboard')

@section('content')
<div class="row">
    <!-- System Health Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-heart-pulse text-primary" style="font-size: 2rem;"></i>
                    </div>
                    <div>
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            System Health
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800 text-capitalize">{{ $stats['system_health']['status'] }}</div>
                        <div class="small text-muted">Uptime: {{ $stats['system_health']['uptime'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Pharmacies Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-building text-success" style="font-size: 2rem;"></i>
                    </div>
                    <div>
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
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
        <div class="card border-left-info h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-people text-info" style="font-size: 2rem;"></i>
                    </div>
                    <div>
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Total Users
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_users'] }}</div>
                        <div class="small text-muted">System Users</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Storage Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-hdd text-warning" style="font-size: 2rem;"></i>
                    </div>
                    <div>
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Storage
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['system_health']['storage'] }}</div>
                        <div class="small text-muted">Last Backup: {{ $stats['system_health']['last_backup'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- System Overview -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">System Overview</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-4 mb-3">
                        <div class="border rounded p-3">
                            <i class="bi bi-database text-primary mb-2" style="font-size: 1.5rem;"></i>
                            <h4>25.4GB</h4>
                            <p class="text-muted mb-0">Database Size</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="border rounded p-3">
                            <i class="bi bi-clock-history text-success mb-2" style="font-size: 1.5rem;"></i>
                            <h4>99.9%</h4>
                            <p class="text-muted mb-0">Uptime</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="border rounded p-3">
                            <i class="bi bi-shield-check text-warning mb-2" style="font-size: 1.5rem;"></i>
                            <h4>Secure</h4>
                            <p class="text-muted mb-0">System Status</p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <h6 class="font-weight-bold">Recent System Logs</h6>
                    <div class="list-group">
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <span>System backup completed</span>
                                <small class="text-muted">2 hours ago</small>
                            </div>
                        </div>
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <span>Security update applied</span>
                                <small class="text-muted">5 hours ago</small>
                            </div>
                        </div>
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <span>New pharmacy registered</span>
                                <small class="text-muted">8 hours ago</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">Super Admin Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.users') }}" class="btn btn-outline-primary btn-sm text-start">
                        <i class="bi bi-people me-2"></i> Manage Users
                    </a>
                    <a href="{{ route('admin.pharmacies') }}" class="btn btn-outline-success btn-sm text-start">
                        <i class="bi bi-building me-2"></i> Manage Pharmacies
                    </a>
                    <a href="#" class="btn btn-outline-info btn-sm text-start">
                        <i class="bi bi-gear me-2"></i> System Settings
                    </a>
                    <a href="#" class="btn btn-outline-warning btn-sm text-start">
                        <i class="bi bi-shield me-2"></i> Security Settings
                    </a>
                    <a href="#" class="btn btn-outline-danger btn-sm text-start">
                        <i class="bi bi-database me-2"></i> Backup System
                    </a>
                </div>
                
                <hr>
                
                <h6 class="font-weight-bold">System Information</h6>
                <div class="small">
                    <div class="d-flex justify-content-between">
                        <span>PHP Version:</span>
                        <span>{{ phpversion() }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Laravel Version:</span>
                        <span>{{ app()->version() }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Server:</span>
                        <span>{{ $_SERVER['SERVER_SOFTWARE'] ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Activity -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">Recent System Activity</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>User</th>
                                <th>Activity</th>
                                <th>IP Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>2 minutes ago</td>
                                <td>System</td>
                                <td>Database backup completed</td>
                                <td>127.0.0.1</td>
                            </tr>
                            <tr>
                                <td>15 minutes ago</td>
                                <td>john@example.com</td>
                                <td>Logged in</td>
                                <td>192.168.1.100</td>
                            </tr>
                            <tr>
                                <td>1 hour ago</td>
                                <td>System</td>
                                <td>Security scan completed</td>
                                <td>127.0.0.1</td>
                            </tr>
                            <tr>
                                <td>3 hours ago</td>
                                <td>mary@example.com</td>
                                <td>Created new pharmacy</td>
                                <td>10.0.0.50</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection