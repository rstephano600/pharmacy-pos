<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;

class PrescriptionItem extends Model
{
    protected $fillable = [
        'prescription_id', 'medicine_id', 'quantity',
        'dosage', 'frequency', 'duration_days', 'status'
    ];

    public function prescription() {
        return $this->belongsTo(Prescription::class);
    }

    public function medicine() {
        return $this->belongsTo(Medicine::class);
    }
}

