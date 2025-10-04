<?php

namespace App\Http\Controllers\Pharmacy;

use App\Http\Controllers\Controller;
use App\Models\MedicineBatch;
use App\Models\Medicine;
use App\Models\PurchaseOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MedicineBatchController extends Controller
{
    public function index()
    {
        $query = MedicineBatch::with(['medicine', 'purchaseOrderItem', 'receiver']);

        if (Auth::user()->role !== 'super_admin') {
            $query->whereHas('medicine', fn($q) => $q->where('pharmacy_id', session('active_pharmacy_id')));
        }

        $batches = $query->latest()->paginate(15);

        return view('in.pharmacy.medicine_batches.index', compact('batches'));
    }

    public function create()
    {
        $medicines = Medicine::query()
            ->when(Auth::user()->role !== 'super_admin', fn($q) => $q->where('pharmacy_id', session('active_pharmacy_id')))
            ->get();

        $purchaseOrderItems = PurchaseOrderItem::query()
            ->with('purchaseOrder')
            ->when(Auth::user()->role !== 'super_admin', fn($q) => $q->whereHas('purchaseOrder', fn($q2) => $q2->where('pharmacy_id', session('active_pharmacy_id'))))
            ->get();

        return view('in.pharmacy.medicine_batches.create', compact('medicines', 'purchaseOrderItems'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'medicine_id' => 'required|exists:medicines,id',
            'purchase_order_item_id' => 'nullable|exists:purchase_order_items,id',
            'batch_number' => 'nullable|string|max:255',
            'manufacture_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:manufacture_date',
            'quantity_received' => 'required|integer|min:1',
            'quantity_available' => 'nullable|integer|min:0',
            'unit_cost' => 'required|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
            'is_expired' => 'boolean',
        ]);

        $validated['quantity_available'] = $validated['quantity_available'] ?? $validated['quantity_received'];
        $validated['received_by'] = Auth::id();

        MedicineBatch::create($validated);

        return redirect()->route('medicine_batches.index')->with('success', 'Medicine batch recorded successfully.');
    }

    public function show(MedicineBatch $medicineBatch)
    {
        $this->authorizeAccess($medicineBatch);
        $medicineBatch->load(['medicine', 'purchaseOrderItem', 'receiver']);
        return view('in.pharmacy.medicine_batches.show', compact('medicineBatch'));
    }

    public function edit(MedicineBatch $medicineBatch)
    {
        $this->authorizeAccess($medicineBatch);

        $medicines = Medicine::query()
            ->when(Auth::user()->role !== 'super_admin', fn($q) => $q->where('pharmacy_id', session('active_pharmacy_id')))
            ->get();

        $purchaseOrderItems = PurchaseOrderItem::query()
            ->with('purchaseOrder')
            ->when(Auth::user()->role !== 'super_admin', fn($q) => $q->whereHas('purchaseOrder', fn($q2) => $q2->where('pharmacy_id', session('active_pharmacy_id'))))
            ->get();

        return view('in.pharmacy.medicine_batches.edit', compact('medicineBatch', 'medicines', 'purchaseOrderItems'));
    }

    public function update(Request $request, MedicineBatch $medicineBatch)
    {
        $this->authorizeAccess($medicineBatch);

        $validated = $request->validate([
            'medicine_id' => 'required|exists:medicines,id',
            'purchase_order_item_id' => 'nullable|exists:purchase_order_items,id',
            'batch_number' => 'nullable|string|max:255',
            'manufacture_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:manufacture_date',
            'quantity_received' => 'required|integer|min:1',
            'quantity_available' => 'nullable|integer|min:0',
            'unit_cost' => 'required|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
            'is_expired' => 'boolean',
        ]);

        $validated['quantity_available'] = $validated['quantity_available'] ?? $validated['quantity_received'];

        $medicineBatch->update($validated);

        return redirect()->route('medicine_batches.index')->with('success', 'Medicine batch updated successfully.');
    }

    public function destroy(MedicineBatch $medicineBatch)
    {
        $this->authorizeAccess($medicineBatch);
        $medicineBatch->delete();

        return redirect()->route('medicine_batches.index')->with('success', 'Medicine batch deleted successfully.');
    }

    protected function authorizeAccess(MedicineBatch $batch)
    {
        if (Auth::user()->role !== 'super_admin' && $batch->medicine->pharmacy_id !== session('active_pharmacy_id')) {
            abort(403, 'Unauthorized access to this medicine batch.');
        }
    }
}
