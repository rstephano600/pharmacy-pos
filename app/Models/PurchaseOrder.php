<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'pharmacy_id',
        'supplier_id',
        'po_number',
        'order_date',
        'expected_delivery_date',
        'status',
        'total_amount',
        'ordered_by',
        'payment_terms',
        'notes',
        'delivery_address',
    ];

    protected $casts = [
        'delivery_address' => 'array',
        'order_date' => 'date',
        'expected_delivery_date' => 'date',
    ];

    // 🔗 Relationships
    public function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function orderedBy()
    {
        return $this->belongsTo(User::class, 'ordered_by');
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }
    
}
