@extends('layouts.app')

@section('title', 'My Pharmacy Dashboard')
@section('header', 'My Pharmacy Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-body text-center">
                <i class="bi bi-building display-1 text-primary mb-3"></i>
                <h3>Welcome to {{ $pharmacy->name }}!</h3>
                <p class="text-muted">You're associated with this pharmacy as a <strong>{{ Auth::user()->role }}</strong></p>
                
                <div class="row mt-4">
                    <div class="col-md-4">
                        <div class="border rounded p-3">
                            <i class="bi bi-geo-alt text-primary mb-2" style="font-size: 1.5rem;"></i>
                            <h6>Location</h6>
                            <p class="text-muted mb-0 small">{{ $pharmacy->location }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3">
                            <i class="bi bi-telephone text-success mb-2" style="font-size: 1.5rem;"></i>
                            <h6>Contact</h6>
                            <p class="text-muted mb-0 small">{{ $pharmacy->contact_phone }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3">
                            <i class="bi bi-clock text-info mb-2" style="font-size: 1.5rem;"></i>
                            <h6>Working Hours</h6>
                            <p class="text-muted mb-0 small">{{ $pharmacy->working_hours ?? 'Not specified' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Pharmacy Services -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">Pharmacy Services</h6>
            </div>
            <div class="card-body">
                <div class="list-group">
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="bi bi-prescription2 me-2 text-primary"></i>
                        Prescription Services
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="bi bi-capsule me-2 text-success"></i>
                        Medication Dispensing
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="bi bi-heart-pulse me-2 text-danger"></i>
                        Health Consultations
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="bi bi-cart me-2 text-warning"></i>
                        Over-the-Counter Products
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="#" class="btn btn-outline-primary btn-sm text-start">
                        <i class="bi bi-prescription me-2"></i> View Prescriptions
                    </a>
                    <a href="#" class="btn btn-outline-success btn-sm text-start">
                        <i class="bi bi-capsule me-2"></i> Browse Inventory
                    </a>
                    <a href="#" class="btn btn-outline-info btn-sm text-start">
                        <i class="bi bi-calendar me-2"></i> Schedule Appointment
                    </a>
                    <a href="#" class="btn btn-outline-warning btn-sm text-start">
                        <i class="bi bi-telephone me-2"></i> Contact Pharmacy
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection