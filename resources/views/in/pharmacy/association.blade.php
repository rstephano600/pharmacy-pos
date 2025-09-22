@extends('layouts.app')

@section('title', 'Join a Pharmacy')
@section('header', 'Join a Pharmacy')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow">
            <div class="card-header bg-primary text-white py-3">
                <h5 class="card-title mb-0">
                    <i class="bi bi-building me-2"></i>Join a Pharmacy
                </h5>
            </div>
            <div class="card-body p-4">
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="bi bi-exclamation-triangle me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>How it works:</strong> Select a pharmacy from the list below to request association. 
                    You may need an access code provided by the pharmacy owner.
                </div>

                @if($pharmacies->isEmpty())
                    <div class="text-center py-5">
                        <i class="bi bi-building-x text-muted" style="font-size: 3rem;"></i>
                        <h4 class="text-muted mt-3">No Pharmacies Available</h4>
                        <p class="text-muted">There are no active pharmacies available for association at the moment.</p>
                        <a href="{{ route('pharmacy.setup') }}" class="btn btn-primary mt-2">
                            <i class="bi bi-plus-circle me-1"></i> Create New Pharmacy
                        </a>
                    </div>
                @else
                    <form method="POST" action="{{ route('pharmacy.associate') }}">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label fw-bold">Select Pharmacy</label>
                            <div class="list-group">
                                @foreach($pharmacies as $pharmacy)
                                    <div class="list-group-item pharmacy-item">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" 
                                                   name="pharmacy_id" 
                                                   id="pharmacy_{{ $pharmacy->id }}" 
                                                   value="{{ $pharmacy->id }}"
                                                   required>
                                            <label class="form-check-label w-100" for="pharmacy_{{ $pharmacy->id }}">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <h6 class="mb-1">{{ $pharmacy->name }}</h6>
                                                        <p class="mb-1 text-muted small">
                                                            <i class="bi bi-geo-alt me-1"></i>{{ $pharmacy->location }}
                                                        </p>
                                                        <p class="mb-0 text-muted small">
                                                            <i class="bi bi-telephone me-1"></i>{{ $pharmacy->contact_phone }}
                                                        </p>
                                                    </div>
                                                    <span class="badge bg-success">Active</span>
                                                </div>
                                            </label>
                                        </div>

                                        <!-- Access Code Field (shown when pharmacy is selected) -->
                                        <div class="access-code-field mt-2" style="display: none;">
                                            <div class="alert alert-warning mb-2">
                                                <i class="bi bi-shield-lock me-2"></i>
                                                This pharmacy requires an access code
                                            </div>
                                            <input type="text" 
                                                   class="form-control form-control-sm" 
                                                   name="access_code" 
                                                   placeholder="Enter access code provided by pharmacy owner"
                                                   data-pharmacy-id="{{ $pharmacy->id }}">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('pharmacy_id')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-link-45deg me-2"></i>Join Pharmacy
                            </button>
                        </div>
                    </form>
                @endif

                <hr class="my-4">

                <div class="text-center">
                    <p class="text-muted mb-2">Don't see your pharmacy?</p>
                    <a href="{{ route('pharmacy.setup') }}" class="btn btn-outline-primary">
                        <i class="bi bi-plus-circle me-2"></i>Create New Pharmacy
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .pharmacy-item {
        border: 1px solid #e3e6f0;
        border-radius: 0.5rem;
        margin-bottom: 0.5rem;
        transition: all 0.3s ease;
    }
    
    .pharmacy-item:hover {
        border-color: #4e73df;
        box-shadow: 0 2px 8px rgba(78, 115, 223, 0.15);
    }
    
    .form-check-input:checked ~ .form-check-label {
        color: #4e73df;
        font-weight: 600;
    }
    
    .pharmacy-item:has(.form-check-input:checked) {
        border-color: #4e73df;
        background-color: rgba(78, 115, 223, 0.05);
    }
    
    .access-code-field {
        border-top: 1px dashed #e3e6f0;
        padding-top: 0.75rem;
    }
    
    .list-group-item {
        padding: 1rem;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Show access code field when pharmacy is selected
    const pharmacyRadios = document.querySelectorAll('input[name="pharmacy_id"]');
    
    pharmacyRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            // Hide all access code fields
            document.querySelectorAll('.access-code-field').forEach(field => {
                field.style.display = 'none';
            });
            
            // Show access code field for selected pharmacy
            const selectedField = this.closest('.pharmacy-item').querySelector('.access-code-field');
            if (selectedField) {
                selectedField.style.display = 'block';
            }
        });
    });
    
    // Form validation
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const selectedPharmacy = document.querySelector('input[name="pharmacy_id"]:checked');
        if (!selectedPharmacy) {
            e.preventDefault();
            alert('Please select a pharmacy to join.');
            return false;
        }
    });
});
</script>
@endpush