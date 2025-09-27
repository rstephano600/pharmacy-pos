<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\PharmacyChoiceController;
use App\Http\Controllers\Auth\PharmacyController;

// Authentication Routes
Route::middleware('guest')->group(function () {
    // Registration
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);

    // Login
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    // Forgot Password
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');

    // Reset Password
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.update');
});

// Protected Routes
Route::middleware('auth')->group(function () {
    // Email Verification
    Route::get('verify-email', EmailVerificationPromptController::class)->name('verification.notice');

    // Logout
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

Route::get('/choose-pharmacy', [PharmacyChoiceController::class, 'show'])->name('auth.choose.pharmacy');
Route::post('/choose-pharmacy', [PharmacyChoiceController::class, 'store'])->name('auth.choose.pharmacy.store');




// Dashboard Route (example)
Route::get('/dashboard', function () {
    return view('in.user.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\AdminController;

// Protected Routes for authenticated users
Route::middleware('auth')->group(function () {

    // Default dashboard route (will redirect based on role)
    Route::get('/dashboard', [DashboardController::class, 'redirectToRoleDashboard'])
         ->name('dashboard');
    
    // User Dashboard (for regular users)
    Route::get('/user/dashboard', [DashboardController::class, 'userDashboard'])
         ->name('user.dashboard');
    
    // User Pharmacy Dashboard (for users associated with a pharmacy)
    Route::get('/user/pharmacy/dashboard', [DashboardController::class, 'userPharmacyDashboard'])
         ->name('user.pharmacy.dashboard');
    Route::get('/pharmacy/staff/dashboard', [DashboardController::class, 'pharmacyStaffDashboard'])
             ->name('pharmacy.staff.dashboard');
    Route::get('/pharmacy/owner/dashboard', [DashboardController::class, 'pharmacyOwnerDashboard'])
             ->name('pharmacy.owner.dashboard');
    
    // Pharmacy Staff Dashboard (for pharmacists and technicians)
    // Route::middleware('pharmacy.associated')->group(function () {
    //     Route::get('/pharmacy/staff/dashboard', [DashboardController::class, 'pharmacyStaffDashboard'])
    //          ->name('pharmacy.staff.dashboard');
    // });
    
    // Pharmacy Owner Dashboard
    // Route::middleware('pharmacy.associated')->group(function () {
    //     Route::get('/pharmacy/owner/dashboard', [DashboardController::class, 'pharmacyOwnerDashboard'])
    //          ->name('pharmacy.owner.dashboard');
    // });
    
    // Admin Dashboard
    Route::middleware('admin')->group(function () {
        Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])
             ->name('admin.dashboard');
        
        // Additional admin routes
        Route::prefix('admin')->group(function () {
            Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
            Route::get('/pharmacies', [AdminController::class, 'pharmacies'])->name('admin.pharmacies');
            Route::get('/reports', [AdminController::class, 'reports'])->name('admin.reports');
        });
    });
    
    // Super Admin Dashboard (can use same as admin or separate)
    Route::middleware('super.admin')->group(function () {
        Route::get('/super-admin/dashboard', [AdminController::class, 'superAdminDashboard'])
             ->name('super.admin.dashboard');
    });
    
    // Pharmacy Association Routes
    Route::prefix('pharmacy')->group(function () {
        Route::get('/association', [PharmacyController::class, 'showAssociationForm'])
             ->name('pharmacy.association');
        
        Route::post('/association', [PharmacyController::class, 'associatePharmacy'])
             ->name('pharmacy.associate');
        
        Route::get('/setup', [PharmacyController::class, 'showSetupForm'])
             ->name('pharmacy.setup');
        
        Route::post('/setup', [PharmacyController::class, 'setupPharmacy'])
             ->name('pharmacy.create');
    });
    
    // Redirect root to appropriate dashboard
    Route::redirect('/', '/dashboard');
});


use App\Http\Controllers\SuperAdmin\PharmacyController as SuperAdminPharmacyController;

Route::middleware(['auth', 'role:super_admin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::resource('pharmacies', SuperAdminPharmacyController::class);
});

use App\Http\Controllers\SuperAdmin\UserController;

Route::prefix('superadmin')->name('superadmin.')->group(function () {
    Route::resource('users', UserController::class);
});
use App\Http\Controllers\Pharmacy\MedicineCategoryController;

Route::middleware(['auth'])->group(function () {
    Route::resource('medicine-categories', MedicineCategoryController::class);
});


use App\Http\Controllers\Pharmacy\MedicineController;

// Medicine Management Routes
Route::middleware(['auth'])->group(function () {
    
    // Standard Resource Routes
    Route::resource('medicines', MedicineController::class);
    
    // Additional Custom Routes
    Route::prefix('medicines')->name('medicines.')->group(function () {
        
        // Toggle medicine status (activate/deactivate)
        Route::post('{medicine}/toggle-status', [MedicineController::class, 'toggleStatus'])
            ->name('toggle-status');
        
        // Export medicines to CSV
        Route::get('export/csv', [MedicineController::class, 'export'])
            ->name('export');
        
        // API endpoints for AJAX requests
        Route::prefix('api')->name('api.')->group(function () {
            Route::get('/', [MedicineController::class, 'apiIndex'])
                ->name('index');
            
            Route::get('{medicine}', [MedicineController::class, 'apiShow'])
                ->name('show');
        });
    });
});


Route::resource('suppliers', \App\Http\Controllers\Pharmacy\SupplierController::class);
Route::patch('suppliers/{supplier}/toggle-status', [\App\Http\Controllers\Pharmacy\SupplierController::class, 'toggleStatus'])
    ->name('suppliers.toggleStatus');

use App\Http\Controllers\Pharmacy\PurchaseOrderController;
use App\Http\Controllers\Pharmacy\MedicineBatchController;

// Purchase Orders CRUD
Route::middleware(['auth'])->group(function () {
    Route::resource('purchase_orders', PurchaseOrderController::class);
});
Route::resource('medicine_batches', MedicineBatchController::class)->middleware('auth');
Route::resource('customers', \App\Http\Controllers\Pharmacy\CustomerController::class);
Route::resource('prescriptions', \App\Http\Controllers\Pharmacy\PrescriptionController::class);
Route::resource('prescription-items', \App\Http\Controllers\Pharmacy\PrescriptionItemController::class);

