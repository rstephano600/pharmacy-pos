<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicineBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'medicine_id',
        'purchase_order_item_id',
        'batch_number',
        'manufacture_date',
        'expiry_date',
        'quantity_received',
        'quantity_available',
        'unit_cost',
        'selling_price',
        'received_by',
        'is_expired',
    ];

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }

    public function purchaseOrderItem()
    {
        return $this->belongsTo(PurchaseOrderItem::class);
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}

