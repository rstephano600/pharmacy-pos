@extends('layouts.app')

@section('title', 'Select Pharmacy')
@section('header', 'Choose Pharmacy')

@section('content')
<div class="row justify-content-center mt-5">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">Select a Pharmacy to Continue</h5>
            </div>
            <div class="card-body">
                @if(session('info'))
                    <div class="alert alert-info">{{ session('info') }}</div>
                @endif

                <form action="{{ route('auth.choose.pharmacy.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="pharmacy_id" class="form-label">Pharmacy</label>
                        <select name="pharmacy_id" id="pharmacy_id" class="form-select @error('pharmacy_id') is-invalid @enderror" required>
                            <option value="">-- Select Pharmacy --</option>
                            @foreach($pharmacies as $pharmacy)
                                <option value="{{ $pharmacy->id }}">{{ $pharmacy->name }} ({{ $pharmacy->district }}, {{ $pharmacy->region }})</option>
                            @endforeach
                        </select>
                        @error('pharmacy_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-check-circle me-1"></i> Continue
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
