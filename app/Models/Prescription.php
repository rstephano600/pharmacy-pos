<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;

class Prescription extends Model
{
    protected $fillable = [
        'customer_id', 'pharmacy_id', 'doctor_name', 'doctor_license',
        'diagnosis', 'notes', 'status', 'created_by'
    ];

    public function customer() {
        return $this->belongsTo(Customer::class);
    }

    public function items() {
        return $this->hasMany(PrescriptionItem::class);
    }

    public function pharmacy() {
        return $this->belongsTo(Pharmacy::class);
    }

    public function creator() {
        return $this->belongsTo(User::class, 'created_by');
    }
}
