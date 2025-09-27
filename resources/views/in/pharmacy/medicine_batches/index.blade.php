@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Medicine Batches</h2>

    <a href="{{ route('medicine_batches.create') }}" class="btn btn-success mb-3">Add New Batch</a>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Medicine</th>
                <th>PO Item</th>
                <th>Batch Number</th>
                <th>Qty Received</th>
                <th>Qty Available</th>
                <th>Unit Cost</th>
                <th>Selling Price</th>
                <th>Expiry Date</th>
                <th>Received By</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($batches as $batch)
            <tr>
                <td>{{ $batch->id }}</td>
                <td>{{ $batch->medicine->name }}</td>
                <td>{{ $batch->purchaseOrderItem?->purchaseOrder?->po_number ?? '—' }}</td>
                <td>{{ $batch->batch_number ?? '—' }}</td>
                <td>{{ $batch->quantity_received }}</td>
                <td>{{ $batch->quantity_available }}</td>
                <td>{{ number_format($batch->unit_cost, 2) }}</td>
                <td>{{ number_format($batch->selling_price ?? 0, 2) }}</td>
                <td>{{ $batch->expiry_date ?? '—' }}</td>
                <td>{{ $batch->receiver->name }}</td>
                <td>
                    <a href="{{ route('medicine_batches.show', $batch) }}" class="btn btn-sm btn-info">View</a>
                    <a href="{{ route('medicine_batches.edit', $batch) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('medicine_batches.destroy', $batch) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Delete this batch?');">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="11" class="text-center">No medicine batches found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-3">{{ $batches->links() }}</div>
</div>
@endsection
