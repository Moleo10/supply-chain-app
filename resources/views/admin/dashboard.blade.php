<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Supply Chain.Web APP</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-warning">
        <div class="container">
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
                <i class="fas fa-truck"></i> Supply Chain.Web APP (Admin)
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('admin.dashboard') }}">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.users.index') }}">
                            <i class="fas fa-users"></i> Users
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.reports') }}">
                            <i class="fas fa-chart-bar"></i> Reports
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i> Admin
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user-circle"></i> Profile</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-cog"></i> Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); this.closest('form').submit();">
                                        <i class="fas fa-sign-out-alt"></i> Logout
                                    </a>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-4">
        <h2 class="fw-bold mb-4">Admin Dashboard</h2>

        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="fas fa-users"></i></div>
                        <div class="ms-3">
                            <div class="stat-value">{{ $totalUsers }}</div>
                            <div class="stat-label">Total Users</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-success bg-opacity-10 text-success"><i class="fas fa-box"></i></div>
                        <div class="ms-3">
                            <div class="stat-value">{{ $totalProducts }}</div>
                            <div class="stat-label">Total Products</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-info bg-opacity-10 text-info"><i class="fas fa-shopping-cart"></i></div>
                        <div class="ms-3">
                            <div class="stat-value">{{ number_format($totalOrders) }}</div>
                            <div class="stat-label">Total Orders</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-warning bg-opacity-10 text-warning"><i class="fas fa-money-bill-wave"></i></div>
                        <div class="ms-3">
                            <div class="stat-value">{{ number_format($totalRevenue, 2) }} SAR</div>
                            <div class="stat-label">Total Revenue</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="card dashboard-card">
                    <div class="card-header bg-white"><h5 class="fw-bold mb-0">Orders by Status</h5></div>
                    <div class="card-body">
                        @php
                            $total = max(array_sum($statusCounts), 1);
                            $statusColors = ['pending' => 'warning', 'processing' => 'primary', 'shipped' => 'info', 'delivered' => 'success', 'cancelled' => 'secondary'];
                        @endphp
                        <div class="progress mb-3" style="height: 25px;">
                            @foreach($statusCounts as $status => $count)
                                @if($count > 0)
                                    <div class="progress-bar bg-{{ $statusColors[$status] }}" style="width: {{ ($count / $total) * 100 }}%" title="{{ ucfirst($status) }}: {{ $count }}"></div>
                                @endif
                            @endforeach
                        </div>
                        <div class="d-flex flex-wrap justify-content-center gap-2 mt-4">
                            @foreach($statusCounts as $status => $count)
                                <span class="badge bg-{{ $statusColors[$status] }}">{{ $count }} {{ ucfirst($status) }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card dashboard-card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">Monthly Revenue</h5>
                        <small class="text-muted">SAR</small>
                    </div>
                    <div class="card-body" style="height: 250px;">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card dashboard-card">
                    <div class="card-header bg-white"><h5 class="fw-bold mb-0">Recent Activities</h5></div>
                    <div class="card-body">
                        <div class="list-group">
                            @foreach($allActivities as $activity)
                                <div class="list-group-item">
                                    @if($activity instanceof \App\Models\Order)
                                        <i class="fas {{ $activity->status == 'shipped' ? 'fa-truck text-info' : 'fa-shopping-cart text-primary' }} me-2"></i>
                                        Order <strong>#ORD-{{ $activity->id }}</strong> is {{ $activity->status }}
                                    @elseif($activity instanceof \App\Models\User)
                                        <i class="fas fa-user-plus text-success me-2"></i>
                                        New user registered: {{ $activity->name }}
                                    @elseif($activity instanceof \App\Models\Product)
                                        <i class="fas fa-box text-warning me-2"></i>
                                        Low stock alert: {{ $activity->name }} ({{ $activity->stock_quantity }} left)
                                    @endif
                                    <small class="text-muted float-end">{{ ($activity->updated_at ?? $activity->created_at)->diffForHumans() }}</small>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const ctx = document.getElementById('revenueChart').getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 200);
        gradient.addColorStop(0, 'rgba(255, 193, 7, 0.6)');
        gradient.addColorStop(1, 'rgba(255, 193, 7, 0.05)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json(array_keys($monthlyRevenue)),
                datasets: [{
                    label: 'Revenue',
                    data: @json(array_values($monthlyRevenue)),
                    fill: true,
                    backgroundColor: gradient,
                    borderColor: '#ffc107',
                    borderWidth: 3,
                    tension: 0.4,
                    pointRadius: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f0f0f0' } },
                    x: { grid: { display: false } }
                }
            }
        });
    </script>
</body>
</html>