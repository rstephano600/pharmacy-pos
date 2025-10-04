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
// Route::resource('medicine_batches', MedicineBatchController::class)->middleware('auth');
Route::resource('customers', \App\Http\Controllers\Pharmacy\CustomerController::class);
Route::resource('prescriptions', \App\Http\Controllers\Pharmacy\PrescriptionController::class);
Route::resource('prescription-items', \App\Http\Controllers\Pharmacy\PrescriptionItemController::class);

// use App\Http\Controllers\Pharmacy\SaleController;

// Route::middleware(['auth'])->prefix('pharmacy')->name('sales.')->group(function () {
//     Route::get('sales', [SaleController::class, 'index'])->name('index');
//     Route::get('sales/create', [SaleController::class, 'create'])->name('create');
//     Route::post('sales', [SaleController::class, 'store'])->name('store');
//     Route::get('sales/{sale}', [SaleController::class, 'show'])->name('show');
//     Route::get('sales/{sale}/edit', [SaleController::class, 'edit'])->name('edit');
//     Route::put('sales/{sale}', [SaleController::class, 'update'])->name('update');
//     Route::delete('sales/{sale}', [SaleController::class, 'destroy'])->name('destroy');
// });


// Medicine Batch Routes
Route::middleware(['auth'])->group(function () {
    
    // Main CRUD routes for Medicine Batches
    Route::prefix('pharmacy/medicine-batches')->name('medicine_batches.')->group(function () {
        
        // Index - List all medicine batches
        Route::get('/', [MedicineBatchController::class, 'index'])->name('index');
        
        // Create - Show form to create new batch
        Route::get('/create', [MedicineBatchController::class, 'create'])->name('create');
        
        // Store - Save new batch
        Route::post('/', [MedicineBatchController::class, 'store'])->name('store');
        
        // Show - Display specific batch details
        Route::get('/{medicineBatch}', [MedicineBatchController::class, 'show'])->name('show');
        
        // Edit - Show form to edit existing batch
        Route::get('/{medicineBatch}/edit', [MedicineBatchController::class, 'edit'])->name('edit');
        
        // Update - Save edited batch
        Route::put('/{medicineBatch}', [MedicineBatchController::class, 'update'])->name('update');
        
        // Delete - Remove batch
        Route::delete('/{medicineBatch}', [MedicineBatchController::class, 'destroy'])->name('destroy');
        
        // Additional utility routes
        Route::post('/mark-expired', [MedicineBatchController::class, 'markExpired'])->name('mark_expired');
    });
    
    // API routes for AJAX requests
    Route::prefix('api/pharmacy/medicine-batches')->name('api.medicine_batches.')->group(function () {
        
        // Get batches by medicine ID (for dropdowns/selects)
        Route::get('/by-medicine/{medicineId}', [MedicineBatchController::class, 'getBatchesByMedicine'])->name('by_medicine');
        
        // Get batch details for quick view
        Route::get('/{medicineBatch}/quick-details', [MedicineBatchController::class, 'getQuickDetails'])->name('quick_details');
        
        // Check expiry status
        Route::get('/expiry-check', [MedicineBatchController::class, 'getExpiringBatches'])->name('expiry_check');
        
        // Get stock movements for a batch
        Route::get('/{medicineBatch}/movements', [MedicineBatchController::class, 'getStockMovements'])->name('movements');
        
        // Bulk operations
        Route::post('/bulk-mark-expired', [MedicineBatchController::class, 'bulkMarkExpired'])->name('bulk_mark_expired');
        
        // Search/filter routes
        Route::get('/search', [MedicineBatchController::class, 'search'])->name('search');
        Route::get('/filter', [MedicineBatchController::class, 'filter'])->name('filter');
    });
});
Route::get('/medicines/{medicine}/batches', [MedicineController::class, 'getBatches'])
    ->name('medicines.batches');

// Routes for different user roles (if needed)
Route::middleware(['auth', 'role:super_admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Super admin specific medicine batch routes
    Route::prefix('medicine-batches')->name('medicine_batches.')->group(function () {
        Route::get('/all-pharmacies', [MedicineBatchController::class, 'allPharmacies'])->name('all_pharmacies');
        Route::get('/analytics', [MedicineBatchController::class, 'analytics'])->name('analytics');
        Route::post('/global-expiry-check', [MedicineBatchController::class, 'globalExpiryCheck'])->name('global_expiry_check');
    });
});

