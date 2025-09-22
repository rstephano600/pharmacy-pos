@extends('layouts.app')

@section('title', 'Manage Users - Admin')
@section('header', 'User Management')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">All Users</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>User</th>
                        <th>Role</th>
                        <th>Pharmacy</th>
                        <th>Status</th>
                        <th>Last Login</th>
                        <th>Registered</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->full_name) }}&background=4e73df&color=fff" 
                                     class="rounded-circle me-3" width="40" height="40">
                                <div>
                                    <strong>{{ $user->full_name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $user->email }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : 'primary' }}">
                                {{ $user->role }}
                            </span>
                        </td>
                        <td>
                            @if($user->pharmacy)
                                <span class="badge bg-success">{{ $user->pharmacy->name }}</span>
                            @else
                                <span class="badge bg-secondary">No pharmacy</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }}">
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            @if($user->last_login)
                                {{ $user->last_login->diffForHumans() }}
                            @else
                                <span class="text-muted">Never</span>
                            @endif
                        </td>
                        <td>
                            {{ $user->created_at->format('M d, Y') }}
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="#" class="btn btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="#" class="btn btn-outline-success">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="#" class="btn btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
                            <h5 class="text-muted mt-3">No users found</h5>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $users->links() }}
    </div>
</div>
@endsection