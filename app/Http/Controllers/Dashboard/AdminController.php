<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Pharmacy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Show admin dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Admin-specific data
        $stats = [
            'total_pharmacies' => Pharmacy::count(),
            'total_users' => User::count(),
            'active_pharmacies' => Pharmacy::where('is_active', true)->count(),
            'pending_pharmacies' => Pharmacy::where('is_active', false)->count(),
            'pharmacy_staff' => User::whereIn('role', [
                User::ROLE_PHARMACIST,
                User::ROLE_PHARMACY_TECHNICIAN,
                User::ROLE_PHARMACY_OWNER
            ])->count(),
        ];

        // Recent activities
        $recentPharmacies = Pharmacy::latest()->take(5)->get();
        $recentUsers = User::latest()->take(5)->get();

        return view('in.dashboards.admin', compact('user', 'stats', 'recentPharmacies', 'recentUsers'));
    }

    /**
     * Show super admin dashboard
     */
    public function superAdminDashboard()
    {
        $user = Auth::user();
        
        // Super admin specific data
        $stats = [
            'total_pharmacies' => Pharmacy::count(),
            'total_users' => User::count(),
            'active_pharmacies' => Pharmacy::where('is_active', true)->count(),
            'system_health' => $this->getSystemHealth(),
        ];

        return view('in.dashboards.super-admin', compact('user', 'stats'));
    }

    /**
     * Show users management page
     */
    public function users()
    {
        $users = User::with('pharmacy')->latest()->paginate(20);
        
        return view('in.admin.users', compact('users'));
    }

    /**
     * Show pharmacies management page
     */
    public function pharmacies()
    {
        $pharmacies = Pharmacy::withCount('users')->latest()->paginate(20);
        
        return view('in.admin.pharmacies', compact('pharmacies'));
    }

    /**
     * Show reports page
     */
    public function reports()
    {
        $reports = [
            'user_activity' => $this->generateUserActivityReport(),
            'pharmacy_performance' => $this->generatePharmacyPerformanceReport(),
        ];

        return view('admin.reports', compact('reports'));
    }

    /**
     * Get system health status (placeholder)
     */
    private function getSystemHealth()
    {
        return [
            'status' => 'healthy',
            'uptime' => '99.9%',
            'storage' => '65% used',
            'last_backup' => now()->subHours(2)->format('Y-m-d H:i:s')
        ];
    }

    /**
     * Generate user activity report (placeholder)
     */
    private function generateUserActivityReport()
    {
        return [
            'total_logins' => User::sum('login_count'),
            'active_today' => User::whereDate('last_login', today())->count(),
            'new_this_week' => User::whereBetween('created_at', [now()->startOfWeek(), now()])->count(),
        ];
    }

    /**
     * Generate pharmacy performance report (placeholder)
     */
    private function generatePharmacyPerformanceReport()
    {
        return [
            'total_pharmacies' => Pharmacy::count(),
            'active_pharmacies' => Pharmacy::where('is_active', true)->count(),
            'avg_staff_per_pharmacy' => Pharmacy::withCount('users')->get()->avg('users_count'),
        ];
    }
}