// Pharmacy staff specific routes
Route::middleware(['auth', 'role:pharmacist,pharmacy_staff'])->prefix('staff')->name('staff.')->group(function () {
    
    Route::prefix('medicine-batches')->name('medicine_batches.')->group(function () {
        // Limited access routes for staff
        Route::get('/assigned', [MedicineBatchController::class, 'assignedBatches'])->name('assigned');
        Route::get('/recent', [MedicineBatchController::class, 'recentBatches'])->name('recent');
    });
});



use App\Http\Controllers\Pharmacy\SaleController;


// Sales Routes
Route::middleware(['auth'])->group(function () {
    
    // Main CRUD routes for Sales
    Route::prefix('pharmacy/sales')->name('sales.')->group(function () {
        
        // Index - List all sales with filtering
        Route::get('/index', [SaleController::class, 'index'])->name('index');
        
        // Create - Show form to create new sale
        Route::get('/create', [SaleController::class, 'create'])->name('create');
        
        // Store - Save new sale
        Route::post('/', [SaleController::class, 'store'])->name('store');
        
        // Show - Display specific sale details
        Route::get('/{sale}', [SaleController::class, 'show'])->name('show');
        
        // Edit - Show form to edit existing sale (limited fields)
        Route::get('/{sale}/edit', [SaleController::class, 'edit'])->name('edit');
        
        // Update - Save edited sale
        Route::put('/{sale}', [SaleController::class, 'update'])->name('update');
        
        // Cancel - Cancel/refund sale
        Route::post('/{sale}/cancel', [SaleController::class, 'cancel'])->name('cancel');
        
        // Delete - Remove sale (restricted)
        Route::delete('/{sale}', [SaleController::class, 'destroy'])->name('destroy');
        
        // Analytics and Reports
        Route::get('/reports/analytics', [SaleController::class, 'analytics'])->name('analytics');
        
        // Print/Export routes
        Route::get('/{sale}/print', [SaleController::class, 'print'])->name('print');
        Route::get('/{sale}/receipt', [SaleController::class, 'receipt'])->name('receipt');
        Route::get('/export/csv', [SaleController::class, 'exportCSV'])->name('export.csv');
        Route::get('/export/pdf', [SaleController::class, 'exportPDF'])->name('export.pdf');
    });
    
    // API routes for AJAX requests
    Route::prefix('api/pharmacy/sales')->name('api.sales.')->group(function () {
        
        // Get medicine batches by medicine ID
        Route::get('/medicine-batches/{medicineId}', [SaleController::class, 'getMedicineBatches'])->name('medicine_batches');
        
        // Get medicine details with pricing
        Route::get('/medicine-details/{medicineId}', [SaleController::class, 'getMedicineDetails'])->name('medicine_details');
        
        // Calculate sale totals (for real-time calculation)
        Route::post('/calculate-totals', [SaleController::class, 'calculateTotals'])->name('calculate_totals');
        
        // Search customers
        Route::get('/customers/search', [SaleController::class, 'searchCustomers'])->name('customers.search');
        
        // Get prescription details
        Route::get('/prescriptions/{prescriptionId}', [SaleController::class, 'getPrescriptionDetails'])->name('prescription_details');
        
        // Validate stock availability
        Route::post('/validate-stock', [SaleController::class, 'validateStock'])->name('validate_stock');
        
        // Get sales statistics for dashboard
        Route::get('/statistics', [SaleController::class, 'getStatistics'])->name('statistics');
        
        // Search sales
        Route::get('/search', [SaleController::class, 'search'])->name('search');
    });
});

// Additional routes for different user roles
Route::middleware(['auth', 'role:super_admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Super admin specific sales routes
    Route::prefix('sales')->name('sales.')->group(function () {
        Route::get('/all-pharmacies', [SaleController::class, 'allPharmacies'])->name('all_pharmacies');
        Route::get('/global-analytics', [SaleController::class, 'globalAnalytics'])->name('global_analytics');
        Route::post('/void-sale/{sale}', [SaleController::class, 'voidSale'])->name('void');
    });
});

// Pharmacy manager specific routes
Route::middleware(['auth', 'role:pharmacy_manager,pharmacist'])->prefix('manager')->name('manager.')->group(function () {
    
    Route::prefix('sales')->name('sales.')->group(function () {
        Route::get('/daily-report', [SaleController::class, 'dailyReport'])->name('daily_report');
        Route::get('/staff-performance', [SaleController::class, 'staffPerformance'])->name('staff_performance');
        Route::post('/approve-refund/{sale}', [SaleController::class, 'approveRefund'])->name('approve_refund');
    });
});

