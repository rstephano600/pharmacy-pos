<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pharmacy;
use App\Models\User;

class DashboardController extends Controller
{
    /**
     * Redirect user to appropriate dashboard based on role
     */
    public function redirectToRoleDashboard()
    {
        $user = Auth::user();
        $activePharmacy = $this->getActivePharmacy();

        switch ($user->role) {
            case User::ROLE_SUPER_ADMIN:
                return redirect()->route('super.admin.dashboard');
                
            case User::ROLE_ADMIN:
                return redirect()->route('admin.dashboard');
                
            case User::ROLE_PHARMACY_OWNER:
                if ($activePharmacy) {
                    return redirect()->route('pharmacy.owner.dashboard');
                }
                return redirect()->route('auth.choose.pharmacy');
                
            case User::ROLE_PHARMACIST:
            case User::ROLE_PHARMACY_TECHNICIAN:
                if ($activePharmacy) {
                    return redirect()->route('pharmacy.staff.dashboard');
                }
                return redirect()->route('auth.choose.pharmacy');
                
            case User::ROLE_USER:
            default:
                if ($activePharmacy) {
                    return redirect()->route('user.pharmacy.dashboard');
                }
                return redirect()->route('user.dashboard');
        }
    }

    /**
     * Show user dashboard (for regular users without pharmacy association)
     */
    public function userDashboard()
    {
        $user = Auth::user();

        return view('dashboards.user', [
            'user' => $user,
            'pharmacy' => null,
        ]);
    }

    /**
     * Show user pharmacy dashboard (for users associated with a pharmacy)
     */
    public function userPharmacyDashboard()
    {
        $user = Auth::user();
        $pharmacy = $this->getActivePharmacy();

        if (!$pharmacy) {
            return redirect()->route('user.dashboard')
                ->with('warning', 'You are not associated with any pharmacy or none is selected.');
        }

        return view('dashboards.user-pharmacy', compact('user', 'pharmacy'));
    }

    /**
     * Show pharmacy staff dashboard (for pharmacists and technicians)
     */
    public function pharmacyStaffDashboard()
    {
        $user = Auth::user();
        $pharmacy = $this->getActivePharmacy();

        if (!$pharmacy) {
            return redirect()->route('auth.choose.pharmacy')
                ->with('warning', 'Please select a pharmacy first.');
        }

        $todayStats = [
            'prescriptions' => 24,
            'patients' => 18,
            'pending' => 5,
            'completed' => 19
        ];

        return view('in.dashboards.pharmacy-staff', compact('user', 'pharmacy', 'todayStats'));
    }

    /**
     * Show pharmacy owner dashboard
     */
    public function pharmacyOwnerDashboard()
    {
        $user = Auth::user();
        $pharmacy = $this->getActivePharmacy();

        if (!$pharmacy) {
            return redirect()->route('auth.choose.pharmacy')
                ->with('warning', 'Please select a pharmacy first.');
        }

        $staffCount = $pharmacy->users()
            ->whereIn('role', [User::ROLE_PHARMACIST, User::ROLE_PHARMACY_TECHNICIAN])
            ->count();

        $monthlyStats = [
            'revenue' => 12500,
            'prescriptions' => 324,
            'new_patients' => 45,
            'inventory_value' => 28750
        ];

        return view('in.dashboards.pharmacy-owner', compact('user', 'pharmacy', 'staffCount', 'monthlyStats'));
    }

    /**
     * Helper: get the currently active pharmacy from session
     */
    protected function getActivePharmacy()
    {
        $pharmacyId = session('active_pharmacy_id');
        if (!$pharmacyId) {
            return null;
        }

        return Pharmacy::find($pharmacyId);
    }
}
