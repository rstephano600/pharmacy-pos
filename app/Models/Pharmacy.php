<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // Optional, if you plan to use soft deletes

class Pharmacy extends Model
{
    use HasFactory;
    // use SoftDeletes; // Uncomment if you add soft deletes to the table

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
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

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'license_expiry' => 'date',
        'is_active' => 'boolean',
        // 'created_at' => 'datetime', // These are automatically cast by Eloquent
        // 'updated_at' => 'datetime',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'pharmacy_logo' => 'default_pharmacy_logo.png',
        'is_active' => true,
    ];

    /**
     * Interact with the pharmacy's license number.
     * Ensures the license number is stored in uppercase format.
     */
    public function setLicenseNumberAttribute($value)
    {
        $this->attributes['license_number'] = strtoupper($value);
    }
}