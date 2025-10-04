@php
    $user = Auth::user();
    $currentRoute = request()->route()->getName();
@endphp

<aside class="sidebar" id="sidebar">
    <!-- Sidebar Brand -->
    <div class="sidebar-brand">
        <div class="brand-content">
            <div class="sidebar-brand-icon">
                <i class="bi bi-capsule-pill"></i>
            </div>
            <div class="sidebar-brand-text">PharmaManage</div>
        </div>
    </div>

    <!-- Scrollable Content -->
    <div class="sidebar-scrollable">

            <!-- Dashboard -->
            <li class="nav-item {{ str_starts_with($currentRoute, 'dashboard') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('dashboard') }}">
                    <span class="nav-icon"><i class="bi bi-speedometer2"></i></span>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>

            <!-- Heading -->
            <div class="sidebar-heading">Main Menu</div>

            <!-- Pharmacy Management (For pharmacy staff) -->
            @if($user->isPharmacyStaff() && $user->hasPharmacy())
            <li class="nav-item {{ str_starts_with($currentRoute, 'pharmacy.') ? 'active' : '' }}">
                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#pharmacyCollapse">
                    <span class="nav-icon"><i class="bi bi-building"></i></span>
                    <span class="nav-text">Pharmacy</span>
                    <span class="nav-arrow"><i class="bi bi-chevron-down"></i></span>
                </a>
                <div id="pharmacyCollapse" class="collapse {{ str_starts_with($currentRoute, 'pharmacy.') ? 'show' : '' }}">
                    <div class="collapse-inner">
                        <div class="collapse-header">Pharmacy Management</div>

                        <a class="collapse-item" href="{{ route('sales.index') }}">
                            <i class="bi bi-cash-coin display-10 d-block me-2"></i>
                            <span>Sales</span>
                        </a>
                        <a class="collapse-item" href="{{ route('prescriptions.index') }}">
                            <i class="bi bi-prescription display-10 d-block me-2"></i>
                            <span>Prescriptions</span>
                        </a>
                        <a class="collapse-item" href="{{ route('customers.index') }}">
                            <i class="bi bi-people display-10 d-block me-2"></i>
                            <span>Patients</span>
                        </a>
                        <a class="collapse-item" href="{{ route('medicine-categories.index') }}">
                            <i class="bi bi-tags display-10 d-block me-2"></i>
                            <span>Categories</span>
                        </a>
                        <a class="collapse-item" href="{{ route('medicines.index') }}">
                            <i class="bi bi-capsule display-10 d-block me-2"></i>
                            <span>Medicines</span>
                        </a>
                        <a class="collapse-item" href="{{ route('medicine_batches.index') }}">
                            <i class="bi bi-house display-10 d-block me-2"></i>
                            <span>Inventory/Batches</span>
                        </a>
                        <a class="collapse-item" href="{{ route('purchase_orders.index') }}">
                            <i class="bi bi-cart-plus display-10 d-block me-2"></i>
                            <span>Purchases</span>
                        </a>
                        <a class="collapse-item" href="{{ route('suppliers.index') }}">
                            <i class="bi bi-truck display-10 d-block me-2"></i>
                            <span>Suppliers</span>
                        </a>
                        <a class="collapse-item" href="#">
                            <i class="bi bi-people me-2"></i>
                            <span>Staff</span>
                        </a>
                        <a class="collapse-item" href="#">
                            <i class="bi bi-person-circle me-2"></i>
                            <span>Profile</span>
                        </a>
                        <a class="collapse-item" href="#">
                            <i class="bi bi-inventory me-2"></i>
                            <span>Inventory</span>
                        </a>
                        @if($user->role === \App\Models\User::ROLE_PHARMACY_OWNER)
                        <a class="collapse-item" href="#">
                            <i class="bi bi-gear me-2"></i>
                            <span>Settings</span>
                        </a>
                        @endif
                    </div>
                </div>
            </li>
            @endif

            <!-- Prescriptions -->
            <li class="nav-item {{ str_starts_with($currentRoute, 'prescription.') ? 'active' : '' }}">
                <a class="nav-link" href="#">
                    <span class="nav-icon"><i class="bi bi-prescription2"></i></span>
                    <span class="nav-text">Prescriptions</span>
                    <span class="nav-badge bg-primary">12</span>
                </a>
            </li>

            <!-- Inventory -->
            @if($user->isPharmacyStaff() && $user->hasPharmacy())
            <li class="nav-item {{ str_starts_with($currentRoute, 'inventory.') ? 'active' : '' }}">
                <a class="nav-link" href="#">
                    <span class="nav-icon"><i class="bi bi-inventory"></i></span>
                    <span class="nav-text">Inventory</span>
                    <span class="nav-badge bg-warning">8</span>
                </a>
            </li>
            @endif

            <!-- Patients -->
            <li class="nav-item {{ str_starts_with($currentRoute, 'patient.') ? 'active' : '' }}">
                <a class="nav-link" href="#">
                    <span class="nav-icon"><i class="bi bi-people"></i></span>
                    <span class="nav-text">Patients</span>
                    <span class="nav-badge bg-success">24</span>
                </a>
            </li>

            <!-- Reports -->
            @if($user->isAdmin() || ($user->isPharmacyStaff() && $user->hasPharmacy()))
            <li class="nav-item {{ str_starts_with($currentRoute, 'report.') ? 'active' : '' }}">
                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#reportsCollapse">
                    <span class="nav-icon"><i class="bi bi-graph-up"></i></span>
                    <span class="nav-text">Reports</span>
                    <span class="nav-arrow"><i class="bi bi-chevron-down"></i></span>
                </a>
                <div id="reportsCollapse" class="collapse {{ str_starts_with($currentRoute, 'report.') ? 'show' : '' }}">
                    <div class="collapse-inner">
                        <div class="collapse-header">Reports</div>
                        <a class="collapse-item" href="#">
                            <i class="bi bi-currency-dollar me-2"></i>
                            <span>Sales</span>
                        </a>
                        <a class="collapse-item" href="#">
                            <i class="bi bi-box me-2"></i>
                            <span>Inventory</span>
                        </a>
                        <a class="collapse-item" href="#">
                            <i class="bi bi-file-text me-2"></i>
                            <span>Prescriptions</span>
                        </a>
                        @if($user->isAdmin())
                        <a class="collapse-item" href="#">
                            <i class="bi bi-cpu me-2"></i>
                            <span>System</span>
                        </a>
                        @endif
                    </div>
                </div>
            </li>
            @endif

            <!-- Admin Section -->
            @if($user->isAdmin())
            <hr class="sidebar-divider">
            <div class="sidebar-heading">Administration</div>
            
            <li class="nav-item {{ str_starts_with($currentRoute, 'admin.') ? 'active' : '' }}">
                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#adminCollapse">
                    <span class="nav-icon"><i class="bi bi-shield-lock"></i></span>
                    <span class="nav-text">Admin Center</span>
                    <span class="nav-arrow"><i class="bi bi-chevron-down"></i></span>
                </a>
                <div id="adminCollapse" class="collapse {{ str_starts_with($currentRoute, 'admin.') ? 'show' : '' }}">
                    <div class="collapse-inner">
                        <div class="collapse-header">Admin Tools</div>
                        <a class="collapse-item" href="{{ route('superadmin.users.index') }}">
                            <i class="bi bi-people me-2"></i>
                            <span>Users</span>
                            <span class="badge bg-primary rounded-pill ms-auto">24</span>
                        </a>
                        <a class="collapse-item" href="{{ route('superadmin.pharmacies.index') }}">
                            <i class="bi bi-building me-2"></i>
                            <span>Pharmacies</span>
                            <span class="badge bg-success rounded-pill ms-auto">15</span>
                        </a>
                        <a class="collapse-item" href="#">
                            <i class="bi bi-gear me-2"></i>
                            <span>System Settings</span>
                        </a>
                        <a class="collapse-item" href="#">
                            <i class="bi bi-clipboard-data me-2"></i>
                            <span>Audit Log</span>
                            <span class="badge bg-warning rounded-pill ms-auto">3</span>
                        </a>
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
                    <span class="nav-icon"><i class="bi bi-exclamation-triangle"></i></span>
                    <span class="nav-text">Join Pharmacy</span>
                    <span class="nav-badge bg-danger">!</span>
                </a>
            </li>
            @endif
        </ul>
    </div>

    <!-- Sidebar Footer -->
    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->full_name) }}&background=4e73df&color=fff" 
                     alt="{{ $user->full_name }}">
            </div>
            <div class="user-details">
                <div class="user-name">{{ $user->full_name }}</div>
                <div class="user-role">{{ ucfirst($user->role) }}</div>
            </div>
        </div>
    </div>
