@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Purchase Orders</h2>

    <div class="mb-3">
        <a href="{{ route('purchase_orders.create') }}" class="btn btn-primary">+ New Purchase Order</a>
    </div>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>PO Number</th>
                <th>Supplier</th>
                <th>Order Date</th>
                <th>Expected Delivery</th>
                <th>Status</th>
                <th>Total</th>
                <th>Pharmacy</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
                <tr>
                    <td>{{ $order->po_number }}</td>
                    <td>{{ $order->supplier?->name ?? 'N/A' }}</td>
                    <td>{{ $order->order_date }}</td>
                    <td>{{ $order->expected_delivery_date ?? '—' }}</td>
                    <td>
                        <span class="badge bg-{{ $order->status == 'pending' ? 'warning' : ($order->status == 'received' ? 'success' : 'secondary') }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                    <td>{{ number_format($order->total_amount, 2) }}</td>
                    <td>{{ $order->pharmacy->name }}</td>
                    <td>
                        <a href="{{ route('purchase_orders.show', $order) }}" class="btn btn-info btn-sm">View</a>
                        <a href="{{ route('purchase_orders.edit', $order) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('purchase_orders.destroy', $order) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this order?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8">No purchase orders found.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-3">
        {{ $orders->links() }}
    </div>
</div>
@endsection
