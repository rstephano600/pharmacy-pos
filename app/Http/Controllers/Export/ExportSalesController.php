<?php

namespace App\Http\Controllers\Export;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Pharmacy;
use App\Models\Customer;
use App\Models\Medicine;
use App\Models\MedicineBatch;
use App\Models\PharmacyStock;
use App\Models\StockMovement;
use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;


class ExportSalesController extends Controller
{
    //

    protected function authorizeAccess(Sale $sale){
        $user = Auth::User();
        if ($user->role != User::ROLE_SUPER_ADMIN && 
        $sale->pharmacy_id !== session('active_pharmacy_id')) {
            abort(403, 'Unauthorized access to this purchase order.');
        }
    }
}
