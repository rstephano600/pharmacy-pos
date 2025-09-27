<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'pharmacy_id',
        'name',
        'contact_person',
        'phone',
        'email',
        'address',
        'credit_days',
        'credit_limit',
        'payment_terms',
        'is_active',
    ];

    /**
     * Relationships
     */

    // Supplier belongs to a pharmacy
    public function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class);
    }

    // Optional: If you have purchases/orders tied to supplier
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    /**
     * Scopes
     */

    // Scope active suppliers
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope by payment terms
    public function scopeByPaymentTerms($query, $term)
    {
        return $query->where('payment_terms', $term);
    }

    /**
     * Accessors
     */

    public function getStatusLabelAttribute()
    {
        return $this->is_active ? 'Active' : 'Inactive';
    }

    public function getPaymentTermsLabelAttribute()
    {
        return ucfirst($this->payment_terms);
    }
}
