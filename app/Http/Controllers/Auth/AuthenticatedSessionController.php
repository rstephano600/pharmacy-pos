<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Pharmacy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // Find user by username or email
        $user = User::where('username', $request->username)
            ->orWhere('email', $request->username)
            ->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'username' => __('auth.failed'),
            ]);
        }

        // Check if user is active
        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'username' => __('Your account has been deactivated. Please contact administrator.'),
            ]);
        }

        // Credentials for Auth
        $credentials = filter_var($request->username, FILTER_VALIDATE_EMAIL)
            ? ['email' => $request->username, 'password' => $request->password]
            : ['username' => $request->username, 'password' => $request->password];

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'username' => __('auth.failed'),
            ]);
        }

        $request->session()->regenerate();

        // Update last login
        $user->update([
            'last_login' => now(),
        ]);

        // 🚩 Step 2 & 3: Handle pharmacy associations
        $pharmacies = Pharmacy::whereHas('users', function ($q) use ($user) {
            $q->where('id', $user->id);
        })->get();

        if ($pharmacies->count() > 1) {
            // Store available pharmacies in session temporarily
            session(['pharmacy_choices' => $pharmacies]);

            return redirect()->route('auth.choose.pharmacy')->with('info', 'Please select a pharmacy to continue.');
        }

        if ($pharmacies->count() === 1) {
            session(['active_pharmacy_id' => $pharmacies->first()->id]);
        }

        // 🚩 Step 4: Redirect user based on role
        return $this->redirectToDashboard($user);
    }

    /**
     * Redirect user to appropriate dashboard based on role.
     */
    protected function redirectToDashboard(User $user): RedirectResponse
    {
        switch ($user->role) {
            case User::ROLE_SUPER_ADMIN:
                return redirect()->route('admin.dashboard')->with('success', 'Welcome Super Admin!');

            case User::ROLE_PHARMACY_OWNER:
                return redirect()->route('pharmacy.owner.dashboard')->with('success', 'Welcome Pharmacy Owner!');

            case User::ROLE_PHARMACIST:
            case User::ROLE_PHARMACY_TECHNICIAN:
            case User::ROLE_PHARMACY_DISPENSER:
            case User::ROLE_PHARMACY_CASHIER:
                return redirect()->route('pharmacy.staff.dashboard')->with('success', 'Welcome Pharmacy Staff!');

            case User::ROLE_USER:
                return redirect()->route('user.dashboard')->with('success', 'Welcome User!');

            case User::ROLE_CUSTOMER:
                return redirect()->route('customer.dashboard')->with('success', 'Welcome Customer!');

            default:
                return redirect()->route('home')->with('success', 'Welcome!');
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'You have been logged out successfully.');
    }
}
