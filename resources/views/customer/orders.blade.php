<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>My Orders | Supply Chain.Web APP</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        .progressbar { display: flex; justify-content: space-between; padding: 0; margin: 30px 0; }
        .progressbar li { list-style-type: none; font-size: 11px; width: 25%; text-align: center; position: relative; color: #6c757d; }
        .progressbar li:before { content: ""; width: 20px; height: 20px; display: block; background: #ddd; border-radius: 50%; margin: 0 auto 5px auto; }
        .progressbar li:after { content: ''; width: 100%; height: 2px; background: #ddd; position: absolute; left: -50%; top: 9px; z-index: -1; }
        .progressbar li:first-child:after { display: none; }
        .progressbar li.active { color: #0d6efd; font-weight: bold; }
        .progressbar li.active:before { background: #0d6efd; content: "\f00c"; font-family: "Font Awesome 6 Free"; font-weight: 900; color: white; line-height: 20px; font-size: 12px; }
        .progressbar li.active:after { background: #0d6efd; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ route('customer.dashboard') }}">
                <i class="fas fa-truck"></i> Supply Chain.Web APP
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="{{ route('customer.dashboard') }}"><i class="fas fa-home"></i> Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('customer.products') }}"><i class="fas fa-box"></i> Products</a></li>
                    <li class="nav-item"><a class="nav-link active" href="{{ route('customer.orders') }}"><i class="fas fa-shopping-cart"></i> My Orders</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user"></i> {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user-circle me-2"></i> Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger border-0 bg-transparent">
                                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-4">
        <div class="row mb-4 text-center text-md-start">
            <div class="col-md-8">
                <h2 class="fw-bold">My Orders</h2>
                <p class="text-muted">Track and manage your orders</p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="{{ route('customer.products') }}" class="btn btn-primary"><i class="fas fa-plus"></i> New Order</a>
            </div>
        </div>

        <ul class="nav nav-tabs mb-4">
            <li class="nav-item"><a class="nav-link {{ !request('status') ? 'active' : '' }}" href="{{ route('customer.orders') }}">All</a></li>
            @foreach(['processing', 'shipped', 'delivered', 'cancelled'] as $status)
            <li class="nav-item">
                <a class="nav-link {{ request('status') == $status ? 'active' : '' }}" href="{{ route('customer.orders', ['status' => $status]) }}">{{ ucfirst($status) }}</a>
            </li>
            @endforeach
        </ul>

        <div class="row">
            <div class="col-12">
                @forelse($orders as $order)
                    <div class="card mb-3 shadow-sm border-0">
                        <div class="card-body">
                            <div class="row align-items-center text-center text-md-start">
                                <div class="col-md-2 mb-3 mb-md-0">
                                    <img src="{{ $order->product && $order->product->image_url ? asset($order->product->image_url) : 'https://via.placeholder.com/100x100?text=No+Image' }}" 
                                         class="img-fluid rounded shadow-sm" style="max-height: 80px; width: 80px; object-fit: cover;">
                                </div>
                                <div class="col-md-3">
                                    <h6 class="fw-bold mb-1">{{ $order->product->name }}</h6>
                                    <p class="text-muted small mb-0">Order #ORD-{{ str_pad($order->id, 3, '0', STR_PAD_LEFT) }}</p>
                                    <p class="text-muted small">Placed on: {{ $order->created_at->format('M d, Y') }}</p>
                                </div>
                                <div class="col-md-2">
                                    <p class="mb-0 fw-bold text-primary">{{ number_format($order->total_price, 2) }} SAR</p>
                                    <p class="text-muted small">Qty: {{ $order->quantity }}</p>
                                </div>
                                <div class="col-md-2 mb-3 mb-md-0">
                                    <!-- تم إصلاح كود الحالة هنا لضمان ظهور النص واللون بشكل صحيح -->
                                    <span class="badge rounded-pill {{ $order->status == 'cancelled' ? 'bg-danger' : ($order->status == 'processing' ? 'bg-primary' : ($order->status == 'delivered' ? 'bg-success' : 'bg-info')) }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </div>
                                <div class="col-md-3 text-md-end">
                                    <button class="btn btn-sm btn-outline-primary me-1 view-order-btn" 
                                        data-id="{{ str_pad($order->id, 3, '0', STR_PAD_LEFT) }}" 
                                        data-date="{{ $order->created_at->format('M d, Y') }}"
                                        data-total="{{ number_format($order->total_price, 2) }}"
                                        data-status="{{ strtolower($order->status) }}">
                                        <i class="fas fa-eye"></i> Track
                                    </button>
                                    
                                    @if($order->status == 'pending' || $order->status == 'processing')
                                        <form action="{{ route('customer.orders.cancel', $order->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to cancel this order?')">
                                                <i class="fas fa-times"></i> Cancel
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="card p-5 text-center shadow-sm border-0">
                        <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                        <h4>No orders found</h4>
                        <a href="{{ route('customer.products') }}" class="btn btn-primary mt-2">Start Shopping</a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Tracking Modal -->
    <div class="modal fade" id="orderModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Order Tracking</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modal-content"></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll('.view-order-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const date = this.getAttribute('data-date');
                const total = this.getAttribute('data-total');
                const status = this.getAttribute('data-status');

                let s1="", s2="", s3="", s4="";
                if(status === 'pending' || status === 'processing') s1="active";
                else if(status === 'shipped') { s1="active"; s2="active"; }
                else if(status === 'delivered') { s1="active"; s2="active"; s3="active"; s4="active"; }

                const modalBody = document.getElementById('modal-content');
                modalBody.innerHTML = `
                    <div class="p-2">
                        <div class="d-flex justify-content-between mb-3 small text-muted">
                            <span><strong>Order:</strong> #ORD-${id}</span>
                            <span><strong>Date:</strong> ${date}</span>
                        </div>
                        <ul class="progressbar">
                            <li class="${s1}">Confirmed</li>
                            <li class="${s2}">Shipped</li>
                            <li class="${s3}">Out for Delivery</li>
                            <li class="${s4}">Delivered</li>
                        </ul>
                        <div class="text-center mt-3 border-top pt-3">
                             <p class="mb-0"><strong class="text-dark">Total:</strong> <span class="text-primary fw-bold">${total} SAR</span></p>
                        </div>
                    </div>
                `;
                new bootstrap.Modal(document.getElementById('orderModal')).show();
            });
        });
    </script>
</body>
</html>