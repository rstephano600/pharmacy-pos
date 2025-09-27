<?php

namespace App\Http\Controllers\Pharmacy;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\Pharmacy;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    /**
     * Display a listing of suppliers.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $query = Supplier::with('pharmacy');

        // Restrict to active pharmacy unless super admin
        if (!$user->hasRole('super_admin')) {
            $activePharmacyId = session('active_pharmacy_id');
            $query->where('pharmacy_id', $activePharmacyId);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('contact_person', 'like', "%{$request->search}%")
                    ->orWhere('phone', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        if ($request->filled('payment_terms')) {
            $query->where('payment_terms', $request->payment_terms);
        }

        $suppliers = $query->latest()->paginate(15)->withQueryString();

        return view('in.pharmacy.suppliers.index', compact('suppliers'));
    }

    /**
     * Show create form
     */
    public function create(): View
    {
        $user = Auth::user();

        // Only super admins can choose pharmacy
        $pharmacies = $user->hasRole('super_admin')
            ? Pharmacy::active()->orderBy('name')->get()
            : [];

        return view('in.pharmacy.suppliers.create', compact('pharmacies'));
    }

    /**
     * Store supplier
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $rules = [
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'credit_days' => 'nullable|integer|min:0',
            'credit_limit' => 'nullable|numeric|min:0',
            'payment_terms' => 'required|in:cash,credit,cod,other',
            'is_active' => 'boolean',
        ];

        // Only super admin can manually assign pharmacy_id
        if ($user->hasRole('super_admin')) {
            $rules['pharmacy_id'] = 'required|exists:pharmacies,id';
        }

        $validated = $request->validate($rules);

        if (!$user->hasRole('super_admin')) {
            $validated['pharmacy_id'] = session('active_pharmacy_id');
        }

        Supplier::create($validated);

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier created successfully.');
    }

    /**
     * Show supplier
     */
    public function show(Supplier $supplier): View
    {
        $this->authorizeSupplier($supplier);
        $supplier->load('pharmacy');
        return view('in.pharmacy.suppliers.show', compact('supplier'));
    }

    /**
     * Show edit form
     */
    public function edit(Supplier $supplier): View
    {
        $this->authorizeSupplier($supplier);

        $user = Auth::user();
        $pharmacies = $user->hasRole('super_admin')
            ? Pharmacy::active()->orderBy('name')->get()
            : [];

        return view('in.pharmacy.suppliers.edit', compact('supplier', 'pharmacies'));
    }

    /**
     * Update supplier
     */
    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        $this->authorizeSupplier($supplier);

        $user = Auth::user();
        $rules = [
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'credit_days' => 'nullable|integer|min:0',
            'credit_limit' => 'nullable|numeric|min:0',
            'payment_terms' => 'required|in:cash,credit,cod,other',
            'is_active' => 'boolean',
        ];

        if ($user->hasRole('super_admin')) {
            $rules['pharmacy_id'] = 'required|exists:pharmacies,id';
        }

        $validated = $request->validate($rules);

        if (!$user->hasRole('super_admin')) {
            $validated['pharmacy_id'] = session('active_pharmacy_id');
        }

        $supplier->update($validated);

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier updated successfully.');
    }

    /**
     * Delete supplier
     */
    public function destroy(Supplier $supplier): RedirectResponse
    {
        $this->authorizeSupplier($supplier);

        try {
            $supplier->delete();
            return redirect()->route('suppliers.index')
                ->with('success', 'Supplier deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Unable to delete supplier. It may be linked to other records.');
        }
    }

    /**
     * Toggle active status
     */
    public function toggleStatus(Supplier $supplier): RedirectResponse
    {
        $this->authorizeSupplier($supplier);

        $supplier->update(['is_active' => !$supplier->is_active]);
        $status = $supplier->is_active ? 'activated' : 'deactivated';

        return redirect()->back()->with('success', "Supplier {$status} successfully.");
    }

    /**
     * Ensure supplier belongs to active pharmacy (unless super admin).
     */
    private function authorizeSupplier(Supplier $supplier)
    {
        $user = Auth::user();

        if ($user->hasRole('super_admin')) {
            return true;
        }

        if ($supplier->pharmacy_id !== session('active_pharmacy_id')) {
            abort(403, 'Unauthorized access to this supplier.');
        }
    }
}
