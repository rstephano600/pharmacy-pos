<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    //
    protected $fillable = [
        'expense_category_id',
        'amount',
        'note',
        'payment_method',
        'expense_date',
        'created_by'
    ];

    protected $casts = [
        'expense_date' => 'date',
    ];
    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
}
