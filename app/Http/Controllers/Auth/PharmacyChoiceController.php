<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Pharmacy;
use Illuminate\Http\Request;

class PharmacyChoiceController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        $pharmacies = $user->pharmacies;

        if ($pharmacies->isEmpty()) {
            abort(403, 'No pharmacies are associated with your account.');
        }

        return view('auth.choose-pharmacy', compact('pharmacies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pharmacy_id' => 'required|exists:pharmacies,id',
        ]);

        $user = auth()->user();

        if (!$user->pharmacies()->where('pharmacy_id', $request->pharmacy_id)->exists()) {
            return back()->withErrors(['pharmacy_id' => 'Invalid pharmacy selected.']);
        }

        session(['active_pharmacy_id' => $request->pharmacy_id]);
        session()->forget('pharmacy_choices');

        return redirect()->route('dashboard')->with('success', 'Pharmacy selected successfully.');
    }
}
