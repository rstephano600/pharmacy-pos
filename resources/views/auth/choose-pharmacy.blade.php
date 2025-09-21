{{-- resources/views/auth/choose-pharmacy.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg rounded-3">
                <div class="card-header bg-primary text-white text-center">
                    <h4>Choose Your Pharmacy</h4>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4 text-center">
                        You are associated with multiple pharmacies. Please select one to continue.
                    </p>

                    @if(session('pharmacy_choices') && count(session('pharmacy_choices')) > 0)
                        <form action="{{ route('auth.set.pharmacy') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="pharmacy_id" class="form-label">Select Pharmacy</label>
                                <select class="form-select @error('pharmacy_id') is-invalid @enderror" id="pharmacy_id" name="pharmacy_id" required>
                                    <option value="" selected disabled>-- Choose Pharmacy --</option>
                                    @foreach(session('pharmacy_choices') as $pharmacy)
                                        <option value="{{ $pharmacy->id }}">{{ $pharmacy->name }}</option>
                                    @endforeach
                                </select>
                                @error('pharmacy_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    Continue
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="alert alert-warning text-center">
                            No pharmacies found. Please contact your administrator.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
