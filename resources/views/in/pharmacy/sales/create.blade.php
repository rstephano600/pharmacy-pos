{{-- resources/views/pharmacy/sales/create.blade.php --}}
@extends('layouts.app')

@section('title', 'New Sale')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">New Sale</h3>
                    <div class="card-tools">
                        <a href="{{ route('sales.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Sales
                        </a>
                    </div>
                </div>

                <form id="saleForm" method="POST" action="{{ route('sales.store') }}">
                    @csrf
                    <div class="card-body">
                        {{-- Customer & Basic Info --}}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Customer</label>
                                    <select name="customer_id" id="customer_id" class="form-control select2">
                                        <option value="">Walk-in Customer</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}">
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
                                    <label>Prescription (Optional)</label>
                                    <select name="prescription_id" id="prescription_id" class="form-control select2">
                                        <option value="">No Prescription</option>
                                        @foreach($prescriptions as $prescription)
                                            <option value="{{ $prescription->id }}">
                                                {{ $prescription->customer->name ?? 'N/A' }} - {{ $prescription->created_at->format('Y-m-d') }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Sale Date</label>
                                    <input type="date" name="sale_date" class="form-control" 
                                           value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}" required>
                                    @error('sale_date')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Payment Method</label>
                                    <select name="payment_method" class="form-control" required>
                                        <option value="cash">Cash</option>
                                        <option value="insurance">Insurance</option>
                                        <option value="mpesa">M-Pesa</option>
                                        <option value="card">Card</option>
                                        <option value="bank_transfer">Bank Transfer</option>
                                    </select>
                                    @error('payment_method')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Payment Reference</label>
                                    <input type="text" name="payment_reference" class="form-control" 
                                           placeholder="Transaction ID, Check Number, etc.">
                                </div>
                            </div>
                        </div>

                        {{-- Sale Items --}}
                        <div class="card">
                            <div class="card-header">
                                <h5>Sale Items</h5>
                                <button type="button" id="addItem" class="btn btn-sm btn-success float-right">
                                    <i class="fas fa-plus"></i> Add Item
                                </button>
                            </div>
                            <div class="card-body">
                                <div id="saleItems">
                                    <!-- Items will be added dynamically -->
                                </div>
                                
                                {{-- Item Template (Hidden) --}}
                                <div id="itemTemplate" class="sale-item-row" style="display: none;">
                                    <div class="row mb-3 border p-3">
                                        <div class="col-md-3">
                                            <label>Medicine</label>
                                            <select name="items[INDEX][medicine_id]" class="form-control medicine-select" required>
                                                <option value="">Select Medicine</option>
                                                @foreach($medicines as $medicine)
                                                    <option value="{{ $medicine->id }}">{{ $medicine->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label>Batch</label>
                                            <select name="items[INDEX][medicine_batch_id]" class="form-control batch-select" required>
                                                <option value="">Select Batch</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label>Quantity</label>
                                            <input type="number" name="items[INDEX][quantity]" class="form-control quantity-input" 
                                                   min="1" required>
                                            <small class="available-stock text-muted"></small>
                                        </div>
                                        <div class="col-md-2">
                                            <label>Unit Price</label>
                                            <input type="number" name="items[INDEX][unit_price]" class="form-control price-input" 
                                                   step="0.01" min="0" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label>Line Total</label>
                                            <input type="text" class="form-control line-total" readonly>
                                        </div>
                                        <div class="col-md-1">
                                            <label>&nbsp;</label>
                                            <button type="button" class="btn btn-danger btn-block remove-item">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                        <div class="col-md-12 mt-2">
                                            <label>Dosage Instructions (Optional)</label>
                                            <textarea name="items[INDEX][dosage_instructions]" class="form-control" rows="2" 
                                                      placeholder="Take 2 tablets twice daily after meals..."></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Sale Totals --}}
                        <div class="row mt-4">
                            <div class="col-md-8"></div>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>Sale Summary</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-2">
                                            <div class="col-6">Subtotal:</div>
                                            <div class="col-6 text-right">
                                                $<span id="subtotal">0.00</span>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-8">
                                                <label>Discount %</label>
                                                <input type="number" name="discount_rate" id="discount_rate" 
                                                       class="form-control form-control-sm" step="0.01" min="0" max="100" value="0">
                                            </div>
                                            <div class="col-4 text-right">
                                                $<span id="discount_amount">0.00</span>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-8">
                                                <label>Tax %</label>
                                                <input type="number" name="tax_rate" id="tax_rate" 
                                                       class="form-control form-control-sm" step="0.01" min="0" max="100" value="0">
                                            </div>
                                            <div class="col-4 text-right">
                                                $<span id="tax_amount">0.00</span>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-6"><strong>Total:</strong></div>
                                            <div class="col-6 text-right">
                                                <strong>$<span id="total_amount">0.00</span></strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Notes --}}
                        <div class="form-group mt-3">
                            <label>Notes (Optional)</label>
                            <textarea name="notes" class="form-control" rows="3" 
                                      placeholder="Additional notes about this sale..."></textarea>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save"></i> Complete Sale
                        </button>
                        <a href="{{ route('sales.index') }}" class="btn btn-secondary btn-lg">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- JavaScript for dynamic form handling --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    let itemIndex = 0;
    
    // Batches data from backend - make sure this is passed from controller
    const batchesData = @json($batches ?? []);
    console.log('Batches data loaded:', batchesData);
    
    // Add item functionality
    document.getElementById('addItem').addEventListener('click', function() {
        const template = document.getElementById('itemTemplate');
        const clone = template.cloneNode(true);
        clone.style.display = 'block';
        clone.id = '';
        clone.classList.remove('sale-item-row');
        
        // Replace INDEX with current index
        clone.innerHTML = clone.innerHTML.replace(/INDEX/g, itemIndex);
        
        document.getElementById('saleItems').appendChild(clone);
        
        // Add event listeners to new item
        setupItemEvents(clone);
        itemIndex++;
    });
    
    // Setup events for item row
    function setupItemEvents(row) {
        const medicineSelect = row.querySelector('.medicine-select');
        const batchSelect = row.querySelector('.batch-select');
        const quantityInput = row.querySelector('.quantity-input');
        const priceInput = row.querySelector('.price-input');
        const removeBtn = row.querySelector('.remove-item');
        
        console.log('Setting up events for new item row');
        
        // Medicine change event
        medicineSelect.addEventListener('change', function() {
            console.log('Medicine changed:', this.value);
            const medicineId = this.value;
            if (medicineId) {
                loadBatches(medicineId, batchSelect);
            } else {
                batchSelect.innerHTML = '<option value="">Select Batch</option>';
            }
        });
        
        // Batch change event
        batchSelect.addEventListener('change', function() {
            console.log('Batch changed:', this.value);
            const batchId = this.value;
            if (batchId) {
                loadBatchDetails(batchId, row);
            } else {
                // Clear batch details
                row.querySelector('.price-input').value = '';
                row.querySelector('.available-stock').textContent = '';
                updateLineTotal(row);
            }
        });
        
        // Quantity and price change events
        quantityInput.addEventListener('input', function() {
            validateQuantity(row);
            updateLineTotal(row);
        });
        priceInput.addEventListener('input', updateLineTotal.bind(null, row));
        
        // Remove item event
        removeBtn.addEventListener('click', function() {
            row.remove();
            calculateTotals();
        });
    }
    
    // Load available batches for medicine
    function loadBatches(medicineId, batchSelect) {
        console.log('Loading batches for medicine:', medicineId);
        console.log('Available batches data:', batchesData);
        
        // Filter batches for this medicine that have available stock
        const batches = batchesData.filter(batch => {
            return batch.medicine_id == medicineId && batch.quantity_available > 0;
        });
        
        console.log('Filtered batches:', batches);
        
        batchSelect.innerHTML = '<option value="">Select Batch</option>';
        
        if (batches.length > 0) {
            batches.forEach(batch => {
                const option = document.createElement('option');
                option.value = batch.id;
                option.textContent = `${batch.batch_number || 'N/A'} (${batch.quantity_available} available) - Exp: ${batch.expiry_date}`;
                option.dataset.price = batch.selling_price;
                option.dataset.available = batch.quantity_available;
                batchSelect.appendChild(option);
            });
        } else {
            const option = document.createElement('option');
            option.value = '';
            option.textContent = 'No batches available';
            option.disabled = true;
            batchSelect.appendChild(option);
        }
    }
    
    // Load batch details
    function loadBatchDetails(batchId, row) {
        const batchSelect = row.querySelector('.batch-select');
        const selectedOption = batchSelect.options[batchSelect.selectedIndex];
        const priceInput = row.querySelector('.price-input');
        const availableStock = row.querySelector('.available-stock');
        const quantityInput = row.querySelector('.quantity-input');
        
        console.log('Loading batch details for:', batchId);
        
        if (selectedOption && selectedOption.dataset.price) {
            priceInput.value = selectedOption.dataset.price;
            availableStock.textContent = `Available: ${selectedOption.dataset.available}`;
            quantityInput.max = selectedOption.dataset.available;
            validateQuantity(row);
            updateLineTotal(row);
        }
    }
    
    // Validate quantity against available stock
    function validateQuantity(row) {
        const batchSelect = row.querySelector('.batch-select');
        const quantityInput = row.querySelector('.quantity-input');
        const selectedOption = batchSelect.options[batchSelect.selectedIndex];
        
        if (selectedOption && selectedOption.dataset.available) {
            const available = parseInt(selectedOption.dataset.available);
            const quantity = parseInt(quantityInput.value) || 0;
            
            if (quantity > available) {
                quantityInput.setCustomValidity(`Only ${available} units available`);
                quantityInput.classList.add('is-invalid');
            } else {
                quantityInput.setCustomValidity('');
                quantityInput.classList.remove('is-invalid');
            }
        }
    }
    
    // Update line total for a row
    function updateLineTotal(row) {
        const quantityInput = row.querySelector('.quantity-input');
        const priceInput = row.querySelector('.price-input');
        const lineTotalInput = row.querySelector('.line-total');
        
        const quantity = parseFloat(quantityInput.value) || 0;
        const price = parseFloat(priceInput.value) || 0;
        const lineTotal = quantity * price;
        
        lineTotalInput.value = lineTotal.toFixed(2);
        calculateTotals();
    }
    
    // Calculate sale totals
    function calculateTotals() {
        let subtotal = 0;
        const lineTotals = document.querySelectorAll('.line-total');
        
        lineTotals.forEach(input => {
            subtotal += parseFloat(input.value) || 0;
        });
        
        const discountRate = parseFloat(document.getElementById('discount_rate').value) || 0;
        const taxRate = parseFloat(document.getElementById('tax_rate').value) || 0;
        
        const discountAmount = (subtotal * discountRate) / 100;
        const afterDiscount = subtotal - discountAmount;
        const taxAmount = (afterDiscount * taxRate) / 100;
        const totalAmount = afterDiscount + taxAmount;
        
        document.getElementById('subtotal').textContent = subtotal.toFixed(2);
        document.getElementById('discount_amount').textContent = discountAmount.toFixed(2);
        document.getElementById('tax_amount').textContent = taxAmount.toFixed(2);
        document.getElementById('total_amount').textContent = totalAmount.toFixed(2);
    }
    
    // Add discount and tax rate change events
    document.getElementById('discount_rate').addEventListener('input', calculateTotals);
    document.getElementById('tax_rate').addEventListener('input', calculateTotals);
    
    // Form submission validation
    document.getElementById('saleForm').addEventListener('submit', function(e) {
        const saleItems = document.querySelectorAll('#saleItems > div');
        
        if (saleItems.length === 0) {
            e.preventDefault();
            alert('Please add at least one sale item');
            return false;
        }
        
        // Validate all quantity inputs
        let hasInvalidQuantity = false;
        document.querySelectorAll('.quantity-input').forEach(input => {
            if (input.validationMessage) {
                hasInvalidQuantity = true;
                input.classList.add('is-invalid');
            }
        });
        
        if (hasInvalidQuantity) {
            e.preventDefault();
            alert('Please fix quantity errors before submitting');
            return false;
        }
    });
    
    // Add first item on page load
    document.getElementById('addItem').click();
});
</script>
@endsection