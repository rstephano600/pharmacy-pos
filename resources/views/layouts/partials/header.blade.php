<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
    <!-- Sidebar Toggle (Topbar) -->
    <button id="mobileMenuBtn" class="mobile-menu-btn me-3">
        <i class="bi bi-list"></i>
    </button>

    <!-- Topbar Brand (Visible on mobile) -->
    <div class="navbar-brand d-lg-none">
        <i class="bi bi-capsule-pill"></i> PharmaManage
    </div>

    <!-- Topbar Navbar -->
    <ul class="navbar-nav ms-auto">
        <!-- Notifications Dropdown -->
        <li class="nav-item dropdown no-arrow mx-1">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                <i class="bi bi-bell text-dark"></i>
                <!-- Counter - Notifications -->
                <span class="badge bg-danger badge-counter">3+</span>
                
            </a>
            <!-- Dropdown - Notifications -->
            <div class="dropdown-menu dropdown-menu-end shadow animated--grow-in">
                <h6 class="dropdown-header">Notifications Center</h6>
                <a class="dropdown-item d-flex align-items-center" href="#">
                    <div class="me-3">
                        <div class="icon-circle bg-primary">
                            <i class="bi bi-file-text text-dark"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-dark">December 12, 2023</div>
                        <span>New prescription received</span>
                    </div>
                </a>
                <a class="dropdown-item text-center small text-dark" href="#">Show All Notifications</a>
            </div>
        </li>

        <!-- User Information -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                <span class="d-none d-lg-inline text-dark small me-2">{{ Auth::user()->full_name }}</span>
                <img class="img-profile rounded-circle" src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->full_name) }}&background=4e73df&color=fff" width="32" height="32">
            </a>
            <!-- Dropdown - User Information -->
            <div class="dropdown-menu dropdown-menu-end shadow animated--grow-in">
                <a class="dropdown-item" href="#">
                    <i class="bi bi-person me-2 text-gray-400"></i>
                    Profile
                </a>
                <a class="dropdown-item" href="#">
                    <i class="bi bi-gear me-2 text-gray-400"></i>
                    Settings
                </a>
                <a class="dropdown-item" href="#">
                    <i class="bi bi-list-check me-2 text-gray-400"></i>
                    Activity Log
                </a>
                <div class="dropdown-divider"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item">
                        <i class="bi bi-box-arrow-right me-2 text-gray-400"></i>
                        Logout
                    </button>
                </form>
            </div>
        </li>
    </ul>
</nav>