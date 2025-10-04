{{-- resources/views/in/pharmacy/sales/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Sales Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Sales Management</h3>
                    <div>
                        <a href="{{ route('sales.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> New Sale
                        </a>
                        <a href="{{ route('sales.analytics') }}" class="btn btn-info">
                            <i class="fas fa-chart-bar"></i> Analytics
                        </a>
                    </div>
                </div>

                {{-- Statistics Cards --}}
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>${{ number_format($stats['total_sales'], 2) }}</h4>
                                            <p class="mb-0">Total Sales</p>
                                        </div>
                                        <i class="fas fa-dollar-sign fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>{{ number_format($stats['total_transactions']) }}</h4>
                                            <p class="mb-0">Transactions</p>
                                        </div>
                                        <i class="fas fa-receipt fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>${{ number_format($stats['average_sale'], 2) }}</h4>
                                            <p class="mb-0">Average Sale</p>
                                        </div>
                                        <i class="fas fa-chart-line fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>${{ number_format($stats['today_sales'], 2) }}</h4>
                                            <p class="mb-0">Today's Sales</p>
                                        </div>
                                        <i class="fas fa-calendar-day fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Filters --}}
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Filter Sales</h5>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="{{ route('sales.index') }}">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label>Invoice Number</label>
                                        <input type="text" name="invoice_number" class="form-control" 
                                               value="{{ request('invoice_number') }}" placeholder="Search invoice...">
                                    </div>
                                    <div class="col-md-3">
                                        <label>Customer</label>
                                        <select name="customer_id" class="form-control">
                                            <option value="">All Customers</option>
                                            @foreach($customers as $customer)
                                                <option value="{{ $customer->id }}" 
                                                        {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                                                    {{ $customer->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Status</label>
                                        <select name="status" class="form-control">
                                            <option value="">All Status</option>
                                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                            <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Date From</label>
                                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label>Date To</label>
                                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary">Filter</button>
                                        <a href="{{ route('sales.index') }}" class="btn btn-secondary">Reset</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    

                    {{-- Sales Table --}}
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Date & Time</th>
                                    <th>Customer</th>
                                    <th>Items</th>
                                    <th>Total Amount</th>
                                    <th>Payment</th>
                                    <th>Status</th>
                                    <th>Sold By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sales as $sale)
                                    <tr>
                                        <td>
                                            <strong>{{ $sale->invoice_number }}</strong>
                                            @if($sale->prescription_id)
                                                <small class="text-info d-block">
                                                    <i class="fas fa-prescription"></i> Prescription
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            <div>{{ $sale->sale_date }}</div>
                                            <small class="text-muted">{{ $sale->sale_time ?? 'N/A' }}</small>
                                        </td>
                                        <td>
                                            @if($sale->customer)
                                                <div>{{ $sale->customer->name }}</div>
                                                <small class="text-muted">{{ $sale->customer->phone }}</small>
                                            @else
                                                <span class="text-muted">Walk-in Customer</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-info">{{ $sale->items->count() }} items</span>
                                            <small class="d-block text-muted">
                                                {{ $sale->items->sum('quantity') }} units
                                            </small>
                                        </td>
                                        <td>
                                            <strong>${{ number_format($sale->total_amount, 2) }}</strong>
                                            @if($sale->discount_amount > 0)
                                                <small class="text-success d-block">
                                                    -${{ number_format($sale->discount_amount, 2) }} discount
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-secondary">{{ ucfirst($sale->payment_method) }}</span>
                                            @if($sale->payment_reference)
                                                <small class="d-block text-muted">{{ $sale->payment_reference }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @switch($sale->status)
                                                @case('completed')
                                                    <span class="badge badge-success">Completed</span>
                                                    @break
                                                @case('pending')
                                                    <span class="badge badge-warning">Pending</span>
                                                    @break
                                                @case('cancelled')
                                                    <span class="badge badge-danger">Cancelled</span>
                                                    @break
                                                @case('refunded')
                                                    <span class="badge badge-info">Refunded</span>
                                                    @break
                                            @endswitch
                                        </td>
                                        <td>
                                            <div>{{ $sale->seller->name }}</div>
                                            <small class="text-muted">{{ $sale->created_at->format('H:i') }}</small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('sales.show', $sale) }}" 
                                                   class="btn btn-sm btn-info" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                
                                                @if($sale->status == 'completed' && $sale->created_at->diffInHours(now()) <= 24)
                                                    <a href="{{ route('sales.edit', $sale) }}" 
                                                       class="btn btn-sm btn-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endif

                                                <a href="{{ route('sales.receipt', $sale) }}" 
                                                   class="btn btn-sm btn-secondary" title="Print Receipt" target="_blank">
                                                    <i class="fas fa-print"></i>
                                                </a>

                                                @if($sale->status == 'completed')
                                                    <form method="POST" action="{{ route('sales.cancel', $sale) }}" 
                                                          style="display: inline;" 
                                                          onsubmit="return confirm('Are you sure you want to cancel this sale? Stock will be restored.')">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Cancel Sale">
                                                            <i class="fas fa-ban"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-receipt fa-3x mb-3"></i>
                                                <h5>No sales found</h5>
                                                <p>No sales match your current filters.</p>
                                                <a href="{{ route('sales.create') }}" class="btn btn-primary">
                                                    <i class="fas fa-plus"></i> Create First Sale
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if($sales->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $sales->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
