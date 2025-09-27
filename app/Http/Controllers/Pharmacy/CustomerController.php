<?php
namespace App\Http\Controllers\Pharmacy;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Pharmacy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers (with filters & search).
     */
    public function index(Request $request)
    {
        $query = Customer::query();

        // Super admin: can see all, else filter by active pharmacy
        if (!auth()->user()->hasRole('super_admin')) {
            $query->where('pharmacy_id', session('active_pharmacy_id'));
        }

        // 🔎 Search filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('insurance_provider', 'like', "%{$search}%");
            });
        }

        if ($request->filled('customer_type')) {
            $query->where('customer_type', $request->customer_type);
        }

        $customers = $query->with('pharmacy')->latest()->paginate(10);

        return view('in.pharmacy.customers.index', compact('customers'));
    }

    /**
     * Show form for creating customer.
     */
    public function create()
    {
        $pharmacies = auth()->user()->hasRole('super_admin')
            ? Pharmacy::all()
            : null;

        return view('in.pharmacy.customers.create', compact('pharmacies'));
    }

    /**
     * Store a new customer.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pharmacy_id'       => 'nullable|exists:pharmacies,id',
            'name'              => 'required|string|max:255',
            'phone'             => 'nullable|string|max:20',
            'email'             => 'nullable|email|unique:customers,email',
            'customer_type'     => 'required|in:individual,hospital,clinic',
            'insurance_provider'=> 'nullable|string|max:255',
            'insurance_number'  => 'nullable|string|max:255',
            'address'           => 'nullable|json',
            'demographics'      => 'nullable|json',
        ]);

        // Force pharmacy_id for non-super admins
        if (!auth()->user()->hasRole('super_admin')) {
            $validated['pharmacy_id'] = session('active_pharmacy_id');
        }

        Customer::create($validated);

        return redirect()->route('customers.index')->with('success', 'Customer created successfully.');
    }

    /**
     * Show customer details.
     */
    public function show(Customer $customer)
    {
        $this->authorizeAccess($customer);

        return view('in.pharmacy.customers.show', compact('customer'));
    }

    /**
     * Show form for editing.
     */
    public function edit(Customer $customer)
    {
        $this->authorizeAccess($customer);

        $pharmacies = auth()->user()->hasRole('super_admin')
            ? Pharmacy::all()
            : null;

        return view('in.pharmacy.customers.edit', compact('customer', 'pharmacies'));
    }

    /**
     * Update a customer.
     */
    public function update(Request $request, Customer $customer)
    {
        $this->authorizeAccess($customer);

        $validated = $request->validate([
            'pharmacy_id'       => 'nullable|exists:pharmacies,id',
            'name'              => 'required|string|max:255',
            'phone'             => 'nullable|string|max:20',
            'email'             => 'nullable|email|unique:customers,email,' . $customer->id,
            'customer_type'     => 'required|in:individual,hospital,clinic',
            'insurance_provider'=> 'nullable|string|max:255',
            'insurance_number'  => 'nullable|string|max:255',
            'address'           => 'nullable|json',
            'demographics'      => 'nullable|json',
        ]);

        // Non-super admin: lock pharmacy_id
        if (!auth()->user()->hasRole('super_admin')) {
            $validated['pharmacy_id'] = session('active_pharmacy_id');
        }

        $customer->update($validated);

        return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
    }

    /**
     * Delete a customer.
     */
    public function destroy(Customer $customer)
    {
        $this->authorizeAccess($customer);

        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
    }

    /**
     * Check access for customer record.
     */
    private function authorizeAccess(Customer $customer)
    {
        if (!auth()->user()->hasRole('super_admin')) {
            if ($customer->pharmacy_id !== session('active_pharmacy_id')) {
                abort(403, 'Unauthorized access.');
            }
        }
    }
}
