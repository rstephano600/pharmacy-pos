<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'pharmacy_id',
        'name',
        'phone',
        'email',
        'customer_type',
        'insurance_provider',
        'insurance_number',
        'address',
        'demographics',
    ];

    protected $casts = [
        'address' => 'array',
        'demographics' => 'array',
    ];

    public function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class);
    }
}

