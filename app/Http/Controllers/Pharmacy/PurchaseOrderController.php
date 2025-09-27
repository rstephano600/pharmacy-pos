<?php

namespace App\Http\Controllers\Pharmacy;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Pharmacy;
use App\Models\Supplier;
use App\Models\Medicine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseOrderController extends Controller
{
    /**
     * List purchase orders
     */
    public function index(Request $request)
    {
        $query = PurchaseOrder::with(['pharmacy', 'supplier', 'orderedBy']);

        $user = Auth::user();

        if ($user->role !== \App\Models\User::ROLE_SUPER_ADMIN) {
            $activePharmacyId = session('active_pharmacy_id');
            $query->where('pharmacy_id', $activePharmacyId);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->latest()->paginate(15);

        return view('in.pharmacy.purchase_orders.index', compact('orders'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $user = Auth::user();

        $pharmacies = [];
        if ($user->role === \App\Models\User::ROLE_SUPER_ADMIN) {
            $pharmacies = Pharmacy::all();
        }

        $suppliers = Supplier::query()
            ->when($user->role !== \App\Models\User::ROLE_SUPER_ADMIN, function ($q) {
                $q->where('pharmacy_id', session('active_pharmacy_id'));
            })
            ->get();
        $medicines = Medicine::query()
            ->when($user->role !== \App\Models\User::ROLE_SUPER_ADMIN, function ($q) {
                $q->where('pharmacy_id', session('active_pharmacy_id'));
            })
            ->get();

        return view('in.pharmacy.purchase_orders.create', compact('pharmacies', 'suppliers', 'medicines'));
    }

    /**
     * Store purchase order + items
     */
    public function store(Request $request)
{
    $user = Auth::user();

    $validated = $request->validate([
        'pharmacy_id' => $user->role === \App\Models\User::ROLE_SUPER_ADMIN
            ? 'required|exists:pharmacies,id'
            : 'nullable',
        'supplier_id' => 'nullable|exists:suppliers,id',
        // remove po_number from validation, it will be auto-generated
        'order_date' => 'required|date',
        'expected_delivery_date' => 'nullable|date|after_or_equal:order_date',
        'status' => 'in:pending,partial,received,cancelled',
        'total_amount' => 'numeric|min:0',
        'payment_terms' => 'nullable|string|max:255',
        'notes' => 'nullable|string',
        'delivery_address' => 'nullable|string',
        'items' => 'required|array|min:1',
        'items.*.medicine_id' => 'required|exists:medicines,id',
        'items.*.quantity_ordered' => 'required|integer|min:1',
        'items.*.unit_cost' => 'required|numeric|min:0',
    ]);

    if ($user->role !== \App\Models\User::ROLE_SUPER_ADMIN) {
        $validated['pharmacy_id'] = session('active_pharmacy_id');
    }

    $validated['ordered_by'] = $user->id;

    // Generate unique PO number (e.g. PO-20250923-XYZ123)
    do {
        $poNumber = 'PO-' . date('Ymd') . '-' . strtoupper(uniqid());
    } while (PurchaseOrder::where('po_number', $poNumber)->exists());

    $validated['po_number'] = $poNumber;

    // Create purchase order
    $purchaseOrder = PurchaseOrder::create($validated);

    // Attach items
    $totalAmount = 0;
    foreach ($request->items as $item) {
        $lineTotal = $item['quantity_ordered'] * $item['unit_cost'];
        $purchaseOrder->items()->create([
            'medicine_id'       => $item['medicine_id'],
            'quantity_ordered'  => $item['quantity_ordered'],
            'unit_cost'         => $item['unit_cost'],
            'total_cost'        => $lineTotal,
        ]);
        $totalAmount += $lineTotal;
    }

    $purchaseOrder->update(['total_amount' => $totalAmount]);

    return redirect()->route('purchase_orders.index')
        ->with('success', "Purchase order {$purchaseOrder->po_number} created successfully.");
}

    /**
     * Show purchase order
     */
public function show(PurchaseOrder $purchase_order)
{
    $this->authorizeAccess($purchase_order);

    $purchase_order->load(['items.medicine', 'supplier', 'pharmacy', 'orderedBy']);

    // Rename variable for Blade
    $purchaseOrder = $purchase_order;

    return view('in.pharmacy.purchase_orders.show', compact('purchaseOrder'));
}

    /**
     * Edit form
     */
    public function edit(PurchaseOrder $purchase_order)
    {
        $this->authorizeAccess($purchase_order);

        $user = Auth::user();

        $pharmacies = [];
        if ($user->role === \App\Models\User::ROLE_SUPER_ADMIN) {
            $pharmacies = Pharmacy::all();
        }

        $suppliers = Supplier::query()
            ->when($user->role !== \App\Models\User::ROLE_SUPER_ADMIN, function ($q) {
                $q->where('pharmacy_id', session('active_pharmacy_id'));
            })
            ->get();

        $medicines = Medicine::query()
            ->when($user->role !== \App\Models\User::ROLE_SUPER_ADMIN, function ($q) {
                $q->where('pharmacy_id', session('active_pharmacy_id'));
            })
            ->get();

        $purchase_order->load('items.medicine');
            // Rename variable for Blade
    $purchaseOrder = $purchase_order;

        return view('in.pharmacy.purchase_orders.edit', compact('purchaseOrder', 'pharmacies', 'suppliers', 'medicines'));
    }

    /**
     * Update purchase order + items
     */
    public function update(Request $request, PurchaseOrder $purchase_order)
    {
        $this->authorizeAccess($purchase_order);

        $user = Auth::user();

        $validated = $request->validate([
            'pharmacy_id' => $user->role === \App\Models\User::ROLE_SUPER_ADMIN
                ? 'required|exists:pharmacies,id'
                : 'nullable',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'order_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date|after_or_equal:order_date',
            'status' => 'in:pending,partial,received,cancelled',
            'total_amount' => 'numeric|min:0',
            'payment_terms' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'delivery_address' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.medicine_id' => 'required|exists:medicines,id',
            'items.*.quantity_ordered' => 'required|integer|min:1',
            'items.*.unit_cost' => 'required|numeric|min:0',
        ]);

        if ($user->role !== \App\Models\User::ROLE_SUPER_ADMIN) {
            $validated['pharmacy_id'] = session('active_pharmacy_id');
        }

        $purchase_order->update($validated);

        // Reset and re-add items
        $purchase_order->items()->delete();

        $totalAmount = 0;
        foreach ($request->items as $item) {
            $lineTotal = $item['quantity_ordered'] * $item['unit_cost'];
            $purchase_order->items()->create([
                'medicine_id'       => $item['medicine_id'],
                'quantity_ordered'  => $item['quantity_ordered'],
                'unit_cost'         => $item['unit_cost'],
                'total_cost'        => $lineTotal,
            ]);
            $totalAmount += $lineTotal;
        }

        $purchase_order->update(['total_amount' => $totalAmount]);

        return redirect()->route('purchase_orders.index')
            ->with('success', 'Purchase order updated successfully.');
    }

    /**
     * Delete purchase order
     */
    public function destroy(PurchaseOrder $purchase_order)
    {
        $this->authorizeAccess($purchase_order);

        $purchase_order->delete();

        return redirect()->route('purchase_orders.index')
            ->with('success', 'Purchase order deleted successfully.');
    }

    /**
     * Helper: check access
     */
    protected function authorizeAccess(PurchaseOrder $order)
    {
        $user = Auth::user();

        if ($user->role !== \App\Models\User::ROLE_SUPER_ADMIN &&
            $order->pharmacy_id !== session('active_pharmacy_id')) {
            abort(403, 'Unauthorized access to this purchase order.');
        }
    }
}
