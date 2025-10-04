<?php

namespace App\Http\Controllers\Pharmacy;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
    //

    protected function authorizeAccess(ExpenseCategory $expenseCategory){
        $user = Auth::User();

        if ($user->role !== ROLE_SUPER_ADMIN &&
        $expenseCategory->pharm!== session('active_pharmacy_id')) {
            abort(403, 'Unauthorized access to this purchase order.');
        }
    }
}
