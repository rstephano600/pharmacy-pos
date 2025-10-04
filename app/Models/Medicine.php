<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Medicine extends Model
{
    use HasFactory;

    protected $fillable = [
        'pharmacy_id',
        'name',
        'generic_name',
        'brand_name',
        'category_id',
        'strength',
        'form',
        'prescription_type',
        'storage_type',
        'barcode',
        'unit_price',
        'reorder_level',
        'is_active',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'reorder_level' => 'integer',
        'is_active' => 'boolean',
    ];

    // Medicine Forms Enum
    const FORMS = [
        'tablet' => 'Tablet',
        'capsule' => 'Capsule',
        'syrup' => 'Syrup',
        'injection' => 'Injection',
        'cream' => 'Cream',
        'drops' => 'Drops',
        'powder' => 'Powder',
        'granules' => 'Granules',
        'pills' => 'Pills',
        'lozenges' => 'Lozenges/Troches',
        'suppositories' => 'Suppositories',
        'patches' => 'Patches',
        'solutions' => 'Solutions',
        'suspensions' => 'Suspensions',
        'emulsions' => 'Emulsions',
        'elixirs' => 'Elixirs',
        'tinctures' => 'Tinctures',
        'liniments' => 'Liniments',
        'ointments' => 'Ointments',
        'gels' => 'Gels',
        'pastes' => 'Pastes',
        'foams' => 'Foams',
        'inhalers' => 'Inhalers',
        'nebulizer_solutions' => 'Nebulizer Solutions',
        'nasal_sprays' => 'Nasal Sprays',
        'aerosols' => 'Aerosols',
        'implants' => 'Implants',
        'pellets' => 'Pellets',
        'films' => 'Films',
        'chewable_tablets' => 'Chewable Tablets',
        'sublingual_tablets' => 'Sublingual Tablets',
        'buccal_tablets' => 'Buccal Tablets',
    ];

    // Prescription Types Enum
    const PRESCRIPTION_TYPES = [
        'prescription' => 'Prescription (Rx)',
        'over_the_counter' => 'Over The Counter (OTC)',
        'controlled_substance' => 'Controlled Substance',
        'behind_the_counter' => 'Behind The Counter (BTC)',
    ];

    // Storage Types Enum
    const STORAGE_TYPES = [
        'room_temperature' => 'Room Temperature (15-25°C)',
        'refrigerated' => 'Refrigerated (2-8°C)',
        'frozen' => 'Frozen (-20°C)',
        'cool_dry_place' => 'Cool & Dry Place',
        'protect_from_light' => 'Protect from Light',
        'controlled_temperature' => 'Controlled Temperature',
        'do_not_freeze' => 'Do Not Freeze',
        'store_upright' => 'Store Upright',
    ];

    /**
     * Get the pharmacy that owns the medicine.
     */
    public function pharmacy(): BelongsTo
    {
        return $this->belongsTo(Pharmacy::class);
    }

    /**
     * Get the category that owns the medicine.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(MedicineCategory::class, 'category_id');
    }

    /**
     * Get the form display name.
     */
    public function getFormDisplayAttribute(): string
    {
        return self::FORMS[$this->form] ?? $this->form;
    }

    /**
     * Get the prescription type display name.
     */
    public function getPrescriptionTypeDisplayAttribute(): string
    {
        return self::PRESCRIPTION_TYPES[$this->prescription_type] ?? $this->prescription_type;
    }

    /**
     * Get the storage type display name.
     */
    public function getStorageTypeDisplayAttribute(): string
    {
        return self::STORAGE_TYPES[$this->storage_type] ?? $this->storage_type;
    }

    /**
     * Scope a query to only include active medicines.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include medicines by category.
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope a query to only include medicines by prescription type.
     */
    public function scopeByPrescriptionType($query, $prescriptionType)
    {
        return $query->where('prescription_type', $prescriptionType);
    }

    /**
     * Scope a query to only include medicines by form.
     */
    public function scopeByForm($query, $form)
    {
        return $query->where('form', $form);
    }

    /**
     * Scope a query to search medicines by name, generic name, or brand name.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('generic_name', 'like', "%{$search}%")
                  ->orWhere('brand_name', 'like', "%{$search}%");
        });
    }

    /**
     * Scope a query to only include medicines below reorder level.
     */
    public function scopeBelowReorderLevel($query)
    {
        return $query->where('current_stock', '<=', 'reorder_level');
    }

    /**
     * Check if medicine is below reorder level.
     */
    public function isBelowReorderLevel(): bool
    {
        return $this->current_stock <= $this->reorder_level;
    }

    /**
     * Get all available forms as options for dropdowns.
     */
    public static function getFormOptions(): array
    {
        return self::FORMS;
    }

    /**
     * Get all available prescription types as options for dropdowns.
     */
    public static function getPrescriptionTypeOptions(): array
    {
        return self::PRESCRIPTION_TYPES;
    }

    /**
     * Get all available storage types as options for dropdowns.
     */
    public static function getStorageTypeOptions(): array
    {
        return self::STORAGE_TYPES;
    }

    public function pharmacyStocks()
    {
        return $this->hasMany(PharmacyStock::class, 'medicine_id');
    }
}