@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Purchase Order #{{ $purchaseOrder->po_number }}</h2>

    <div class="card mb-3">
        <div class="card-body">
            <p><strong>Pharmacy:</strong> {{ $purchaseOrder->pharmacy->name }}</p>
            <p><strong>Supplier:</strong> {{ $purchaseOrder->supplier?->name ?? 'N/A' }}</p>
            <p><strong>Order Date:</strong> {{ $purchaseOrder->order_date }}</p>
            <p><strong>Expected Delivery:</strong> {{ $purchaseOrder->expected_delivery_date ?? '—' }}</p>
            <p><strong>Status:</strong> 
                <span class="badge bg-{{ $purchaseOrder->status == 'pending' ? 'warning' : ($purchaseOrder->status == 'received' ? 'success' : 'secondary') }}">
                    {{ ucfirst($purchaseOrder->status) }}
                </span>
            </p>
            <p><strong>Total Amount:</strong> {{ number_format($purchaseOrder->total_amount, 2) }}</p>
            <p><strong>Payment Terms:</strong> {{ $purchaseOrder->payment_terms ?? '—' }}</p>
            <p><strong>Notes:</strong> {{ $purchaseOrder->notes ?? '—' }}</p>
            <p><strong>Delivery Address:</strong> {{ is_array($purchaseOrder->delivery_address) ? json_encode($purchaseOrder->delivery_address) : ($purchaseOrder->delivery_address ?? '—') }}</p>
            <p><strong>Ordered By:</strong> {{ $purchaseOrder->orderedBy->name }}</p>
        </div>
    </div>

    {{-- Purchase Order Items --}}
    <div class="card mb-3">
        <div class="card-header">
            <h5 class="mb-0">Order Items</h5>
        </div>
        <div class="card-body p-0">
            @if($purchaseOrder->items->count())
                <table class="table table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Medicine</th>
                            <th>Quantity Ordered</th>
                            <th>Quantity Received</th>
                            <th>Unit Cost</th>
                            <th>Total Cost</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchaseOrder->items as $item)
                            <tr>
                                <td>{{ $item->medicine->name }}</td>
                                <td>{{ $item->quantity_ordered }}</td>
                                <td>{{ $item->quantity_received }}</td>
                                <td>{{ number_format($item->unit_cost, 2) }}</td>
                                <td>{{ number_format($item->total_cost, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" class="text-end">Total</th>
                            <th>{{ number_format($purchaseOrder->items->sum('total_cost'), 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            @else
                <p class="p-3">No items added to this order.</p>
            @endif
        </div>
    </div>

    <a href="{{ route('purchase_orders.index') }}" class="btn btn-secondary">Back to List</a>
    <a href="{{ route('purchase_orders.edit', $purchaseOrder) }}" class="btn btn-warning">Edit</a>
</div>
@endsection
