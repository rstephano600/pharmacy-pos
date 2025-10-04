
{{-- resources/views/in/pharmacy/sales/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Sale')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Sale - {{ $sale->invoice_number }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('sales.show', $sale) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Sale
                        </a>
                    </div>
                </div>

                <form method="POST" action="{{ route('sales.update', $sale) }}">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Note:</strong> Only limited fields can be edited to maintain data integrity. 
                            Sale items cannot be modified after completion.
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Customer</label>
                                    <select name="customer_id" class="form-control">
                                        <option value="">Walk-in Customer</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" 
                                                    {{ $sale->customer_id == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->name }} - {{ $customer->phone }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('customer_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Prescription</label>
                                    <select name="prescription_id" class="form-control">
                                        <option value="">No Prescription</option>
                                        @foreach($prescriptions as $prescription)
                                            <option value="{{ $prescription->id }}"
                                                    {{ $sale->prescription_id == $prescription->id ? 'selected' : '' }}>
                                                {{ $prescription->customer->name ?? 'N/A' }} - {{ $prescription->created_at->format('Y-m-d') }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Payment Method</label>
                                    <select name="payment_method" class="form-control" required>
                                        <option value="cash" {{ $sale->payment_method == 'cash' ? 'selected' : '' }}>Cash</option>
                                        <option value="insurance" {{ $sale->payment_method == 'insurance' ? 'selected' : '' }}>Insurance</option>
                                        <option value="mpesa" {{ $sale->payment_method == 'mpesa' ? 'selected' : '' }}>M-Pesa</option>
                                        <option value="card" {{ $sale->payment_method == 'card' ? 'selected' : '' }}>Card</option>
                                        <option value="bank_transfer" {{ $sale->payment_method == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                    </select>
                                    @error('payment_method')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Payment Reference</label>
                                    <input type="text" name="payment_reference" class="form-control" 
                                           value="{{ $sale->payment_reference }}" 
                                           placeholder="Transaction ID, Check Number, etc.">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Notes</label>
                            <textarea name="notes" class="form-control" rows="3" 
                                      placeholder="Additional notes about this sale...">{{ $sale->notes }}</textarea>
                        </div>

                        {{-- Display current sale items (read-only) --}}
                        <div class="card">
                            <div class="card-header">
                                <h5>Current Sale Items (Read-Only)</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Medicine</th>
                                                <th>Quantity</th>
                                                <th>Unit Price</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($sale->items as $item)
                                                <tr>
                                                    <td>{{ $item->medicine->name }}</td>
                                                    <td>{{ $item->quantity }}</td>
                                                    <td>${{ number_format($item->unit_price, 2) }}</td>
                                                    <td>${{ number_format($item->total_price, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr class="table-info">
                                                <th colspan="3">Total:</th>
                                                <th>${{ number_format($sale->total_amount, 2) }}</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Sale
                        </button>
                        <a href="{{ route('sales.show', $sale) }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
