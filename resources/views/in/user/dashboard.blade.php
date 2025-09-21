
@extends('layouts.app')

@section('title', 'User Dashboard')
@section('header', 'Welcome to PharmaManage')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">Your Dashboard</h6>
            </div>
            <div class="card-body">
                <div class="text-center">
                    <i class="bi bi-person-circle display-1 text-primary mb-3"></i>
                    <h3>Welcome, {{ Auth::user()->full_name }}!</h3>
                    <p class="text-muted">You're logged in as a <strong>{{ Auth::user()->role }}</strong></p>
                    
                    @if(!Auth::user()->pharmacy() && Auth::user()->isPharmacyStaff())
                    <div class="alert alert-warning mt-4">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        You need to be associated with a pharmacy to access all features.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Your dashboard content here -->
    <div class="col-md-6 mb-4">
        <div class="card border-left-primary h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-capsule text-primary" style="font-size: 2rem;"></i>
                    </div>
                    <div>
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Medicines
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">2,500+</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card border-left-success h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-building text-success" style="font-size: 2rem;"></i>
                    </div>
                    <div>
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Partner Pharmacies
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">150+</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection



<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Dashboard - Pharmacy Management System</title>

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            --secondary-gradient: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
            --warning-gradient: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%);
            --info-gradient: linear-gradient(135deg, #36b9cc 0%, #258391 100%);
        }
        
        /* Sidebar Styles */
        .sidebar {
            min-height: 100vh;
            background: var(--primary-gradient);
            color: white;
            position: fixed;
            z-index: 1000;
            transition: all 0.3s ease;
            width: 250px;
            left: -250px;
        }
        
        .sidebar.active {
            left: 0;
        }
        
        /* Main Content */
        .main-content {
            min-height: 100vh;
            background-color: #f8f9fc;
            transition: margin-left 0.3s ease;
            margin-left: 0;
        }
        
        .main-content.sidebar-open {
            margin-left: 250px;
        }
        
        /* Navbar */
        .navbar-top {
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 999;
        }
        
        /* Card Styles */
        .card-gradient {
            border: none;
            border-radius: 15px;
            color: white;
            transition: transform 0.3s ease;
        }
        
        .card-gradient:hover {
            transform: translateY(-5px);
        }
        
        .stat-card {
            border-left: 4px solid;
            border-radius: 8px;
            background: white;
            transition: transform 0.2s ease;
            height: 100%;
        }
        
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .welcome-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
        }
        
        .service-card {
            border: none;
            border-radius: 12px;
            transition: all 0.3s ease;
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            height: 100%;
        }
        
        .service-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        
        .icon-circle {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0;
        }
        
        /* Mobile Menu Button */
        .mobile-menu-btn {
            display: none;
            border: none;
            background: none;
            font-size: 1.5rem;
            color: #4e73df;
        }
        
        /* Overlay for mobile */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }
        
        .sidebar-overlay.active {
            display: block;
        }
        
        /* Responsive adjustments */
        @media (min-width: 992px) {
            .sidebar {
                left: 0;
                width: 250px;
            }
            
            .main-content {
                margin-left: 250px;
            }
            
            .mobile-menu-btn {
                display: none !important;
            }
        }
        
        @media (max-width: 991.98px) {
            .mobile-menu-btn {
                display: block;
            }
            
            .sidebar {
                width: 250px;
            }
            
            .main-content.sidebar-open {
                margin-left: 0;
            }
            
            .stat-card {
                margin-bottom: 1rem;
            }
            
            .welcome-card .col-md-4 {
                display: none;
            }
        }
        
        @media (max-width: 767.98px) {
            .icon-circle {
                width: 40px;
                height: 40px;
                font-size: 1rem;
            }
            
            .welcome-card h2 {
                font-size: 1.5rem;
            }
            
            .service-card {
                margin-bottom: 1rem;
            }
            
            .btn-lg {
                padding: 0.5rem 1rem;
                font-size: 0.9rem;
            }
        }
        
        @media (max-width: 575.98px) {
            .container-fluid {
                padding: 1rem;
            }
            
            .welcome-card {
                padding: 1.5rem !important;
            }
            
            .stat-card {
                padding: 1rem !important;
            }
            
            .service-card {
                padding: 1.5rem !important;
            }
            
            .btn-group-responsive {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .btn-group-responsive .btn {
                width: 100%;
            }
        }
        
        /* Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate__animated {
            animation-duration: 0.5s;
            animation-fill-mode: both;
        }
        
        .animate__fadeInUp {
            animation-name: fadeInUp;
        }
    </style>
</head>
<body>
    <!-- Mobile Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="p-4 text-center">
                <h4 class="mb-0">PharmaManage</h4>
                <small class="opacity-75">Pharmacy System</small>
            </div>
            
            <hr class="my-2 opacity-25">
            
            <nav class="nav flex-column p-3">
                <a href="#" class="nav-link text-white active">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
                <a href="#" class="nav-link text-white opacity-75">
                    <i class="bi bi-person me-2"></i> Profile
                </a>
                <a href="#" class="nav-link text-white opacity-75">
                    <i class="bi bi-question-circle me-2"></i> Help & Support
                </a>
                <a href="#" class="nav-link text-white opacity-75">
                    <i class="bi bi-gear me-2"></i> Settings
                </a>
                <hr class="my-2 opacity-25">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="nav-link text-white opacity-75 border-0 bg-transparent w-100 text-start">
                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                    </button>
                </form>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content w-100" id="mainContent">
            <!-- Top Navigation -->
            <nav class="navbar-top navbar-expand navbar-light shadow-sm">
                <div class="container-fluid">
                    <button class="mobile-menu-btn me-3" id="mobileMenuBtn">
                        <i class="bi bi-list"></i>
                    </button>
                    
                    <div class="navbar-brand d-lg-none mb-0 h1">
                        PharmaManage
                    </div>
                    
                    <div class="collapse navbar-collapse">
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-person-circle me-1"></i> 
                                    <span class="d-none d-md-inline">{{ Auth::user()->full_name }}</span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profile</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Settings</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item">
                                                <i class="bi bi-box-arrow-right me-2"></i> Logout
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <!-- Page Content -->
            <div class="container-fluid p-3 p-lg-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Welcome Card -->
                <div class="welcome-card p-4 mb-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="mb-2">Welcome back, {{ Auth::user()->full_name }}! 👋</h2>
                            <p class="mb-0 opacity-90">
                                You're logged in as a <strong>{{ Auth::user()->role }}</strong>. 
                                Get started by exploring our pharmacy management services.
                            </p>
                        </div>
                        <div class="col-md-4 text-center d-none d-md-block">
                            <i class="bi bi-heart-pulse-fill" style="font-size: 3rem;"></i>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="stat-card border-left-primary p-3">
                            <div class="d-flex align-items-center">
                                <div class="icon-circle bg-primary text-white me-3">
                                    <i class="bi bi-capsule"></i>
                                </div>
                                <div>
                                    <div class="text-xs text-uppercase text-primary fw-bold">Medicines</div>
                                    <div class="h5 mb-0 fw-bold">2,500+</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="stat-card border-left-success p-3">
                            <div class="d-flex align-items-center">
                                <div class="icon-circle bg-success text-white me-3">
                                    <i class="bi bi-people"></i>
                                </div>
                                <div>
                                    <div class="text-xs text-uppercase text-success fw-bold">Pharmacies</div>
                                    <div class="h5 mb-0 fw-bold">150+</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="stat-card border-left-warning p-3">
                            <div class="d-flex align-items-center">
                                <div class="icon-circle bg-warning text-white me-3">
                                    <i class="bi bi-cart-check"></i>
                                </div>
                                <div>
                                    <div class="text-xs text-uppercase text-warning fw-bold">Orders Today</div>
                                    <div class="h5 mb-0 fw-bold">324</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="stat-card border-left-info p-3">
                            <div class="d-flex align-items-center">
                                <div class="icon-circle bg-info text-white me-3">
                                    <i class="bi bi-clock-history"></i>
                                </div>
                                <div>
                                    <div class="text-xs text-uppercase text-info fw-bold">Avg. Response</div>
                                    <div class="h5 mb-0 fw-bold">2.4 min</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- System Services -->
                <div class="row">
                    <div class="col-12 mb-3">
                        <h4 class="mb-3">Our Pharmacy Services</h4>
                    </div>
                    
                    <div class="col-xl-4 col-md-6 mb-3">
                        <div class="service-card p-3 p-md-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon-circle bg-primary text-white me-3">
                                    <i class="bi bi-prescription2"></i>
                                </div>
                                <h5 class="mb-0 fs-6 fs-md-5">Prescription Management</h5>
                            </div>
                            <p class="text-muted small">Manage and track prescriptions with our advanced digital system.</p>
                            <a href="#" class="btn btn-outline-primary btn-sm">Learn More</a>
                        </div>
                    </div>
                    
                    <div class="col-xl-4 col-md-6 mb-3">
                        <div class="service-card p-3 p-md-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon-circle bg-success text-white me-3">
                                    <i class="bi bi-inventory"></i>
                                </div>
                                <h5 class="mb-0 fs-6 fs-md-5">Inventory Tracking</h5>
                            </div>
                            <p class="text-muted small">Real-time inventory management with automated restocking alerts.</p>
                            <a href="#" class="btn btn-outline-success btn-sm">Learn More</a>
                        </div>
                    </div>
                    
                    <div class="col-xl-4 col-md-6 mb-3">
                        <div class="service-card p-3 p-md-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon-circle bg-warning text-white me-3">
                                    <i class="bi bi-shield-check"></i>
                                </div>
                                <h5 class="mb-0 fs-6 fs-md-5">Patient Safety</h5>
                            </div>
                            <p class="text-muted small">Advanced drug interaction checks and allergy alerts.</p>
                            <a href="#" class="btn btn-outline-warning btn-sm">Learn More</a>
                        </div>
                    </div>
                    
                    <div class="col-xl-4 col-md-6 mb-3">
                        <div class="service-card p-3 p-md-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon-circle bg-info text-white me-3">
                                    <i class="bi bi-graph-up"></i>
                                </div>
                                <h5 class="mb-0 fs-6 fs-md-5">Reporting & Analytics</h5>
                            </div>
                            <p class="text-muted small">Comprehensive reports on sales and inventory data.</p>
                            <a href="#" class="btn btn-outline-info btn-sm">Learn More</a>
                        </div>
                    </div>
                    
                    <div class="col-xl-4 col-md-6 mb-3">
                        <div class="service-card p-3 p-md-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon-circle bg-danger text-white me-3">
                                    <i class="bi bi-phone"></i>
                                </div>
                                <h5 class="mb-0 fs-6 fs-md-5">Mobile Access</h5>
                            </div>
                            <p class="text-muted small">Access your pharmacy data anywhere with our mobile platform.</p>
                            <a href="#" class="btn btn-outline-danger btn-sm">Learn More</a>
                        </div>
                    </div>
                    
                    <div class="col-xl-4 col-md-6 mb-3">
                        <div class="service-card p-3 p-md-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon-circle bg-secondary text-white me-3">
                                    <i class="bi bi-headset"></i>
                                </div>
                                <h5 class="mb-0 fs-6 fs-md-5">24/7 Support</h5>
                            </div>
                            <p class="text-muted small">Round-the-clock customer support for any issues.</p>
                            <a href="#" class="btn btn-outline-secondary btn-sm">Contact Support</a>
                        </div>
                    </div>
                </div>

                <!-- Call to Action -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center py-4 py-md-5">
                                <h3 class="mb-3">Ready to Get Started?</h3>
                                <p class="text-muted mb-4">Join our network of pharmacies and start managing your operations efficiently</p>
                                <div class="d-flex justify-content-center gap-3 btn-group-responsive">
                                    <button class="btn btn-primary btn-lg">
                                        <i class="bi bi-building me-2"></i> Register Pharmacy
                                    </button>
                                    <button class="btn btn-outline-secondary btn-lg">
                                        <i class="bi bi-book me-2"></i> Documentation
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5.3 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            
            // Toggle sidebar on mobile
            function toggleSidebar() {
                sidebar.classList.toggle('active');
                mainContent.classList.toggle('sidebar-open');
                sidebarOverlay.classList.toggle('active');
            }
            
            mobileMenuBtn.addEventListener('click', toggleSidebar);
            sidebarOverlay.addEventListener('click', toggleSidebar);
            
            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(event) {
                if (window.innerWidth < 992 && 
                    sidebar.classList.contains('active') &&
                    !sidebar.contains(event.target) &&
                    event.target !== mobileMenuBtn) {
                    toggleSidebar();
                }
            });
            
            // Handle window resize
            function handleResize() {
                if (window.innerWidth >= 992) {
                    sidebar.classList.add('active');
                    mainContent.classList.add('sidebar-open');
                    sidebarOverlay.classList.remove('active');
                } else {
                    sidebar.classList.remove('active');
                    mainContent.classList.remove('sidebar-open');
                }
            }
            
            window.addEventListener('resize', handleResize);
            handleResize(); // Initial call
            
            // Simple animations
            const cards = document.querySelectorAll('.service-card, .stat-card');
            cards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
                card.classList.add('animate__animated', 'animate__fadeInUp');
            });
        });
    </script>
</body>
</html>