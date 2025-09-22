<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicineCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'pharmacy_id',
        'category_name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * ✅ Relationships
     */
    public function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class);
    }

    public function medicines()
    {
        return $this->hasMany(Medicine::class, 'category_id');
    }

    /**
     * ✅ Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForPharmacy($query, $pharmacyId)
    {
        return $query->where('pharmacy_id', $pharmacyId);
    }

    /**
     * ✅ Business logic
     */
    public function toggleStatus(): void
    {
        $this->is_active = ! $this->is_active;
        $this->save();
    }
}
