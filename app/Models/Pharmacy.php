<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;

class Pharmacy extends Model
{
    use HasFactory;
    // use SoftDeletes;

    protected $fillable = [
        'name',
        'license_number',
        'country',
        'region',
        'district',
        'location',
        'working_hours',
        'contact_phone',
        'contact_email',
        'license_expiry',
        'pharmacy_logo',
        'is_active',
    ];

    protected $casts = [
        'license_expiry' => 'date',
        'is_active' => 'boolean',
    ];

    protected $attributes = [
        'pharmacy_logo' => 'default_pharmacy_logo.png',
        'is_active' => true,
    ];

    // ✅ Relationships
    // public function users(): HasMany
    // {
    //     return $this->hasMany(User::class, 'pharmacy_id');
    // }

public function users()
{
    return $this->belongsToMany(User::class, 'pharmacy_user', 'pharmacy_id', 'user_id');
}


    public function staff(): HasMany
    {
        return $this->users()->whereIn('role', [
            User::ROLE_PHARMACY_OWNER,
            User::ROLE_PHARMACIST,
            User::ROLE_PHARMACY_TECHNICIAN
        ]);
    }

    public function activeStaff(): HasMany
    {
        return $this->staff()->where('is_active', true);
    }

    // ✅ Business logic
    public function isLicenseExpired(): bool
    {
        return $this->license_expiry && $this->license_expiry->isPast();
    }

    // ✅ Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeValidLicense($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('license_expiry')
              ->orWhere('license_expiry', '>', now());
        });
    }

    // ✅ Mutators
    public function setLicenseNumberAttribute($value)
    {
        $this->attributes['license_number'] = strtoupper($value);
    }
}
