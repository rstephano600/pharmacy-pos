<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Pharmacy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PharmacyController extends Controller
{
    /**
     * Show pharmacy association form
     */
    public function showAssociationForm()
    {
        $user = Auth::user();
        
        if ($user->hasPharmacy()) {
            return redirect()->route('pharmacy.staff.dashboard')
                ->with('info', 'You are already associated with a pharmacy.');
        }

        // Get available pharmacies for association
        $pharmacies = Pharmacy::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('in.pharmacy.association', compact('pharmacies'));
    }

    /**
     * Associate user with a pharmacy
     */
    public function associatePharmacy(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'pharmacy_id' => 'required|exists:pharmacies,id',
            'access_code' => 'sometimes|required|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $pharmacy = Pharmacy::findOrFail($request->pharmacy_id);

        // Check if pharmacy is active
        if (!$pharmacy->is_active) {
            return redirect()->back()
                ->with('error', 'This pharmacy is not active. Please contact the pharmacy owner.');
        }

        // Check access code if required (you can implement this logic)
        if ($pharmacy->requires_access_code && $pharmacy->access_code !== $request->access_code) {
            return redirect()->back()
                ->with('error', 'Invalid access code. Please contact the pharmacy owner.');
        }

        // Associate user with pharmacy
        $user->pharmacy_id = $pharmacy->id;
        $user->save();

        return redirect()->route('pharmacy.staff.dashboard')
            ->with('success', 'Successfully associated with ' . $pharmacy->name);
    }

    /**
     * Show pharmacy setup form
     */
    public function showSetupForm()
    {
        $user = Auth::user();
        
        if ($user->hasPharmacy()) {
            return redirect()->route('pharmacy.owner.dashboard')
                ->with('info', 'You already have a pharmacy setup.');
        }

        return view('in.pharmacy.setup');
    }

    /**
     * Setup new pharmacy
     */
    public function setupPharmacy(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'license_number' => 'required|string|unique:pharmacies,license_number',
            'country' => 'required|string|max:255',
            'region' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'location' => 'required|string|max:500',
            'contact_phone' => 'required|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            'working_hours' => 'nullable|string|max:255',
            'license_expiry' => 'required|date|after:today'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Create new pharmacy
        $pharmacy = Pharmacy::create([
            'name' => $request->name,
            'license_number' => $request->license_number,
            'country' => $request->country,
            'region' => $request->region,
            'district' => $request->district,
            'location' => $request->location,
            'contact_phone' => $request->contact_phone,
            'contact_email' => $request->contact_email,
            'working_hours' => $request->working_hours,
            'license_expiry' => $request->license_expiry,
            'is_active' => true // Auto-activate for now, can be changed to require admin approval
        ]);

        // Associate user as pharmacy owner
        $user->pharmacy_id = $pharmacy->id;
        $user->save();

        return redirect()->route('pharmacy.owner.dashboard')
            ->with('success', 'Pharmacy setup successfully! Welcome to your new pharmacy dashboard.');
    }
}
