<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports | Supply Chain.Web APP</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
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
                        <a class="nav-link" href="{{ route('admin.dashboard') }}">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.users.index') }}">
                            <i class="fas fa-users"></i> Users
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('admin.reports') }}">
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
                            <li><a class="dropdown-item text-danger" href="#"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-4">
        <h2 class="fw-bold mb-4">Analytics & Reports</h2>

        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Report Type</label>
                        <select class="form-select">
                            <option>Sales Report</option>
                            <option>Inventory Report</option>
                            <option>User Activity</option>
                            <option>Supplier Performance</option>
                        </select>
                    </div>
                    <!-- قمت بتوسيع حقول التاريخ لملء الفراغ بعد حذف الزر -->
                    <div class="col-md-3">
                        <label class="form-label fw-bold">From Date</label>
                        <input type="date" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">To Date</label>
                        <input type="date" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">&nbsp;</label>
                        <a href="{{ route('admin.reports.export') }}" class="btn btn-success w-100">
                            <i class="fas fa-download"></i> Export Report
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="fw-bold mb-0">Sales Overview</h5>
            </div>
            <div class="card-body">
                <div class="row g-4 mb-4">
                    <div class="col-md-3">
                        <div class="border rounded p-3 text-center">
                            <h3 class="text-primary fw-bold">{{ number_format($totalRevenue, 2) }} SAR</h3>
                            <p class="text-muted mb-0">Total Revenue</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3 text-center">
                            <h3 class="text-success fw-bold">{{ $totalOrders }}</h3>
                            <p class="text-muted mb-0">Total Orders</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3 text-center">
                            <h3 class="text-info fw-bold">{{ number_format($avgOrder, 2) }} SAR</h3>
                            <p class="text-muted mb-0">Avg. Order Value</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3 text-center">
                            <h3 class="text-warning fw-bold">+15%</h3>
                            <p class="text-muted mb-0">Growth Rate</p>
                        </div>
                    </div>
                </div>

                <h6 class="fw-bold mb-3">Top Selling Products</h6>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Units Sold</th>
                            <th>Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topProducts as $product)
                        <tr>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->category }}</td>
                            <td>{{ $product->sold_count }}</td>
                            <td>{{ number_format($product->price * $product->sold_count, 2) }} SAR</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-white">
                <h5 class="fw-bold mb-0">Inventory Status</h5>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3">Stock Levels</h6>
                        <div class="progress mb-3" style="height: 30px;">
                            <div class="progress-bar bg-success" style="width: 60%">In Stock</div>
                            <div class="progress-bar bg-warning" style="width: 25%">Low Stock</div>
                            <div class="progress-bar bg-danger" style="width: 15%">Out of Stock</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3">Category Distribution</h6>
                        <div class="progress mb-3" style="height: 30px;">
                            <div class="progress-bar bg-primary" style="width: 40%">Electronics</div>
                            <div class="progress-bar bg-info" style="width: 25%">Clothing</div>
                            <div class="progress-bar bg-success" style="width: 20%">Furniture</div>
                        </div>
                    </div>
                </div>

                <h6 class="fw-bold mb-3 mt-4">Low Stock Alert</h6>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Current Stock</th>
                            <th>Price</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lowStockProducts as $product)
                        <tr>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->stock_quantity }}</td>
                            <td>{{ number_format($product->price, 2) }} SAR</td>
                            <td>
                                @if($product->stock_quantity <= 0)
                                    <span class="badge bg-danger">Out of Stock</span>
                                @elseif($product->stock_quantity <= 5)
                                    <span class="badge bg-danger">Critical</span>
                                @else
                                    <span class="badge bg-warning">Low</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>