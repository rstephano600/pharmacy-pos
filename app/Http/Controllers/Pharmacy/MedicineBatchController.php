<?php

namespace App\Http\Controllers\Pharmacy;

use App\Http\Controllers\Controller;
use App\Models\MedicineBatch;
use App\Models\Medicine;
use App\Models\PharmacyStock;
use App\Models\StockMovement;
use App\Models\PurchaseOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MedicineBatchController extends Controller
{
    public function index()
    {
        $query = MedicineBatch::with(['medicine', 'purchaseOrderItem', 'receiver', 'pharmacy']);

        // Filter by pharmacy for non-super admins
        if (Auth::user()->role !== 'super_admin') {
            $query->where('pharmacy_id', session('active_pharmacy_id'));
        }

        $batches = $query->latest()->paginate(15);

        // Add stock status information
        $batches->getCollection()->transform(function ($batch) {
            $batch->stock_status = $this->getStockStatus($batch);
            return $batch;
        });

        return view('in.pharmacy.medicine_batches.index', compact('batches'));
    }

    public function create()
    {
        $pharmacyId = session('active_pharmacy_id');

        $medicines = Medicine::query()
            ->when(Auth::user()->role !== 'super_admin', fn($q) => $q->where('pharmacy_id', $pharmacyId))
            ->get();

        $purchaseOrderItems = PurchaseOrderItem::query()
            ->with('purchaseOrder')
            ->when(Auth::user()->role !== 'super_admin', fn($q) => $q->whereHas('purchaseOrder', fn($q2) => $q2->where('pharmacy_id', $pharmacyId)))
            ->where('status', 'pending') // Only show pending items
            ->get();

        return view('in.pharmacy.medicine_batches.create', compact('medicines', 'purchaseOrderItems'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
    'medicine_id' => 'required|exists:medicines,id',
    'purchase_order_item_id' => 'nullable|exists:purchase_order_items,id',
    'batch_number' => 'nullable|string|max:255',
    'manufacture_date' => 'nullable|date|before_or_equal:today',
    'expiry_date' => 'nullable|date|after:today|after_or_equal:manufacture_date',
    'quantity_received' => 'required|integer|min:1',
    'quantity_available' => 'nullable|integer|min:0|max:quantity_received',
    'unit_cost' => 'required|numeric|min:0',
    'selling_price' => 'nullable|numeric|gte:unit_cost', // ✅ fixed
    'is_expired' => 'boolean',
]);


        // Additional validation for pharmacy access
        $medicine = Medicine::findOrFail($validated['medicine_id']);
        $pharmacyId = session('active_pharmacy_id');

        if (Auth::user()->role !== 'super_admin' && $medicine->pharmacy_id !== $pharmacyId) {
            throw ValidationException::withMessages([
                'medicine_id' => 'You do not have access to this medicine.'
            ]);
        }

        $validated['quantity_available'] = $validated['quantity_available'] ?? $validated['quantity_received'];
        $validated['received_by'] = Auth::id();
        $validated['pharmacy_id'] = $medicine->pharmacy_id;

        // Check for expiry
        if (isset($validated['expiry_date']) && $validated['expiry_date'] <= now()->toDateString()) {
            $validated['is_expired'] = true;
        }

        DB::transaction(function () use ($validated, $medicine) {
            // Create medicine batch
            $batch = MedicineBatch::create($validated);

            // Update or create pharmacy stock
            $this->updatePharmacyStock($medicine, $batch, 'increase');

            // Record stock movement
            $this->recordStockMovement([
                'pharmacy_id' => $batch->pharmacy_id,
                'medicine_id' => $batch->medicine_id,
                'medicine_batch_id' => $batch->id,
                'movement_type' => 'receipt',
                'quantity_change' => $batch->quantity_received,
                'quantity_before' => 0, // Will be calculated in the method
                'reference_type' => $batch->purchase_order_item_id ? 'purchase_order' : 'direct_receipt',
                'reference_id' => $batch->purchase_order_item_id ?? $batch->id,
                'notes' => "Batch received: {$batch->batch_number}",
                'created_by' => Auth::id()
            ]);

            // Update purchase order item if linked
            if ($batch->purchase_order_item_id) {
                $this->updatePurchaseOrderItem($batch);
            }
        });

        return redirect()->route('medicine_batches.index')
            ->with('success', 'Medicine batch recorded successfully and stock updated.');
    }

    public function show(MedicineBatch $medicineBatch)
    {
        $this->authorizeAccess($medicineBatch);
        
        $medicineBatch->load(['medicine', 'purchaseOrderItem.purchaseOrder', 'receiver', 'pharmacy']);
        
        // Get related stock movements
        $stockMovements = StockMovement::where('medicine_batch_id', $medicineBatch->id)
            ->with('creator')
            ->latest()
            ->get();

        // Get current pharmacy stock
        $pharmacyStock = PharmacyStock::where('pharmacy_id', $medicineBatch->pharmacy_id)
            ->where('medicine_id', $medicineBatch->medicine_id)
            ->first();

        return view('in.pharmacy.medicine_batches.show', compact('medicineBatch', 'stockMovements', 'pharmacyStock'));
    }

    public function edit(MedicineBatch $medicineBatch)
    {
        $this->authorizeAccess($medicineBatch);

        $pharmacyId = session('active_pharmacy_id');

        $medicines = Medicine::query()
            ->when(Auth::user()->role !== 'super_admin', fn($q) => $q->where('pharmacy_id', $pharmacyId))
            ->get();

        $purchaseOrderItems = PurchaseOrderItem::query()
            ->with('purchaseOrder')
            ->when(Auth::user()->role !== 'super_admin', fn($q) => $q->whereHas('purchaseOrder', fn($q2) => $q2->where('pharmacy_id', $pharmacyId)))
            ->get();

        return view('in.pharmacy.medicine_batches.edit', compact('medicineBatch', 'medicines', 'purchaseOrderItems'));
    }

    public function update(Request $request, MedicineBatch $medicineBatch)
    {
        $this->authorizeAccess($medicineBatch);

        $validated = $request->validate([
            'medicine_id' => 'required|exists:medicines,id',
            'purchase_order_item_id' => 'nullable|exists:purchase_order_items,id',
            'batch_number' => 'nullable|string|max:255',
            'manufacture_date' => 'nullable|date|before_or_equal:today',
            'expiry_date' => 'nullable|date|after:today|after_or_equal:manufacture_date',
            'quantity_received' => 'required|integer|min:1',
            'quantity_available' => 'required|integer|min:0',
            'unit_cost' => 'required|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:unit_cost',
            'is_expired' => 'boolean',
        ]);

        // Validate quantity available doesn't exceed received minus sold
        $soldQuantity = $medicineBatch->quantity_received - $medicineBatch->quantity_available;
        $newAvailable = $validated['quantity_available'];
        
        if (($validated['quantity_received'] - $newAvailable) < $soldQuantity) {
            throw ValidationException::withMessages([
                'quantity_available' => 'Available quantity cannot be more than received minus already sold quantity.'
            ]);
        }

        $oldQuantityReceived = $medicineBatch->quantity_received;
        $oldQuantityAvailable = $medicineBatch->quantity_available;

        DB::transaction(function () use ($validated, $medicineBatch, $oldQuantityReceived, $oldQuantityAvailable) {
            // Calculate quantity difference
            $receivedDifference = $validated['quantity_received'] - $oldQuantityReceived;
            $availableDifference = $validated['quantity_available'] - $oldQuantityAvailable;

            // Update the batch
            $medicineBatch->update($validated);

            // Update pharmacy stock if quantities changed
            if ($receivedDifference !== 0 || $availableDifference !== 0) {
                $this->adjustPharmacyStock($medicineBatch, $receivedDifference, $availableDifference);
                
                // Record stock movement for the adjustment
                if ($availableDifference !== 0) {
                    $this->recordStockMovement([
                        'pharmacy_id' => $medicineBatch->pharmacy_id,
                        'medicine_id' => $medicineBatch->medicine_id,
                        'medicine_batch_id' => $medicineBatch->id,
                        'movement_type' => 'adjustment',
                        'quantity_change' => $availableDifference,
                        'reference_type' => 'batch_update',
                        'reference_id' => $medicineBatch->id,
                        'notes' => "Batch quantity adjusted from {$oldQuantityAvailable} to {$validated['quantity_available']}",
                        'created_by' => Auth::id()
                    ]);
                }
            }
        });

        return redirect()->route('medicine_batches.index')
            ->with('success', 'Medicine batch updated successfully.');
    }

    public function destroy(MedicineBatch $medicineBatch)
    {
        $this->authorizeAccess($medicineBatch);

        // Check if batch has been used in any sales
        if ($medicineBatch->quantity_available < $medicineBatch->quantity_received) {
            return redirect()->back()
                ->withErrors(['error' => 'Cannot delete batch that has been partially or fully sold.']);
        }

        DB::transaction(function () use ($medicineBatch) {
            // Reduce pharmacy stock
            $this->updatePharmacyStock($medicineBatch->medicine, $medicineBatch, 'decrease');

            // Record stock movement
            $this->recordStockMovement([
                'pharmacy_id' => $medicineBatch->pharmacy_id,
                'medicine_id' => $medicineBatch->medicine_id,
                'medicine_batch_id' => $medicineBatch->id,
                'movement_type' => 'adjustment',
                'quantity_change' => -$medicineBatch->quantity_available,
                'reference_type' => 'batch_deletion',
                'reference_id' => $medicineBatch->id,
                'notes' => "Batch deleted: {$medicineBatch->batch_number}",
                'created_by' => Auth::id()
            ]);

            // Delete the batch
            $medicineBatch->delete();
        });

        return redirect()->route('medicine_batches.index')
            ->with('success', 'Medicine batch deleted successfully.');
    }

    /**
     * Mark expired batches
     */
    public function markExpired()
    {
        $pharmacyId = Auth::user()->role !== 'super_admin' ? session('active_pharmacy_id') : null;

        $expiredBatches = MedicineBatch::where('expiry_date', '<=', now())
            ->where('is_expired', false)
            ->where('quantity_available', '>', 0)
            ->when($pharmacyId, fn($q) => $q->where('pharmacy_id', $pharmacyId))
            ->get();

        DB::transaction(function () use ($expiredBatches) {
            foreach ($expiredBatches as $batch) {
                // Mark as expired
                $batch->update(['is_expired' => true]);

                // Record expiry movement
                $this->recordStockMovement([
                    'pharmacy_id' => $batch->pharmacy_id,
                    'medicine_id' => $batch->medicine_id,
                    'medicine_batch_id' => $batch->id,
                    'movement_type' => 'expiry',
                    'quantity_change' => -$batch->quantity_available,
                    'reference_type' => 'expiry_check',
                    'reference_id' => $batch->id,
                    'notes' => "Batch expired on {$batch->expiry_date}",
                    'created_by' => Auth::id()
                ]);

                // Update pharmacy stock
                $stock = PharmacyStock::where('pharmacy_id', $batch->pharmacy_id)
                    ->where('medicine_id', $batch->medicine_id)
                    ->first();

                if ($stock) {
                    $stock->decrement('available_quantity', $batch->quantity_available);
                    $stock->decrement('total_quantity', $batch->quantity_available);
                }
            }
        });

        return redirect()->back()
            ->with('success', count($expiredBatches) . ' expired batches have been marked and stock updated.');
    }

    // Protected helper methods

    protected function authorizeAccess(MedicineBatch $batch)
    {
        if (Auth::user()->role !== 'super_admin' && $batch->pharmacy_id !== session('active_pharmacy_id')) {
            abort(403, 'Unauthorized access to this medicine batch.');
        }
    }

    protected function updatePharmacyStock(Medicine $medicine, MedicineBatch $batch, string $operation)
    {
        $stock = PharmacyStock::firstOrCreate(
            [
                'pharmacy_id' => $batch->pharmacy_id,
                'medicine_id' => $medicine->id
            ],
            [
                'total_quantity' => 0,
                'available_quantity' => 0,
                'average_cost' => 0,
                'default_selling_price' => $batch->selling_price
            ]
        );

        if ($operation === 'increase') {
            // Calculate new average cost
            $totalValue = ($stock->total_quantity * $stock->average_cost) + ($batch->quantity_received * $batch->unit_cost);
            $newTotalQuantity = $stock->total_quantity + $batch->quantity_received;
            $newAverageCost = $newTotalQuantity > 0 ? $totalValue / $newTotalQuantity : $batch->unit_cost;

            $stock->update([
                'total_quantity' => $newTotalQuantity,
                'available_quantity' => $stock->available_quantity + $batch->quantity_available,
                'average_cost' => $newAverageCost,
                'default_selling_price' => $stock->default_selling_price ?? $batch->selling_price
            ]);
        } else {
            // Decrease operation
            $stock->update([
                'total_quantity' => max(0, $stock->total_quantity - $batch->quantity_received),
                'available_quantity' => max(0, $stock->available_quantity - $batch->quantity_available)
            ]);
        }
    }

    protected function adjustPharmacyStock(MedicineBatch $batch, int $receivedDiff, int $availableDiff)
    {
        $stock = PharmacyStock::where('pharmacy_id', $batch->pharmacy_id)
            ->where('medicine_id', $batch->medicine_id)
            ->first();

        if ($stock) {
            $stock->increment('total_quantity', $receivedDiff);
            $stock->increment('available_quantity', $availableDiff);

            // Recalculate average cost if received quantity changed
            if ($receivedDiff !== 0) {
                $allBatches = MedicineBatch::where('pharmacy_id', $batch->pharmacy_id)
                    ->where('medicine_id', $batch->medicine_id)
                    ->get();

                $totalValue = $allBatches->sum(fn($b) => $b->quantity_received * $b->unit_cost);
                $totalQuantity = $allBatches->sum('quantity_received');

                if ($totalQuantity > 0) {
                    $stock->update(['average_cost' => $totalValue / $totalQuantity]);
                }
            }
        }
    }

    protected function recordStockMovement(array $data)
    {
        // Calculate quantity before if not provided
        if (!isset($data['quantity_before'])) {
            $stock = PharmacyStock::where('pharmacy_id', $data['pharmacy_id'])
                ->where('medicine_id', $data['medicine_id'])
                ->first();
            $data['quantity_before'] = $stock ? $stock->available_quantity - $data['quantity_change'] : 0;
        }

        $data['quantity_after'] = max(0, $data['quantity_before'] + $data['quantity_change']);

        StockMovement::create($data);
    }

    protected function updatePurchaseOrderItem(MedicineBatch $batch)
    {
        if ($batch->purchaseOrderItem) {
            $item = $batch->purchaseOrderItem;
            $item->increment('quantity_received', $batch->quantity_received);
            
            // Mark as completed if fully received
            if ($item->quantity_received >= $item->quantity_ordered) {
                $item->update(['status' => 'received']);
            }
        }
    }

    protected function getStockStatus(MedicineBatch $batch): string
    {
        if ($batch->is_expired) {
            return 'expired';
        }

        if ($batch->expiry_date && $batch->expiry_date <= now()->addMonths(3)) {
            return 'near_expiry';
        }

        if ($batch->quantity_available <= 0) {
            return 'out_of_stock';
        }

        if ($batch->quantity_available <= ($batch->quantity_received * 0.2)) {
            return 'low_stock';
        }

        return 'good';
    }
}