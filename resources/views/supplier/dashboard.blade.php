<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supplier Dashboard | Supply Chain.Web APP</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{asset('css/style.css')}}">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container">
            <a class="navbar-brand" href="{{ route('supplier.dashboard') }}">
                <i class="fas fa-truck"></i> Supply Chain.Web APP (Supplier)
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link active" href="{{ route('supplier.dashboard') }}"><i class="fas fa-home"></i> Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('supplier.products') }}"><i class="fas fa-boxes"></i> Manage Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('supplier.orders') }}"><i class="fas fa-shopping-cart"></i> Incoming Orders</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i> {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu border-0 shadow">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user-circle"></i> Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <a class="dropdown-item text-danger" href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">
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
        <div class="alert alert-success fade-in">
            <i class="fas fa-bell me-2"></i>
            Welcome back, <strong>{{ Auth::user()->name }}</strong>! You have <strong>{{ $pendingOrders }}</strong> new orders.
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-success bg-opacity-10 text-success"><i class="fas fa-box"></i></div>
                        <div class="ms-3"><div class="stat-value">{{ $totalProducts }}</div><div class="stat-label">Total Products</div></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card text-warning">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-warning bg-opacity-10 text-warning"><i class="fas fa-clock"></i></div>
                        <div class="ms-3"><div class="stat-value">{{ $pendingOrders }}</div><div class="stat-label">Pending Orders</div></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card text-info">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-info bg-opacity-10 text-info"><i class="fas fa-check-circle"></i></div>
                        <div class="ms-3"><div class="stat-value">{{ $completedOrders }}</div><div class="stat-label">Completed Orders</div></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card text-danger">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-danger bg-opacity-10 text-danger"><i class="fas fa-exclamation-triangle"></i></div>
                        <div class="ms-3"><div class="stat-value">{{ $lowStockCount }}</div><div class="stat-label">Low Stock</div></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <div class="card dashboard-card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Quick Actions</h5>
                        <div class="row g-2">
                            <div class="col-md-3">
                                <a href="{{ route('supplier.products') }}" class="btn btn-outline-success w-100">
                                    <i class="fas fa-plus"></i> Add Product
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('supplier.products') }}"  class="btn btn-outline-success w-100">
                                    <i class="fas fa-edit"></i> Update Stock
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="#" class="btn btn-outline-success w-100">
                                    <i class="fas fa-truck"></i> Process Orders
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="#" class="btn btn-outline-success w-100">
                                    <i class="fas fa-chart-bar"></i> View Reports
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-7">
                <div class="card dashboard-card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 border-0"><h5 class="mb-0 fw-bold">Recent Orders</h5></div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Product</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentOrders as $order)
                                    <tr>
                                        <td>#ORD-{{ str_pad($order->id, 3, '0', STR_PAD_LEFT) }}</td>
                                        <td>{{ $order->product->name }}</td>
                                        {{-- تم تعديل العملة هنا --}}
                                        <td>{{ number_format($order->total_price, 2) }} SAR</td>
                                        <td>
                                            <span class="badge rounded-pill 
                                                {{ $order->status == 'pending' ? 'bg-warning text-dark' : 'bg-success' }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#orderModal{{ $order->id }}">
                                                <i class="fas fa-eye"></i> View
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="5" class="text-center">No recent orders found.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <div class="card dashboard-card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 border-0"><h5 class="mb-0 fw-bold text-danger">Low Stock Alert</h5></div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            @forelse($lowStockProducts as $product)
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div><h6 class="fw-bold mb-1">{{ $product->name }}</h6><p class="text-muted small mb-0">SKU: {{ $product->sku }}</p></div>
                                <div class="text-end">
                                    <span class="badge bg-danger mb-1">{{ $product->stock_quantity }} left</span>
                                    <a href="{{ route('supplier.products') }}" class="btn btn-sm btn-outline-primary d-block">Restock</a>
                                </div>
                            </div>
                            @empty
                            <p class="text-muted text-center py-4">All products are well stocked! ✅</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @foreach($recentOrders as $order)
    <div class="modal fade" id="orderModal{{ $order->id }}" tabindex="-1" aria-labelledby="orderModalLabel{{ $order->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="orderModalLabel{{ $order->id }}">Order Details #ORD-{{ $order->id }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-start">
                    <div class="row g-3">
                        <div class="col-6"><strong>Product:</strong><br>{{ $order->product->name }}</div>
                        <div class="col-6"><strong>Quantity:</strong><br>{{ $order->quantity }}</div>
                        {{-- تم تعديل العملة في المودال هنا --}}
                        <div class="col-6"><strong>Total Price:</strong><br><span class="text-success fw-bold">{{ number_format($order->total_price, 2) }} SAR</span></div>
                        <div class="col-6"><strong>Status:</strong><br>{{ ucfirst($order->status) }}</div>
                        <div class="col-12"><hr></div>
                        <div class="col-12"><strong>Order Date:</strong><br>{{ $order->created_at->format('d M Y, h:i A') }}</div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>