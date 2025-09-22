@extends('layouts.app')

@section('title', 'Create New Pharmacy')
@section('header', 'Create New Pharmacy')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10 col-lg-8">
        <div class="card shadow">
            <div class="card-header bg-success text-white py-3">
                <h5 class="card-title mb-0">
                    <i class="bi bi-plus-circle me-2"></i>Setup Your Pharmacy
                </h5>
            </div>
            <div class="card-body p-4">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Please fix the following errors:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Important:</strong> You are creating a new pharmacy. As the owner, you'll have full 
                    administrative control over this pharmacy and its staff.
                </div>

                <form method="POST" action="{{ route('pharmacy.create') }}" id="pharmacySetupForm">
                    @csrf

                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-2 mb-3">
                                <i class="bi bi-building me-2"></i>Basic Information
                            </h6>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Pharmacy Name *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       name="name" value="{{ old('name') }}" 
                                       placeholder="Enter pharmacy name" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">License Number *</label>
                                <input type="text" class="form-control @error('license_number') is-invalid @enderror" 
                                       name="license_number" value="{{ old('license_number') }}" 
                                       placeholder="Enter license number" required>
                                @error('license_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">License Expiry Date *</label>
                                <input type="date" class="form-control @error('license_expiry') is-invalid @enderror" 
                                       name="license_expiry" value="{{ old('license_expiry') }}" 
                                       min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                                @error('license_expiry')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Location Information -->
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-2 mb-3">
                                <i class="bi bi-geo-alt me-2"></i>Location Information
                            </h6>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Country *</label>
                                <input type="text" class="form-control @error('country') is-invalid @enderror" 
                                       name="country" value="{{ old('country') }}" 
                                       placeholder="Enter country" required>
                                @error('country')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Region/State *</label>
                                <input type="text" class="form-control @error('region') is-invalid @enderror" 
                                       name="region" value="{{ old('region') }}" 
                                       placeholder="Enter region or state" required>
                                @error('region')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">District/City *</label>
                                <input type="text" class="form-control @error('district') is-invalid @enderror" 
                                       name="district" value="{{ old('district') }}" 
                                       placeholder="Enter district or city" required>
                                @error('district')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Full Address *</label>
                                <textarea class="form-control @error('location') is-invalid @enderror" 
                                          name="location" rows="2" 
                                          placeholder="Enter complete address" required>{{ old('location') }}</textarea>
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <!-- Contact Information -->
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-2 mb-3">
                                <i class="bi bi-telephone me-2"></i>Contact Information
                            </h6>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Phone Number *</label>
                                <input type="tel" class="form-control @error('contact_phone') is-invalid @enderror" 
                                       name="contact_phone" value="{{ old('contact_phone') }}" 
                                       placeholder="Enter phone number" required>
                                @error('contact_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Email Address</label>
                                <input type="email" class="form-control @error('contact_email') is-invalid @enderror" 
                                       name="contact_email" value="{{ old('contact_email') }}" 
                                       placeholder="Enter email address">
                                @error('contact_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-2 mb-3">
                                <i class="bi bi-clock me-2"></i>Additional Information
                            </h6>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Working Hours</label>
                                <input type="text" class="form-control @error('working_hours') is-invalid @enderror" 
                                       name="working_hours" value="{{ old('working_hours') }}" 
                                       placeholder="e.g., Mon-Fri: 8AM-10PM, Sat-Sun: 9AM-8PM">
                                @error('working_hours')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Example: Mon-Fri: 8:00 AM - 10:00 PM, Sat-Sun: 9:00 AM - 8:00 PM</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Access Code (Optional)</label>
                                <input type="text" class="form-control" 
                                       name="access_code" 
                                       placeholder="Create access code for staff joining">
                                <div class="form-text">Optional code that staff will need to join this pharmacy</div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="alert alert-warning">
                        <i class="bi bi-shield-exclamation me-2"></i>
                        <strong>Verification:</strong> By creating this pharmacy, you confirm that all information 
                        provided is accurate and you are authorized to operate this pharmacy.
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('pharmacy.association') }}" class="btn btn-outline-secondary me-md-2">
                            <i class="bi bi-arrow-left me-2"></i>Back to Join Pharmacy
                        </a>
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="bi bi-check-circle me-2"></i>Create Pharmacy
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .form-label {
        color: #4e73df;
    }
    
    .card-header {
        border-radius: 0.5rem 0.5rem 0 0 !important;
    }
    
    .border-bottom {
        border-color: #4e73df !important;
    }
    
    input:focus, textarea:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const form = document.getElementById('pharmacySetupForm');
    
    form.addEventListener('submit', function(e) {
        const requiredFields = form.querySelectorAll('[required]');
        let valid = true;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                valid = false;
                field.classList.add('is-invalid');
            }
        });
        
        if (!valid) {
            e.preventDefault();
            // Scroll to first error
            const firstError = form.querySelector('.is-invalid');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
    });
    
    // Real-time validation
    const inputs = form.querySelectorAll('input, textarea');
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            if (this.value.trim()) {
                this.classList.remove('is-invalid');
            }
        });
    });
    
    // Set minimum date for license expiry
    const licenseExpiry = document.querySelector('input[name="license_expiry"]');
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    licenseExpiry.min = tomorrow.toISOString().split('T')[0];
});
</script>
@endpush