</aside>

<style>
    .sidebar {
        width: 250px;
        height: 100vh;
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        color: white;
        position: fixed;
        z-index: 1050;
        transition: all 0.3s ease;
        left: -250px;
        display: flex;
        flex-direction: column;
        box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
    }

    .sidebar.active {
        left: 0;
    }

    .sidebar-brand {
        padding: 1rem 1rem;
        background: rgba(255, 255, 255, 0.1);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        flex-shrink: 0;
    }

    .brand-content {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .sidebar-brand-icon {
        font-size: 2rem;
        margin-right: 0.75rem;
    }

    .sidebar-brand-text {
        font-weight: 700;
        font-size: 1.25rem;
        color: white;
    }

    .sidebar-scrollable {
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
        padding: 1rem 0;
    }

    .sidebar-nav {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .nav-item {
        margin: 0.25rem 0.75rem;
    }

    .nav-link {
        display: flex;
        align-items: center;
        padding: 0.75rem 1rem;
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
        border-radius: 8px;
        transition: all 0.3s ease;
        position: relative;
    }

    .nav-link:hover {
        background: rgba(255, 255, 255, 0.1);
        color: white;
        transform: translateX(5px);
    }

    .nav-link.active {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        box-shadow: 0 4px 15px rgba(255, 255, 255, 0.1);
    }

    .nav-icon {
        width: 20px;
        margin-right: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .nav-text {
        flex: 1;
        font-weight: 500;
    }

    .nav-arrow {
        transition: transform 0.3s ease;
    }

    .nav-link.collapsed .nav-arrow {
        transform: rotate(0deg);
    }

    .nav-arrow {
        transform: rotate(180deg);
    }

    .nav-badge {
        padding: 0.25rem 0.5rem;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
        min-width: 20px;
        text-align: center;
    }

    .sidebar-divider {
        border-color: rgba(255, 255, 255, 0.1);
        margin: 1rem 0.75rem;
    }

    .sidebar-heading {
        padding: 0.5rem 1.25rem;
        font-size: 0.75rem;
        color: rgba(255, 255, 255, 0.6);
        text-transform: uppercase;
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    .collapse-inner {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 8px;
        margin: 0.5rem 0;
        padding: 0.5rem 0;
    }

    .collapse-header {
        padding: 0.5rem 1.25rem;
        font-size: 0.7rem;
        color: rgba(255, 255, 255, 0.6);
        text-transform: uppercase;
        font-weight: 600;
    }

    .collapse-item {
        display: flex;
        align-items: center;
        padding: 0.5rem 1.25rem;
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
        transition: all 0.3s ease;
        border-left: 3px solid transparent;
        margin: 0.25rem 0.5rem;
        border-radius: 6px;
    }

    .collapse-item:hover {
        background: rgba(255, 255, 255, 0.1);
        color: white;
        border-left-color: white;
    }

    .collapse-item.active {
        background: rgba(255, 255, 255, 0.15);
        color: white;
        border-left-color: white;
    }

    .sidebar-footer {
        padding: 1rem;
        background: rgba(255, 255, 255, 0.1);
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        flex-shrink: 0;
    }

    .user-info {
        display: flex;
        align-items: center;
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        overflow: hidden;
        margin-right: 0.75rem;
        border: 2px solid rgba(255, 255, 255, 0.2);
    }

    .user-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .user-details {
        flex: 1;
    }

    .user-name {
        font-weight: 600;
        font-size: 0.9rem;
    }

    .user-role {
        font-size: 0.75rem;
        color: rgba(255, 255, 255, 0.7);
    }

    /* Scrollbar Styling */
    .sidebar-scrollable::-webkit-scrollbar {
        width: 6px;
    }

    .sidebar-scrollable::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 3px;
    }

    .sidebar-scrollable::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.3);
        border-radius: 3px;
    }

    .sidebar-scrollable::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.5);
    }

    @media (min-width: 992px) {
        .sidebar {
            left: 0;
        }
    }

    @media (max-width: 991.98px) {
        .sidebar {
            width: 250px;
            left: -250px;
        }
        
        .sidebar-brand {
            padding: 1rem 0.75rem;
        }
        
        .nav-item {
            margin: 0.2rem 0.5rem;
        }
        
        .nav-link {
            padding: 0.6rem 0.75rem;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Maintain collapse state
    const saveCollapseState = (collapseId, isOpen) => {
        localStorage.setItem(`collapse_${collapseId}`, isOpen);
    };

    const getCollapseState = (collapseId) => {
        return localStorage.getItem(`collapse_${collapseId}`) === 'true';
    };

    // Initialize collapse states
    const collapses = ['pharmacyCollapse', 'reportsCollapse', 'adminCollapse'];
    collapses.forEach(collapseId => {
        const isOpen = getCollapseState(collapseId);
        const collapseElement = document.getElementById(collapseId);
        if (collapseElement) {
            if (isOpen) {
                new bootstrap.Collapse(collapseElement, { show: true });
            }
            
            collapseElement.addEventListener('show.bs.collapse', () => {
                saveCollapseState(collapseId, true);
            });
            
            collapseElement.addEventListener('hide.bs.collapse', () => {
                saveCollapseState(collapseId, false);
            });
        }
    });

    // Auto-open collapse if current route matches
    const currentPath = window.location.pathname;
    if (currentPath.includes('/admin')) {
        const adminCollapse = document.getElementById('adminCollapse');
        if (adminCollapse) {
            new bootstrap.Collapse(adminCollapse, { show: true });
        }
    }
});
</script>