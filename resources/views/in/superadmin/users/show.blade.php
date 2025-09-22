@extends('layouts.app')

@section('title', 'View User - Super Admin')
@section('header', 'User Details')

@section('content')
<div class="card shadow">
    <div class="card-body">
        <div class="mb-3">
            <strong>Full Name:</strong> {{ $user->full_name }}
        </div>
        <div class="mb-3">
            <strong>Username:</strong> {{ $user->username }}
        </div>
        <div class="mb-3">
            <strong>Email:</strong> {{ $user->email }}
        </div>
        <div class="mb-3">
            <strong>Phone:</strong> {{ $user->phone ?? '-' }}
        </div>
        <div class="mb-3">
            <strong>Role:</strong> {{ ucfirst(str_replace('_',' ',$user->role)) }}
        </div>
        <div class="mb-3">
            <strong>Pharmacies:</strong>
            @if($user->pharmacies->count())
                <ul class="list-unstyled mb-0">
                    @foreach($user->pharmacies as $pharmacy)
                        <li>
                            {{ $pharmacy->name }} 
                            <small class="text-muted">({{ $pharmacy->district }}, {{ $pharmacy->region }})</small>
                        </li>
                    @endforeach
                </ul>
            @else
                <span>—</span>
            @endif
        </div>
        <div class="mb-3">
            <strong>Status:</strong>
            <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }}">
                {{ $user->is_active ? 'Active' : 'Inactive' }}
            </span>
        </div>
        <div class="mb-3">
            <strong>Last Login:</strong> {{ $user->last_login ? $user->last_login->format('M d, Y H:i') : 'Never' }}
        </div>
        <div class="d-flex justify-content-between">
            <a href="{{ route('superadmin.users.index') }}" class="btn btn-secondary">Back</a>
            <a href="{{ route('superadmin.users.edit', $user) }}" class="btn btn-primary">Edit User</a>
        </div>
    </div>
</div>
@endsection
