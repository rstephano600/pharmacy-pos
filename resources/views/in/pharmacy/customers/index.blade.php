@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Customers</h2>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('customers.index') }}" class="row g-2">
                <div class="col-md-4">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Search name, phone, email, insurance..." class="form-control">
                </div>
                <div class="col-md-3">
                    <select name="customer_type" class="form-select">
                        <option value="">-- All Types --</option>
                        <option value="individual" {{ request('customer_type')=='individual'?'selected':'' }}>Individual</option>
                        <option value="hospital" {{ request('customer_type')=='hospital'?'selected':'' }}>Hospital</option>
                        <option value="clinic" {{ request('customer_type')=='clinic'?'selected':'' }}>Clinic</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100">Filter</button>
                </div>
                <div class="col-md-3 text-end">
                    <a href="{{ route('customers.create') }}" class="btn btn-success">+ New Customer</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Type</th>
                        <th>Insurance</th>
                        @if(auth()->user()->hasRole('super_admin'))
                            <th>Pharmacy</th>
                        @endif
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                        <tr>
                            <td>{{ $customer->name }}</td>
                            <td>{{ $customer->phone }}</td>
                            <td>{{ $customer->email }}</td>
                            <td>{{ ucfirst($customer->customer_type) }}</td>
                            <td>{{ $customer->insurance_provider ?? '—' }}</td>
                            @if(auth()->user()->hasRole('super_admin'))
                                <td>{{ $customer->pharmacy->name ?? 'N/A' }}</td>
                            @endif
                            <td>
                                <a href="{{ route('customers.show', $customer) }}" class="btn btn-sm btn-info">View</a>
                                <a href="{{ route('customers.edit', $customer) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('customers.destroy', $customer) }}" method="POST" 
                                      class="d-inline" onsubmit="return confirm('Delete this customer?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Del</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7">No customers found.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-3">
                {{ $customers->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
