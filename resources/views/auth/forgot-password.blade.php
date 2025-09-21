@extends('layouts.auth-app')

@section('content')
<div class="auth-card card">
    <div class="auth-header card-header text-center py-4">
        <h3 class="m-0"><i class="bi bi-key me-2"></i>Reset Password</h3>
        <p class="m-0 opacity-75">Enter your email to receive reset instructions</p>
    </div>
    <div class="card-body p-5">
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="mb-4">
                <label for="email" class="form-label">Email Address *</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                       id="email" name="email" value="{{ old('email') }}" required autofocus>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 mb-3">
                <i class="bi bi-envelope me-2"></i>Send Password Reset Link
            </button>

            <div class="text-center">
                <p class="mb-0">
                    <a href="{{ route('login') }}" class="text-decoration-none">
                        <i class="bi bi-arrow-left me-1"></i>Back to Login
                    </a>
                </p>
            </div>
        </form>
    </div>
</div>
@endsection