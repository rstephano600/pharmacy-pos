<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Pharmacy;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index()
    {
        $users = User::with('pharmacy')->latest()->paginate(10);
        return view('in.superadmin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $roles = User::getRoles();
        $pharmacies = Pharmacy::orderBy('name')->get();
        return view('in.superadmin.users.create', compact('roles', 'pharmacies'));
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
       $validated = $request->validate([
    'full_name'   => 'required|string|max:255',
    'username'    => 'required|string|max:100|unique:users,username',
    'email'       => 'required|email|unique:users,email',
    'phone'       => 'nullable|string|max:20',
    'role'        => ['required', Rule::in(User::getRoles())],
    'password'    => 'required|string|min:6|confirmed',
    'pharmacies'  => 'nullable|array',
    'pharmacies.*'=> 'exists:pharmacies,id',
    'is_active'   => 'boolean',
]);

$user = User::create($validated);

// Attach pharmacies if selected
if(!empty($validated['pharmacies'])) {
    $user->pharmacies()->sync($validated['pharmacies']);
}


        return redirect()->route('superadmin.users.index')
                         ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $user->load('pharmacy');
        return view('in.superadmin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $roles = User::getRoles();
        $pharmacies = Pharmacy::orderBy('name')->get();
        return view('in.superadmin.users.edit', compact('user', 'roles', 'pharmacies'));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'username'  => ['required', 'string', 'max:100', Rule::unique('users')->ignore($user->id)],
            'email'     => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone'     => 'nullable|string|max:20',
            'role'      => ['required', Rule::in(User::getRoles())],
            'password'  => 'nullable|string|min:6|confirmed',
            'pharmacy_id' => 'nullable|exists:pharmacies,id',
            'is_active'   => 'boolean',
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $user->update($validated);
        $user->pharmacies()->sync($request->input('pharmacies', []));

        return redirect()->route('superadmin.users.index')
                         ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('superadmin.users.index')
                         ->with('success', 'User deleted successfully.');
    }
}
