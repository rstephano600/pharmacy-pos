<?php

namespace App\Http\Controllers\Pharmacy;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\Customer;
use App\Models\Medicine;
use Illuminate\Support\Facades\Auth;

class PrescriptionController extends Controller
{
    public function index(Request $request)
    {
        $query = Prescription::with(['customer','pharmacy','creator'])
            ->when(!auth()->user()->hasRole('super_admin'), function($q) {
                $q->where('pharmacy_id', session('active_pharmacy_id'));
            });

        if ($search = $request->input('search')) {
            $query->whereHas('customer', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            })->orWhere('doctor_name','like',"%{$search}%");
        }

        $prescriptions = $query->latest()->paginate(10);

        return view('in.pharmacy.prescriptions.index', compact('prescriptions'));
    }

    public function create()
    {
                $user = Auth::user();

        $pharmacies = [];
        if ($user->role === \App\Models\User::ROLE_SUPER_ADMIN) {
            $pharmacies = Pharmacy::all();
        }
        $customers = Customer::query()
            ->when($user->role !== \App\Models\User::ROLE_SUPER_ADMIN, function ($q) {
                $q->where('pharmacy_id', session('active_pharmacy_id'));
            })
            ->get();
        $medicines = Medicine::query()
            ->when($user->role !== \App\Models\User::ROLE_SUPER_ADMIN, function ($q) {
                $q->where('pharmacy_id', session('active_pharmacy_id'));
            })
            ->get();

        return view('in.pharmacy.prescriptions.create', compact('pharmacies','customers','medicines'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'doctor_name' => 'nullable|string',
            'doctor_license' => 'nullable|string',
            'diagnosis' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'required|array',
            'items.*.medicine_id' => 'required|exists:medicines,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.dosage' => 'required|string',
            'items.*.frequency' => 'required|string',
            'items.*.duration_days' => 'required|integer|min:1',
        ]);

        $data['pharmacy_id'] = auth()->user()->hasRole('super_admin')
            ? $request->pharmacy_id
            : session('active_pharmacy_id');
        $data['created_by'] = auth()->id();

        $prescription = Prescription::create($data);

        foreach ($request->items as $item) {
            $prescription->items()->create($item);
        }

        return redirect()->route('prescriptions.show', $prescription)
            ->with('success','Prescription created with items!');
    }

    public function show(Prescription $prescription)
    {
        $this->authorizeAccess($prescription);
        $prescription->load(['items.medicine','customer','pharmacy']);
        return view('in.pharmacy.prescriptions.show', compact('prescription'));
    }

    public function edit(Prescription $prescription)
    {
        $this->authorizeAccess($prescription);
        $customers = Customer::all();
        $medicines = Medicine::all();
        $prescription->load('items.medicine');
        return view('in.pharmacy.prescriptions.edit', compact('prescription','customers','medicines'));
    }

    public function update(Request $request, Prescription $prescription)
    {
        $this->authorizeAccess($prescription);

        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'doctor_name' => 'nullable|string',
            'doctor_license' => 'nullable|string',
            'diagnosis' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'required|array',
            'items.*.medicine_id' => 'required|exists:medicines,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.dosage' => 'required|string',
            'items.*.frequency' => 'required|string',
            'items.*.duration_days' => 'required|integer|min:1',
        ]);

        $prescription->update($data);

        // Sync prescription items
        $prescription->items()->delete();
        foreach ($request->items as $item) {
            $prescription->items()->create($item);
        }

        return redirect()->route('prescriptions.show', $prescription)
            ->with('success','Prescription updated with items!');
    }

    public function destroy(Prescription $prescription)
    {
        $this->authorizeAccess($prescription);
        $prescription->delete();
        return back()->with('success','Prescription deleted!');
    }

    private function authorizeAccess($prescription)
    {
        if (!auth()->user()->hasRole('super_admin') &&
            $prescription->pharmacy_id != session('active_pharmacy_id')) {
            abort(403, 'Unauthorized');
        }
    }
}
