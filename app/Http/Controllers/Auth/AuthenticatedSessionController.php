<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('username', $request->username)
            ->orWhere('email', $request->username)
            ->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'username' => __('auth.failed'),
            ]);
        }

        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'username' => 'Your account has been deactivated. Please contact administrator.',
            ]);
        }

        $credentials = filter_var($request->username, FILTER_VALIDATE_EMAIL)
            ? ['email' => $request->username, 'password' => $request->password]
            : ['username' => $request->username, 'password' => $request->password];

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'username' => __('auth.failed'),
            ]);
        }

        $request->session()->regenerate();

        $user->update(['last_login' => now()]);

        // Multi-pharmacy support
        $pharmacies = $user->pharmacies;

        if ($pharmacies->count() > 1) {
            session(['pharmacy_choices' => $pharmacies]);
            return redirect()->route('auth.choose.pharmacy')->with('info', 'Please select a pharmacy to continue.');
        }

        if ($pharmacies->count() === 1) {
            session(['active_pharmacy_id' => $pharmacies->first()->id]);
        }

        return redirect()->route('dashboard');
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('success', 'You have been logged out successfully.');
    }
}
