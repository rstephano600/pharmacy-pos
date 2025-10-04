
{{-- resources/views/in/pharmacy/sales/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Sale Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Sale Details - {{ $sale->invoice_number }}</h3>
                    <div>
                        @if($sale->status == 'completed' && $sale->created_at->diffInHours(now()) <= 24)
                            <a href="{{ route('sales.edit', $sale) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        @endif
                        <a href="{{ route('sales.receipt', $sale) }}" class="btn btn-info" target="_blank">
                            <i class="fas fa-print"></i> Print Receipt
                        </a>
                        @if($sale->status == 'completed')
                            <form method="POST" action="{{ route('sales.cancel', $sale) }}" style="display: inline;"
                                  onsubmit="return confirm('Are you sure you want to cancel this sale? Stock will be restored.')">
                                @csrf
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-ban"></i> Cancel Sale
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('sales.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    {{-- Sale Information --}}
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Sale Information</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Invoice Number:</strong></td>
                                    <td>{{ $sale->invoice_number }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Date & Time:</strong></td>
                                    <td>{{ $sale->sale_date }} {{ $sale->sale_time ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
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
                                </tr>
                                <tr>
                                    <td><strong>Sold By:</strong></td>
                                    <td>{{ $sale->seller->name }}</td>
                                </tr>
                                @if($sale->prescription)
                                    <tr>
                                        <td><strong>Prescription:</strong></td>
                                        <td>
                                            <i class="fas fa-prescription"></i> 
                                            Linked to prescription from {{ $sale->prescription->created_at->format('Y-m-d') }}
                                        </td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Customer Information</h5>
                            @if($sale->customer)
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Name:</strong></td>
                                        <td>{{ $sale->customer->name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Phone:</strong></td>
                                        <td>{{ $sale->customer->phone }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Email:</strong></td>
                                        <td>{{ $sale->customer->email ?? 'N/A' }}</td>
                                    </tr>
                                </table>
                            @else
                                <p class="text-muted">Walk-in Customer</p>
                            @endif

                            <h5 class="mt-4">Payment Information</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Method:</strong></td>
                                    <td>{{ ucfirst($sale->payment_method) }}</td>
                                </tr>
                                @if($sale->payment_reference)
                                    <tr>
                                        <td><strong>Reference:</strong></td>
                                        <td>{{ $sale->payment_reference }}</td>
                                    </tr>
                                @endif
                                @if($sale->completed_at)
                                    <tr>
                                        <td><strong>Completed At:</strong></td>
                                        <td>{{ $sale->completed_at->format('Y-m-d H:i:s') }}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    {{-- Sale Items --}}
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>Sale Items</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Medicine</th>
                                            <th>Batch</th>
                                            <th>Quantity</th>
                                            <th>Unit Price</th>
                                            <th>Unit Cost</th>
                                            <th>Discount</th>
                                            <th>Total</th>
                                            <th>Profit</th>
                                            <th>Expiry Date</th>
                                            <th>Instructions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($sale->items as $item)
                                            <tr>
                                                <td>
                                                    <strong>{{ $item->medicine->name }}</strong>
                                                    <br><small class="text-muted">{{ $item->medicine->generic_name }}</small>
                                                </td>
                                                <td>{{ $item->medicineBatch->batch_number ?? 'N/A' }}</td>
                                                <td>{{ $item->quantity }}</td>
                                                <td>${{ number_format($item->unit_price, 2) }}</td>
                                                <td>${{ number_format($item->unit_cost, 2) }}</td>
                                                <td>
                                                    @if($item->discount_amount > 0)
                                                        ${{ number_format($item->discount_amount, 2) }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>${{ number_format($item->total_price, 2) }}</td>
                                                <td>
                                                    @php
                                                        $itemProfit = ($item->unit_price - $item->unit_cost) * $item->quantity;
                                                        $profitMargin = $item->unit_price > 0 ? (($item->unit_price - $item->unit_cost) / $item->unit_price) * 100 : 0;
                                                    @endphp
                                                    <span class="{{ $itemProfit > 0 ? 'text-success' : 'text-danger' }}">
                                                        ${{ number_format($itemProfit, 2) }}
                                                        <br><small>({{ number_format($profitMargin, 1) }}%)</small>
                                                    </span>
                                                </td>
                                                <td>
                                                    {{ $item->expiry_date_at_sale }}
                                                    @if($item->expiry_date_at_sale <= now()->addMonths(6))
                                                        <small class="text-warning d-block">
                                                            <i class="fas fa-exclamation-triangle"></i> Near expiry
                                                        </small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($item->dosage_instructions)
                                                        <small>{{ $item->dosage_instructions }}</small>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-info">
                                            <th colspan="6">Total Items: {{ $sale->items->count() }} | Total Quantity: {{ $sale->items->sum('quantity') }}</th>
                                            <th>${{ number_format($sale->items->sum('total_price'), 2) }}</th>
                                            <th>
                                                @php
                                                    $totalItemProfit = $sale->items->sum(function($item) {
                                                        return ($item->unit_price - $item->unit_cost) * $item->quantity;
                                                    });
                                                @endphp
                                                <span class="{{ $totalItemProfit > 0 ? 'text-success' : 'text-danger' }}">
                                                    ${{ number_format($totalItemProfit, 2) }}
                                                </span>
                                            </th>
                                            <th colspan="2"></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Financial Summary --}}
                    <div class="row">
                        <div class="col-md-6">
                            {{-- Profit Analysis --}}
                            <div class="card">
                                <div class="card-header">
                                    <h5>Profit Analysis</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Total Revenue:</strong></td>
                                            <td class="text-right">${{ number_format($profitAnalysis['total_revenue'], 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Total Cost:</strong></td>
                                            <td class="text-right">${{ number_format($profitAnalysis['total_cost'], 2) }}</td>
                                        </tr>
                                        <tr class="border-top">
                                            <td><strong>Gross Profit:</strong></td>
                                            <td class="text-right {{ $profitAnalysis['gross_profit'] > 0 ? 'text-success' : 'text-danger' }}">
                                                <strong>${{ number_format($profitAnalysis['gross_profit'], 2) }}</strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Profit Margin:</strong></td>
                                            <td class="text-right {{ $profitAnalysis['profit_margin'] > 0 ? 'text-success' : 'text-danger' }}">
                                                <strong>{{ number_format($profitAnalysis['profit_margin'], 2) }}%</strong>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            {{-- Sale Summary --}}
                            <div class="card">
                                <div class="card-header">
                                    <h5>Sale Summary</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Subtotal:</strong></td>
                                            <td class="text-right">${{ number_format($sale->subtotal, 2) }}</td>
                                        </tr>
                                        @if($sale->discount_amount > 0)
                                            <tr>
                                                <td><strong>Discount ({{ $sale->discount_rate }}%):</strong></td>
                                                <td class="text-right text-success">-${{ number_format($sale->discount_amount, 2) }}</td>
                                            </tr>
                                        @endif
                                        @if($sale->tax_amount > 0)
                                            <tr>
                                                <td><strong>Tax ({{ $sale->tax_rate }}%):</strong></td>
                                                <td class="text-right">${{ number_format($sale->tax_amount, 2) }}</td>
                                            </tr>
                                        @endif
                                        <tr class="border-top">
                                            <td><strong>Total Amount:</strong></td>
                                            <td class="text-right">
                                                <h4 class="mb-0">${{ number_format($sale->total_amount, 2) }}</h4>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Notes --}}
                    @if($sale->notes)
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5>Notes</h5>
                            </div>
                            <div class="card-body">
                                <p class="mb-0">{{ $sale->notes }}</p>
                            </div>
                        </div>
                    @endif

                    {{-- Stock Movements --}}
                    @if($stockMovements->count() > 0)
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5>Related Stock Movements</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Date/Time</th>
                                                <th>Medicine</th>
                                                <th>Batch</th>
                                                <th>Movement</th>
                                                <th>Quantity Change</th>
                                                <th>After</th>
                                                <th>Notes</th>
                                                <th>By</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($stockMovements as $movement)
                                                <tr>
                                                    <td>{{ $movement->created_at->format('Y-m-d H:i') }}</td>
                                                    <td>{{ $movement->medicine->name ?? 'N/A' }}</td>
                                                    <td>{{ $movement->medicineBatch->batch_number ?? 'N/A' }}</td>
                                                    <td>
                                                        <span class="badge badge-{{ $movement->movement_type == 'sale' ? 'primary' : 'secondary' }}">
                                                            {{ ucfirst($movement->movement_type) }}
                                                        </span>
                                                    </td>
                                                    <td class="{{ $movement->quantity_change > 0 ? 'text-success' : 'text-danger' }}">
                                                        {{ $movement->quantity_change > 0 ? '+' : '' }}{{ $movement->quantity_change }}
                                                    </td>
                                                    <td>{{ $movement->quantity_after }}</td>
                                                    <td>{{ $movement->notes }}</td>
                                                    <td>{{ $movement->creator->name ?? 'System' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
