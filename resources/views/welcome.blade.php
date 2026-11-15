<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supply Chain Application | Home</title>
    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <i class="fas fa-truck text-primary"></i>
                <span class="fw-bold">Supply Chain.Web APP</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ url('/') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                </ul>
                @if (Route::has('login'))
                <div class="d-flex ms-lg-3">
                    @auth
            <a href="{{ url('/customer/dashboard') }}" class="btn btn-primary">Go to Dashboard</a>
        @else
                    <a href="{{ route('login') }}" class="btn btn-outline-primary me-2">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Register
                    </a>
                    @endauth
                </div>
                @endif
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">Supply Chain Application</h1>
                    <p class="lead mb-4">A unified digital platform connecting suppliers, customers, and administrators in one place. Process automation, real-time tracking, and smart analytics.</p>
                    <div class="d-flex gap-3">
                        <!-- التعديل هنا فقط لربط الصفحة بالـ Route الصحيح -->
                        <a href="{{ route('register') }}" class="btn btn-light btn-lg">Get Started Free</a>
                        <a href="#features" class="btn btn-outline-light btn-lg">Learn More</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-5">
        <div class="container">
            <h2 class="text-center fw-bold mb-5">System Features</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card p-4">
                        <div class="feature-icon mb-3">
                            <i class="fas fa-users fa-3x text-primary"></i>
                        </div>
                        <h4 class="fw-bold">Three Different Roles</h4>
                        <p class="text-muted">Customized interfaces for suppliers, customers, and administrators with specific permissions for each role.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card p-4">
                        <div class="feature-icon mb-3">
                            <i class="fas fa-boxes fa-3x text-primary"></i>
                        </div>
                        <h4 class="fw-bold">Inventory Management</h4>
                        <p class="text-muted">Automatic inventory updates with each sale, and alerts when stock runs out.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card p-4">
                        <div class="feature-icon mb-3">
                            <i class="fas fa-chart-line fa-3x text-primary"></i>
                        </div>
                        <h4 class="fw-bold">Reports & Analytics</h4>
                        <p class="text-muted">Interactive dashboards and performance reports for better decision making.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card p-4">
                        <div class="feature-icon mb-3">
                            <i class="fas fa-bell fa-3x text-primary"></i>
                        </div>
                        <h4 class="fw-bold">Instant Notifications</h4>
                        <p class="text-muted">Email and dashboard alerts for order updates and status changes.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card p-4">
                        <div class="feature-icon mb-3">
                            <i class="fas fa-truck fa-3x text-primary"></i>
                        </div>
                        <h4 class="fw-bold">Order Tracking</h4>
                        <p class="text-muted">Real-time order status tracking from purchase to delivery.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card p-4">
                        <div class="feature-icon mb-3">
                            <i class="fas fa-shield-alt fa-3x text-primary"></i>
                        </div>
                        <h4 class="fw-bold">Secure & Reliable</h4>
                        <p class="text-muted">Advanced role-based access control and data encryption.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <h2 class="fw-bold mb-4">About The Project</h2>
                    <p class="lead">SCA System is a comprehensive project aimed at digitizing and automating supply chain operations for small and medium enterprises.</p>
                    <ul class="list-unstyled">
                        <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i> Reduce human errors and administrative delays</li>
                        <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i> Improve communication and transparency in the supply chain</li>
                        <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i> Provide real-time visibility of inventory and orders</li>
                        <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i> Support decision-making through accurate reports</li>
                    </ul>
                </div>
                <div class="col-lg-6">
                    <div class="bg-white p-4 rounded-3 shadow-sm">
                        <h4 class="fw-bold mb-3">Technologies Used</h4>
                        <div class="row">
                            <div class="col-6">
                                <p><i class="fab fa-laravel text-danger me-2"></i> Laravel (Backend)</p>
                                <p><i class="fas fa-database text-primary me-2"></i> MySQL</p>
                                <p><i class="fab fa-bootstrap text-purple me-2"></i> Bootstrap</p>
                            </div>
                            <div class="col-6">
                                <p><i class="fab fa-react text-info me-2"></i> React/Blade</p>
                                <p><i class="fab fa-github me-2"></i> GitHub</p>
                                <p><i class="fab fa-aws text-warning me-2"></i> AWS/Heroku</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5 class="fw-bold mb-3">SCM System</h5>
                    <p>Integrated Supply Chain Management System. Senior project for College of Computing & Informatics students.</p>
                </div>
                <div class="col-md-2">
                    <h5 class="fw-bold mb-3">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="{{ url('/') }}" class="text-decoration-none">Home</a></li>
                        <li><a href="#features" class="text-decoration-none">Features</a></li>
                        <li><a href="#about" class="text-decoration-none">About</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5 class="fw-bold mb-3">User Roles</h5>
                    <ul class="list-unstyled">
                        <li><a href="{{ url('/customer/dashboard') }}" class="text-decoration-none">Customer</a></li>
                        <li><a href="{{ url('/supplier/dashboard') }}" class="text-decoration-none">Supplier</a></li>
                        <li><a href="{{ url('/admin/dashboard') }}" class="text-decoration-none">Admin</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5 class="fw-bold mb-3">Contact Us</h5>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-envelope me-2"></i> info@scm.com</li>
                        <li><i class="fas fa-phone me-2"></i> +966 123 456 789</li>
                    </ul>
                </div>
            </div>
            <hr>
            <p class="text-center mb-0">&copy; 2026 SCM System - All Rights Reserved</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>