<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PharmacyStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'pharmacy_id',
        'medicine_id',
        'total_quantity',
        'available_quantity',
        'reserved_quantity',
        'minimum_stock_level',
        'average_cost',
        'default_selling_price',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class);
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }

    /**
     * Each stock entry may be referenced in many sale items.
     */
    public function saleItems()
    {
        return $this->hasMany(SaleItem::class, 'medicine_id', 'medicine_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes & Helpers
    |--------------------------------------------------------------------------
    */

    public function scopeLowStock($query)
    {
        return $query->whereColumn('available_quantity', '<=', 'minimum_stock_level');
    }

    public function isLowStock(): bool
    {
        return $this->available_quantity <= $this->minimum_stock_level;
    }

    public function hasAvailableStock(int $requiredQty): bool
    {
        return $this->available_quantity >= $requiredQty;
    }
}
