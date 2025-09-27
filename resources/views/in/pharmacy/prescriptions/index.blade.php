@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Prescriptions</h1>

    {{-- Search & Filter --}}
    <form method="GET" action="{{ route('prescriptions.index') }}" class="mb-3 d-flex">
        <input type="text" name="search" class="form-control me-2"
               placeholder="Search by customer or doctor"
               value="{{ request('search') }}">
        <button type="submit" class="btn btn-primary">Search</button>
    </form>

    <a href="{{ route('prescriptions.create') }}" class="btn btn-success mb-3">➕ New Prescription</a>

    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>PO #</th>
                <th>Customer</th>
                <th>Doctor</th>
                <th>Status</th>
                <th>Created</th>
                <th width="180">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($prescriptions as $prescription)
                <tr>
                    <td>{{ $prescription->id }}</td>
                    <td>{{ $prescription->customer->name ?? '-' }}</td>
                    <td>{{ $prescription->doctor_name }}</td>
                    <td>
                        <span class="badge bg-{{ $prescription->status == 'pending' ? 'warning' : 'success' }}">
                            {{ ucfirst($prescription->status) }}
                        </span>
                    </td>
                    <td>{{ $prescription->created_at->format('d M Y') }}</td>
                    <td>
                        <a href="{{ route('prescriptions.show', $prescription) }}" class="btn btn-sm btn-info">View</a>
                        <a href="{{ route('prescriptions.edit', $prescription) }}" class="btn btn-sm btn-primary">Edit</a>
                        <form action="{{ route('prescriptions.destroy', $prescription) }}" method="POST"
                              style="display:inline-block"
                              onsubmit="return confirm('Are you sure you want to delete this prescription?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center">No prescriptions found</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-3">
        {{ $prescriptions->links() }}
    </div>
</div>
@endsection
