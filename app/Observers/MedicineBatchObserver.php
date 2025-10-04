<?php

namespace App\Observers;

use App\Models\MedicineBatch;
use App\Models\PharmacyStock;

class MedicineBatchObserver
{
    public function created(MedicineBatch $batch): void
    {
        $pharmacyId = $batch->purchaseOrderItem->purchaseOrder->pharmacy_id;
        $medicineId = $batch->medicine_id;

        $stock = PharmacyStock::firstOrCreate(
            ['pharmacy_id' => $pharmacyId, 'medicine_id' => $medicineId],
            ['total_quantity' => 0, 'available_quantity' => 0, 'average_cost' => 0]
        );

        // update stock
        $stock->total_quantity += $batch->quantity_received;
        $stock->available_quantity += $batch->quantity_received;

        // weighted average cost
        if ($batch->unit_cost > 0) {
            $existingValue = $stock->average_cost * $stock->total_quantity;
            $newValue = $batch->unit_cost * $batch->quantity_received;
            $stock->average_cost = ($existingValue + $newValue) / max(1, $stock->total_quantity);
        }

        // update default selling price (fallback to batch selling price if provided)
        if ($batch->selling_price) {
            $stock->default_selling_price = $batch->selling_price;
        }

        $stock->save();
    }
}
