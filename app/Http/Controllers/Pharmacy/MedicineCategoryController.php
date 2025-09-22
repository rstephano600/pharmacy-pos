<?php

namespace App\Http\Controllers\Pharmacy;

use App\Http\Controllers\Controller;
use App\Models\MedicineCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MedicineCategoryController extends Controller
{
    /**
     * Display a listing of the categories.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('super_admin')) {
            // super admin can see all pharmacies
            $categories = MedicineCategory::with('pharmacy')->paginate(15);
        } else {
            $pharmacyId = session('active_pharmacy_id');
            if (! $pharmacyId) {
                return redirect()->route('choose-pharmacy')->withErrors('Please select a pharmacy first.');
            }

            $categories = MedicineCategory::where('pharmacy_id', $pharmacyId)->paginate(15);
        }

        return view('in.pharmacy.medicine_categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        $user = Auth::user();
        if (! $user->hasRole(['super_admin', 'pharmacy_admin', 'pharmacist'])) {
            abort(403, 'Unauthorized action.');
        }

        return view('in.pharmacy.medicine_categories.create');
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // super_admin must specify pharmacy_id
        if ($user->hasRole('super_admin')) {
            $request->validate([
                'pharmacy_id' => 'required|exists:pharmacies,id',
                'category_name'        => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
            ]);

            $pharmacyId = $request->pharmacy_id;
        } else {
            // for admins/staff → pharmacy comes from session
            $pharmacyId = session('active_pharmacy_id');
            if (! $pharmacyId) {
                return redirect()->route('choose-pharmacy')->withErrors('Please select a pharmacy first.');
            }

            $request->validate([
                'category_name'        => 'required|string|max:255|unique:medicine_categories,category_name,NULL,id,pharmacy_id,' . $pharmacyId,
                'description' => 'nullable|string|max:1000',
            ]);
        }

        MedicineCategory::create([
            'pharmacy_id' => $pharmacyId,
            'category_name'        => $request->category_name,
            'description' => $request->description,
        ]);

        return redirect()->route('medicine-categories.index')->with('success', 'Medicine category created successfully.');
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(MedicineCategory $medicineCategory)
    {
        $user = Auth::user();

        if (! $user->hasRole(['super_admin', 'pharmacy_admin'])) {
            abort(403, 'Unauthorized action.');
        }

        if (! $user->hasRole('super_admin')) {
            $pharmacyId = session('active_pharmacy_id');
            if ($medicineCategory->pharmacy_id != $pharmacyId) {
                abort(403, 'Unauthorized action.');
            }
        }

        return view('in.pharmacy.medicine_categories.edit', compact('medicineCategory'));
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, MedicineCategory $medicineCategory)
    {
        $user = Auth::user();

        if (! $user->hasRole(['super_admin', 'pharmacy_admin'])) {
            abort(403, 'Unauthorized action.');
        }

        if (! $user->hasRole('super_admin')) {
            $pharmacyId = session('active_pharmacy_id');
            if ($medicineCategory->pharmacy_id != $pharmacyId) {
                abort(403, 'Unauthorized action.');
            }
        } else {
            $pharmacyId = $medicineCategory->pharmacy_id;
        }

        $request->validate([
            'category_name'        => 'required|string|max:255|unique:medicine_categories,name,' . $medicineCategory->id . ',id,pharmacy_id,' . $pharmacyId,
            'description' => 'nullable|string|max:1000',
        ]);

        $medicineCategory->update([
            'category_name'        => $request->category_name,
            'description' => $request->description,
        ]);

        return redirect()->route('medicine_categories.index')->with('success', 'Medicine category updated successfully.');
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(MedicineCategory $medicineCategory)
    {
        $user = Auth::user();

        if (! $user->hasRole(['super_admin', 'pharmacy_admin'])) {
            abort(403, 'Unauthorized action.');
        }

        if (! $user->hasRole('super_admin')) {
            $pharmacyId = session('active_pharmacy_id');
            if ($medicineCategory->pharmacy_id != $pharmacyId) {
                abort(403, 'Unauthorized action.');
            }
        }

        $medicineCategory->delete();

        return redirect()->route('medicine_categories.index')->with('success', 'Medicine category deleted successfully.');
    }
}
