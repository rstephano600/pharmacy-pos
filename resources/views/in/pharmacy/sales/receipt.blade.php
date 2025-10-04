
{{-- resources/views/in/pharmacy/sales/receipt.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <title>Receipt - {{ $sale->invoice_number }}</title>
    <style>
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
        
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            max-width: 300px;
            margin: 0 auto;
            padding: 10px;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        
        .pharmacy-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .info-section {
            margin-bottom: 15px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        .items-table th,
        .items-table td {
            text-align: left;
            padding: 2px;
            border-bottom: 1px dashed #ccc;
        }
        
        .items-table th {
            border-bottom: 2px solid #000;
        }
        
        .total-section {
            border-top: 2px solid #000;
            padding-top: 10px;
            margin-top: 15px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }
        
        .grand-total {
            font-weight: bold;
            font-size: 14px;
            border-top: 1px solid #000;
            padding-top: 5px;
            margin-top: 5px;
        }
        
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px dashed #000;
            font-size: 10px;
        }
        
        .no-print {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()" class="btn btn-primary">Print Receipt</button>
        <button onclick="window.close()" class="btn btn-secondary">Close</button>
    </div>

    <div class="receipt">
        {{-- Header --}}
        <div class="header">
            <div class="pharmacy-name">{{ $sale->pharmacy->name ?? 'Pharmacy' }}</div>
            <div>{{ $sale->pharmacy->address ?? '' }}</div>
            <div>{{ $sale->pharmacy->phone ?? '' }}</div>
            @if($sale->pharmacy->email)
                <div>{{ $sale->pharmacy->email }}</div>
            @endif
        </div>

        {{-- Sale Information --}}
        <div class="info-section">
            <div class="info-row">
                <span>Invoice:</span>
                <span><strong>{{ $sale->invoice_number }}</strong></span>
            </div>
            <div class="info-row">
                <span>Date:</span>
                <span>{{ $sale->sale_date }} {{ $sale->sale_time ?? '' }}</span>
            </div>
            <div class="info-row">
                <span>Cashier:</span>
                <span>{{ $sale->seller->name }}</span>
            </div>
            @if($sale->customer)
                <div class="info-row">
                    <span>Customer:</span>
                    <span>{{ $sale->customer->name }}</span>
                </div>
                @if($sale->customer->phone)
                    <div class="info-row">
                        <span>Phone:</span>
                        <span>{{ $sale->customer->phone }}</span>
                    </div>
                @endif
            @endif
            @if($sale->prescription_id)
                <div class="info-row">
                    <span>Prescription:</span>
                    <span>Yes</span>
                </div>
            @endif
        </div>

        {{-- Items Table --}}
        <table class="items-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $item)
                    <tr>
                        <td>
                            {{ $item->medicine->name }}
                            @if($item->dosage_instructions)
                                <br><small>{{ Str::limit($item->dosage_instructions, 30) }}</small>
                            @endif
                        </td>
                        <td>{{ $item->quantity }}</td>
                        <td>${{ number_format($item->unit_price, 2) }}</td>
                        <td>${{ number_format($item->total_price, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Totals --}}
        <div class="total-section">
            <div class="total-row">
                <span>Subtotal:</span>
                <span>${{ number_format($sale->subtotal, 2) }}</span>
            </div>
            
            @if($sale->discount_amount > 0)
                <div class="total-row">
                    <span>Discount ({{ $sale->discount_rate }}%):</span>
                    <span>-${{ number_format($sale->discount_amount, 2) }}</span>
                </div>
            @endif
            
            @if($sale->tax_amount > 0)
                <div class="total-row">
                    <span>Tax ({{ $sale->tax_rate }}%):</span>
                    <span>${{ number_format($sale->tax_amount, 2) }}</span>
                </div>
            @endif
            
            <div class="total-row grand-total">
                <span>TOTAL:</span>
                <span>${{ number_format($sale->total_amount, 2) }}</span>
            </div>
            
            <div class="total-row">
                <span>Payment:</span>
                <span>{{ ucfirst($sale->payment_method) }}</span>
            </div>
            
            @if($sale->payment_reference)
                <div class="total-row">
                    <span>Reference:</span>
                    <span>{{ $sale->payment_reference }}</span>
                </div>
            @endif
        </div>

        {{-- Footer --}}
        <div class="footer">
            <div>Thank you for your business!</div>
            <div>{{ now()->format('Y-m-d H:i:s') }}</div>
            @if($sale->notes)
                <div style="margin-top: 10px;">
                    <strong>Note:</strong> {{ $sale->notes }}
                </div>
            @endif
        </div>
    </div>

    <script>
        // Auto-print when page loads (optional)
        // window.onload = function() { window.print(); };
    </script>
</body>
</html>