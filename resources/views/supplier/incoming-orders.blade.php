<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products | Supply Chain.Web APP</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
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
                        <ul class="dropdown-menu">
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
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="fw-bold">Incoming Orders</h2>
            <p class="text-muted">Process and manage customer orders</p>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card dashboard-card p-3 shadow-sm border-0">
                <div class="row text-center">
                    <div class="col-md-3">
                        <h3 class="text-primary fw-bold" id="count-pending">{{ $orders->where('status', 'pending')->count() }}</h3>
                        <p class="text-muted mb-0">Pending</p>
                    </div>
                    <div class="col-md-3">
                        <h3 class="text-warning fw-bold" id="count-processing">{{ $orders->where('status', 'processing')->count() }}</h3>
                        <p class="text-muted mb-0">Processing</p>
                    </div>
                    <div class="col-md-3">
                        <h3 class="text-info fw-bold" id="count-shipped">{{ $orders->where('status', 'shipped')->count() }}</h3>
                        <p class="text-muted mb-0">Shipped</p>
                    </div>
                    <div class="col-md-3">
                        <h3 class="text-success fw-bold" id="count-delivered">{{ $orders->where('status', 'delivered')->count() }}</h3>
                        <p class="text-muted mb-0">Delivered</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card dashboard-card shadow-sm border-0">
        <div class="card-body">
            <ul class="nav nav-tabs mb-3" id="orderTabs">
                <li class="nav-item"><a class="nav-link active" href="#" data-filter="all">All Orders</a></li>
                <li class="nav-item"><a class="nav-link" href="#" data-filter="pending">Pending</a></li>
                <li class="nav-item"><a class="nav-link" href="#" data-filter="processing">Processing</a></li>
                <li class="nav-item"><a class="nav-link" href="#" data-filter="shipped">Shipped</a></li>
            </ul>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Product Details</th>
                            <th>Total Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        <tr class="order-row" data-status="{{ strtolower($order->status) }}">
                            <td><span class="fw-bold text-dark">#ORD-{{ $order->id }}</span></td>
                            <td>{{ \Carbon\Carbon::parse($order->created_at)->format('Y-m-d') }}</td>
                            <td>{{ $order->user->name ?? 'Deleted user' }}</td>
                            <td>{{ $order->product->name ?? 'Product #'.$order->product_id }} (x{{ $order->quantity }})</td>
                            {{-- تم تعديل العملة هنا --}}
                            <td class="text-success fw-bold">{{ number_format($order->total_price, 2) }} SAR</td>
                            <td>
                                <select class="form-select form-select-sm status-select" data-id="{{ $order->id }}">
                                    <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                                    <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                                    <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                </select>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modal{{ $order->id }}">
                                    <i class="fas fa-eye"></i> View
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@foreach($orders as $order)
<div class="modal fade" id="modal{{ $order->id }}" tabindex="-1" aria-labelledby="modalLabel{{ $order->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalLabel{{ $order->id }}">Order Details #{{ $order->id }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-start">
                <div class="mb-3"><strong>Date:</strong> {{ $order->created_at }}</div>
                <div class="mb-3"><strong>Product:</strong> {{ $order->product->name ?? '#'.$order->product_id }} ({{ $order->product->sku ?? '-' }})</div>
                <div class="mb-3"><strong>Customer:</strong> {{ $order->user->name ?? 'Deleted user' }}</div>
                <div class="mb-3"><strong>Quantity:</strong> {{ $order->quantity }}</div>
                {{-- تم تعديل العملة هنا --}}
                <div class="mb-3"><strong>Total Paid:</strong> {{ number_format($order->total_price, 2) }} SAR</div>
                <div><strong>Status:</strong> {{ ucfirst($order->status) }}</div>
            </div>
        </div>
    </div>
</div>
@endforeach

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // Filter Tabs
        const tabs = document.querySelectorAll('#orderTabs .nav-link');
        const rows = document.querySelectorAll('.order-row');

        tabs.forEach(tab => {
            tab.addEventListener('click', function(e) {
                e.preventDefault();
                tabs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                const filter = this.getAttribute('data-filter');
                rows.forEach(row => {
                    row.style.display = (filter === 'all' || row.dataset.status === filter) ? '' : 'none';
                });
            });
        });

        // ✅ AJAX STATUS UPDATE (FIXED + COUNTS UPDATE)
        document.querySelectorAll('.status-select').forEach(select => {
            select.addEventListener('change', function() {

                const id = this.dataset.id;
                const newStatus = this.value;
                const row = this.closest('.order-row');

                fetch(`/supplier/orders/${id}/update-status`, { 
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ status: newStatus })
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {

                        // تحديث الصف
                        row.dataset.status = newStatus;

                        // تحديث الأرقام
                        document.getElementById('count-pending').innerText = data.counts.pending;
                        document.getElementById('count-processing').innerText = data.counts.processing;
                        document.getElementById('count-shipped').innerText = data.counts.shipped;
                        document.getElementById('count-delivered').innerText = data.counts.delivered;
                    }
                });

            });
        });

    });
</script>

</body>
</html>