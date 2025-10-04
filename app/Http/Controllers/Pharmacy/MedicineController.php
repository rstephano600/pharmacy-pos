<?php

namespace App\Http\Controllers\Pharmacy;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMedicineRequest;
use App\Http\Requests\UpdateMedicineRequest;
use App\Models\Medicine;
use App\Models\MedicineCategory;
use App\Models\Pharmacy;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class MedicineController extends Controller
{
    /**
     * Display a listing of the medicines.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $user = auth()->user();

        $query = Medicine::with(['pharmacy', 'category']);

        // Restrict to active pharmacy for non-super admins
        if (!$user->hasRole('super_admin')) {
            $activePharmacyId = session('active_pharmacy_id');

            if (!$activePharmacyId) {
                return redirect()->route('choose-pharmacy')
                    ->with('warning', 'Please select a pharmacy first.');
            }

            $query->where('pharmacy_id', $activePharmacyId);
        }

        // Apply filters
        if ($request->filled('search')) {
            $query->search($request->search);
        }
        if ($request->filled('category_id')) {
            $query->byCategory($request->category_id);
        }
        if ($request->filled('prescription_type')) {
            $query->byPrescriptionType($request->prescription_type);
        }
        if ($request->filled('form')) {
            $query->byForm($request->form);
        }
        if ($request->filled('status')) {
            $request->status === 'active'
                ? $query->active()
                : $query->where('is_active', false);
        }
        if ($request->filled('low_stock')) {
            $query->belowReorderLevel();
        }

        $medicines = $query->latest()->paginate(15)->withQueryString();

        // Filter options
        $categories = MedicineCategory::orderBy('category_name')->get();
        $forms = Medicine::getFormOptions();
        $prescriptionTypes = Medicine::getPrescriptionTypeOptions();

        return view('in.pharmacy.medicines.index', compact(
            'medicines',
            'categories',
            'forms',
            'prescriptionTypes'
        ));
    }

    /**
     * Show the form for creating a new medicine.
     */
    public function create(): View|RedirectResponse
    {
        $user = auth()->user();

        if ($user->hasRole('super_admin')) {
            $pharmacies = Pharmacy::active()->orderBy('name')->get();
        } else {
            $activePharmacyId = session('active_pharmacy_id');
            if (!$activePharmacyId) {
                return redirect()->route('choose-pharmacy')
                    ->with('warning', 'Please select a pharmacy first.');
            }
            $pharmacies = Pharmacy::where('id', $activePharmacyId)->get();
        }

        $categories = MedicineCategory::orderBy('category_name')->get();
        $forms = Medicine::getFormOptions();
        $prescriptionTypes = Medicine::getPrescriptionTypeOptions();
        $storageTypes = Medicine::getStorageTypeOptions();

        return view('in.pharmacy.medicines.create', compact(
            'pharmacies',
            'categories',
            'forms',
            'prescriptionTypes',
            'storageTypes'
        ));
    }

    /**
     * Store a newly created medicine in storage.
     */
    public function store(StoreMedicineRequest $request): RedirectResponse
    {
        $user = auth()->user();

        // Enforce pharmacy ownership for non-super admins
        if (!$user->hasRole('super_admin')) {
            $activePharmacyId = session('active_pharmacy_id');
            if (!$activePharmacyId || $activePharmacyId != $request->pharmacy_id) {
                return back()->withErrors(['pharmacy_id' => 'Invalid pharmacy selection.']);
            }
        }

        $medicine = Medicine::create($request->validated());

        return redirect()
            ->route('medicines.show', $medicine)
            ->with('success', 'Medicine created successfully.');
    }

    /**
     * Display the specified medicine.
     */
    public function show(Medicine $medicine): View|RedirectResponse
    {
        $user = auth()->user();

        if (!$user->hasRole('super_admin') && $medicine->pharmacy_id != session('active_pharmacy_id')) {
            return redirect()->route('medicines.index')
                ->with('error', 'Unauthorized access to this medicine.');
        }

        $medicine->load(['pharmacy', 'category']);

        return view('in.pharmacy.medicines.show', compact('medicine'));
    }

    /**
     * Show the form for editing the specified medicine.
     */
    public function edit(Medicine $medicine): View|RedirectResponse
    {
        $user = auth()->user();

        if (!$user->hasRole('super_admin') && $medicine->pharmacy_id != session('active_pharmacy_id')) {
            return redirect()->route('medicines.index')
                ->with('error', 'Unauthorized access to this medicine.');
        }

        if ($user->hasRole('super_admin')) {
            $pharmacies = Pharmacy::active()->orderBy('name')->get();
        } else {
            $pharmacies = Pharmacy::where('id', $medicine->pharmacy_id)->get();
        }

        $categories = MedicineCategory::orderBy('category_name')->get();
        $forms = Medicine::getFormOptions();
        $prescriptionTypes = Medicine::getPrescriptionTypeOptions();
        $storageTypes = Medicine::getStorageTypeOptions();

        return view('in.pharmacy.medicines.edit', compact(
            'medicine',
            'pharmacies',
            'categories',
            'forms',
            'prescriptionTypes',
            'storageTypes'
        ));
    }

    /**
     * Update the specified medicine in storage.
     */
    public function update(UpdateMedicineRequest $request, Medicine $medicine): RedirectResponse
    {
        $user = auth()->user();

        if (!$user->hasRole('super_admin') && $medicine->pharmacy_id != session('active_pharmacy_id')) {
            return redirect()->route('medicines.index')
                ->with('error', 'Unauthorized update attempt.');
        }

        $medicine->update($request->validated());

        return redirect()
            ->route('medicines.show', $medicine)
            ->with('success', 'Medicine updated successfully.');
    }

    /**
     * Remove the specified medicine from storage.
     */

    public function getBatches($medicineId)
{
    $pharmacyId = session('active_pharmacy_id');

    $batches = \App\Models\Batch::where('medicine_id', $medicineId)
        ->where('pharmacy_id', $pharmacyId) // only active pharmacy
        ->whereDate('expiry_date', '>', now()) // only non-expired
        ->get(['id', 'batch_number', 'expiry_date', 'quantity']);

    return response()->json($batches);
}

    public function destroy(Medicine $medicine): RedirectResponse
    {
        $user = auth()->user();

        if (!$user->hasRole('super_admin') && $medicine->pharmacy_id != session('active_pharmacy_id')) {
            return redirect()->route('medicines.index')
                ->with('error', 'Unauthorized delete attempt.');
        }

        try {
            $medicine->delete();
            return redirect()
                ->route('medicines.index')
                ->with('success', 'Medicine deleted successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Unable to delete medicine. It may be referenced by other records.');
        }
    }

    /**
     * Toggle the active status of the specified medicine.
     */
    public function toggleStatus(Medicine $medicine): RedirectResponse
    {
        $user = auth()->user();

        if (!$user->hasRole('super_admin') && $medicine->pharmacy_id != session('active_pharmacy_id')) {
            return redirect()->route('medicines.index')
                ->with('error', 'Unauthorized action.');
        }

        $medicine->update(['is_active' => !$medicine->is_active]);

        $status = $medicine->is_active ? 'activated' : 'deactivated';
        
        return redirect()
            ->back()
            ->with('success', "Medicine {$status} successfully.");
    }
}
