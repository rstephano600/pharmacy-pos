<?php

namespace App\Http\Controllers\Export;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Pharmacy;
use App\Models\Supplier;
use App\Models\Medicine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ExportPurchaseController extends Controller
{
    public function index( Request $request){
      
    }

    
    protected function authorizeAccess(PurchaseOrder $order)
    {
        $user = Auth::user();

        if ($user->role !== \App\Models\User::ROLE_SUPER_ADMIN &&
            $order->pharmacy_id !== session('active_pharmacy_id')) {
            abort(403, 'Unauthorized access to this purchase order.');
        }
    }
}
