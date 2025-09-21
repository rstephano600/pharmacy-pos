@php
    $user = Auth::user();
    $currentRoute = request()->route()->getName();
@endphp

<aside class="sidebar" id="sidebar">
    <!-- Sidebar Brand -->
    <div class="sidebar-brand d-flex align-items-center justify-content-center p-4">
        <div class="sidebar-brand-icon">
            <i class="bi bi-capsule-pill"></i>
        </div>
        <div class="sidebar-brand-text mx-3">PharmaManage</div>
    </div>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Items -->
    <div class="sidebar-content">
        <!-- Dashboard -->
        <li class="nav-item {{ str_starts_with($currentRoute, 'dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('user.dashboard') }}">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <!-- Divider -->
        <hr class="sidebar-divider">

        <!-- Heading -->
        <div class="sidebar-heading">Main Menu</div>

        <!-- Pharmacy Management (For pharmacy staff) -->
        @if($user->isPharmacyStaff() && $user->hasPharmacy())
        <li class="nav-item {{ str_starts_with($currentRoute, 'pharmacy.') ? 'active' : '' }}">
            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#pharmacyCollapse">
                <i class="bi bi-building"></i>
                <span>Pharmacy</span>
                <i class="bi bi-chevron-down"></i>
            </a>
            <div id="pharmacyCollapse" class="collapse">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Pharmacy Management:</h6>
                    <a class="collapse-item" href="#">Profile</a>
                    <a class="collapse-item" href="#">Inventory</a>
                    <a class="collapse-item" href="#">Staff</a>
                    @if($user->role === \App\Models\User::ROLE_PHARMACY_OWNER)
                    <a class="collapse-item" href="#">Settings</a>
                    @endif
                </div>
            </div>
        </li>
        @endif

        <!-- Prescriptions -->
        <li class="nav-item {{ str_starts_with($currentRoute, 'prescription.') ? 'active' : '' }}">
            <a class="nav-link" href="#">
                <i class="bi bi-prescription2"></i>
                <span>Prescriptions</span>
            </a>
        </li>

        <!-- Inventory -->
        @if($user->isPharmacyStaff() && $user->hasPharmacy())
        <li class="nav-item {{ str_starts_with($currentRoute, 'inventory.') ? 'active' : '' }}">
            <a class="nav-link" href="#">
                <i class="bi bi-inventory"></i>
                <span>Inventory</span>
            </a>
        </li>
        @endif

        <!-- Patients -->
        <li class="nav-item {{ str_starts_with($currentRoute, 'patient.') ? 'active' : '' }}">
            <a class="nav-link" href="#">
                <i class="bi bi-people"></i>
                <span>Patients</span>
            </a>
        </li>

        <!-- Reports -->
        @if($user->isAdmin() || ($user->isPharmacyStaff() && $user->hasPharmacy()))
        <li class="nav-item {{ str_starts_with($currentRoute, 'report.') ? 'active' : '' }}">
            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#reportsCollapse">
                <i class="bi bi-graph-up"></i>
                <span>Reports</span>
                <i class="bi bi-chevron-down"></i>
            </a>
            <div id="reportsCollapse" class="collapse">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Reports:</h6>
                    <a class="collapse-item" href="#">Sales</a>
                    <a class="collapse-item" href="#">Inventory</a>
                    <a class="collapse-item" href="#">Prescriptions</a>
                    @if($user->isAdmin())
                    <a class="collapse-item" href="#">System</a>
                    @endif
                </div>
            </div>
        </li>
        @endif

        <!-- Admin Section -->
        @if($user->isAdmin())
        <hr class="sidebar-divider">
        <div class="sidebar-heading">Admin</div>
        
        <li class="nav-item {{ str_starts_with($currentRoute, 'admin.') ? 'active' : '' }}">
            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#adminCollapse">
                <i class="bi bi-shield-lock"></i>
                <span>Administration</span>
                <i class="bi bi-chevron-down"></i>
            </a>
            <div id="adminCollapse" class="collapse">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Admin Tools:</h6>
                    <a class="collapse-item" href="#">Users</a>
                    <a class="collapse-item" href="#">Pharmacies</a>
                    <a class="collapse-item" href="#">System Settings</a>
                    <a class="collapse-item" href="#">Audit Log</a>
                </div>
            </div>
        </li>
        @endif

        <!-- Pharmacy Association Prompt -->
        @if($user->isPharmacyStaff() && !$user->hasPharmacy())
        <hr class="sidebar-divider">
        <div class="sidebar-heading">Setup Required</div>
        
        <li class="nav-item">
            <a class="nav-link text-warning" href="{{ route('pharmacy.association') }}">
                <i class="bi bi-exclamation-triangle"></i>
                <span>Join Pharmacy</span>
            </a>
        </li>
        @endif
    </div>

    <!-- Sidebar Toggler (Bottom) -->
    <div class="text-center d-none d-md-inline mt-auto p-3">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</aside>

<style>
    .sidebar {
        width: 250px;
        min-height: 100vh;
        background: linear-gradient(180deg, var(--primary-color) 0%, #224abe 100%);
        color: white;
        position: fixed;
        z-index: 1050;
        transition: all 0.3s ease;
        left: -250px;
    }
    
    .sidebar.active {
        left: 0;
    }
    
    .sidebar-brand {
        background: rgba(255, 255, 255, 0.1);
    }
    
    .sidebar-brand-text {
        font-weight: 600;
        font-size: 1.1rem;
    }
    
    .sidebar-brand-icon {
        font-size: 1.5rem;
    }
    
    .sidebar .nav-item {
        position: relative;
    }
    
    .sidebar .nav-link {
        color: rgba(255, 255, 255, 0.8);
        padding: 1rem;
        transition: all 0.3s;
    }
    
    .sidebar .nav-link:hover {
        color: white;
        background: rgba(255, 255, 255, 0.1);
    }
    
    .sidebar .nav-link.active {
        color: white;
        background: rgba(255, 255, 255, 0.2);
    }
    
    .sidebar .nav-link i {
        margin-right: 0.5rem;
    }
    
    .sidebar-divider {
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        margin: 1rem 0;
    }
    
    .sidebar-heading {
        padding: 0 1rem;
        font-size: 0.85rem;
        color: rgba(255, 255, 255, 0.6);
        text-transform: uppercase;
        font-weight: 600;
    }
    
    @media (min-width: 992px) {
        .sidebar {
            left: 0;
        }
    }
</style>