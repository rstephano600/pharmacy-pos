@extends('layouts.app')

@section('title', 'Edit User - Super Admin')
@section('header', 'Edit User')

@section('content')
<div class="card shadow mb-4">
    <div class="card-body">
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Whoops!</strong> Please fix the errors below:
                <ul>
                    @foreach ($errors->all() as $error)
                       <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('superadmin.users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                {{-- Full Name --}}
                <div class="col-md-6 mb-3">
                    <label for="full_name" class="form-label">Full Name</label>
                    <input type="text" name="full_name" id="full_name"
                           class="form-control @error('full_name') is-invalid @enderror"
                           value="{{ old('full_name', $user->full_name) }}">
                    @error('full_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Username --}}
                <div class="col-md-6 mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" name="username" id="username"
                           class="form-control @error('username') is-invalid @enderror"
                           value="{{ old('username', $user->username) }}">
                    @error('username')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email"
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email', $user->email) }}">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Phone --}}
                <div class="col-md-6 mb-3">
                    <label for="phone" class="form-label">Phone</label>
                    <input type="text" name="phone" id="phone"
                           class="form-control @error('phone') is-invalid @enderror"
                           value="{{ old('phone', $user->phone) }}">
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Role --}}
                <div class="col-md-6 mb-3">
                    <label for="role" class="form-label">Role</label>
                    <select name="role" id="role"
                            class="form-select @error('role') is-invalid @enderror">
                        <option value="">Select Role</option>
                        @foreach(App\Models\User::getRoles() as $role)
                            <option value="{{ $role }}" 
                                {{ old('role', $user->role) == $role ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_',' ', $role)) }}
                            </option>
                        @endforeach
                    </select>
                    @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Pharmacies (Multi-select) --}}
                <div class="col-md-6 mb-3">
                    <label for="pharmacies" class="form-label">Pharmacies</label>
                    <select name="pharmacies[]" id="pharmacies"
                            class="form-select @error('pharmacies') is-invalid @enderror"
                            multiple>
                        @foreach($pharmacies as $pharmacy)
                            <option value="{{ $pharmacy->id }}"
                                {{ in_array($pharmacy->id, old('pharmacies', $user->pharmacies->pluck('id')->toArray())) ? 'selected' : '' }}>
                                {{ $pharmacy->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('pharmacies')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Is Active --}}
                <div class="col-md-12 mb-3 form-check">
                    <input class="form-check-input" type="checkbox" name="is_active"
                           id="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>

            </div>

            <button type="submit" class="btn btn-success"><i class="bi bi-save me-1"></i> Update User</button>
        </form>
    </div>
</div>
@endsection
