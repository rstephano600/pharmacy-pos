<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Pharmacy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PharmacyController extends Controller
{
    /**
     * Display a listing of the pharmacies.
     */
    public function index()
    {
        $pharmacies = Pharmacy::latest()->paginate(10);
        return view('in.superadmin.pharmacies.index', compact('pharmacies'));
    }

    /**
     * Show the form for creating a new pharmacy.
     */
    public function create()
    {
        return view('in.superadmin.pharmacies.create');
    }

    /**
     * Store a newly created pharmacy in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'license_number' => 'required|string|unique:pharmacies,license_number',
            'country'        => 'required|string|max:100',
            'region'         => 'required|string|max:100',
            'district'       => 'nullable|string|max:100',
            'location'       => 'nullable|string|max:255',
            'working_hours'  => 'nullable|string|max:255',
            'contact_phone'  => 'nullable|string|max:50',
            'contact_email'  => 'nullable|email|max:100',
            'license_expiry' => 'nullable|date',
            'pharmacy_logo'  => 'nullable|image|max:2048',
            'is_active'      => 'boolean',
        ]);

        if ($request->hasFile('pharmacy_logo')) {
            $validated['pharmacy_logo'] = $request->file('pharmacy_logo')->store('pharmacy_logos', 'public');
        }

        Pharmacy::create($validated);

        return redirect()->route('superadmin.pharmacies.index')
                         ->with('success', 'Pharmacy created successfully.');
    }

    /**
     * Display the specified pharmacy.
     */
    public function show(Pharmacy $pharmacy)
    {
        return view('in.superadmin.pharmacies.show', compact('pharmacy'));
    }

    /**
     * Show the form for editing the specified pharmacy.
     */
    public function edit(Pharmacy $pharmacy)
    {
        return view('in.superadmin.pharmacies.edit', compact('pharmacy'));
    }

    /**
     * Update the specified pharmacy in storage.
     */
    public function update(Request $request, Pharmacy $pharmacy)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'license_number' => 'required|string|unique:pharmacies,license_number,' . $pharmacy->id,
            'country'        => 'required|string|max:100',
            'region'         => 'required|string|max:100',
            'district'       => 'nullable|string|max:100',
            'location'       => 'nullable|string|max:255',
            'working_hours'  => 'nullable|string|max:255',
            'contact_phone'  => 'nullable|string|max:50',
            'contact_email'  => 'nullable|email|max:100',
            'license_expiry' => 'nullable|date',
            'pharmacy_logo'  => 'nullable|image|max:2048',
            'is_active'      => 'boolean',
        ]);

        if ($request->hasFile('pharmacy_logo')) {
            if ($pharmacy->pharmacy_logo && Storage::disk('public')->exists($pharmacy->pharmacy_logo)) {
                Storage::disk('public')->delete($pharmacy->pharmacy_logo);
            }
            $validated['pharmacy_logo'] = $request->file('pharmacy_logo')->store('pharmacy_logos', 'public');
        }

        $pharmacy->update($validated);

        return redirect()->route('superadmin.pharmacies.index')
                         ->with('success', 'Pharmacy updated successfully.');
    }

    /**
     * Remove the specified pharmacy from storage.
     */
    public function destroy(Pharmacy $pharmacy)
    {
        if ($pharmacy->pharmacy_logo && Storage::disk('public')->exists($pharmacy->pharmacy_logo)) {
            Storage::disk('public')->delete($pharmacy->pharmacy_logo);
        }

        $pharmacy->delete();

        return redirect()->route('superadmin.pharmacies.index')
                         ->with('success', 'Pharmacy deleted successfully.');
    }
}
