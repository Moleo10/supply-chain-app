<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard | Supply Chain.Web APP</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{asset('css/style.css')}}">
    <style>
        .product-image { height: 200px; object-fit: cover; }
        .shipping-badge { font-size: 0.7rem; padding: 4px 8px; border-radius: 5px; background-color: #f8f9fa; border: 1px solid #dee2e6; display: inline-block; margin-bottom: 5px; margin-right: 4px; }
        .store-box { background-color: #f1f8ff; padding: 8px; border-radius: 8px; margin-top: 5px; border-left: 3px solid #0d6efd; }
        
        /* CSS شريط التتبع */
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
                    <li class="nav-item"><a class="nav-link active" href="{{ route('customer.dashboard') }}"><i class="fas fa-home"></i> Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('customer.products') }}"><i class="fas fa-box"></i> Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('customer.orders') }}"><i class="fas fa-shopping-cart"></i> My Orders</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user"></i> {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user-circle me-2"></i> Profile</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i> Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" id="logout-form">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
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

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show container mt-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="container my-4">
        <div class="alert alert-info fade-in">
            <i class="fas fa-bell me-2"></i>
            Welcome back, {{ Auth::user()->name }}! Your dashboard is ready.
        </div>

        {{-- الإحصائيات --}}
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="stat-card p-3 border rounded bg-white shadow-sm">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary p-3 rounded-circle"><i class="fas fa-shopping-bag"></i></div>
                        <div class="ms-3">
                            <div class="stat-value h4 fw-bold mb-0">{{ $stats['total_orders'] }}</div>
                            <div class="stat-label text-muted small">Total Orders</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- الطلبات الأخيرة --}}
        <div class="row">
            <div class="col-12">
                <div class="card dashboard-card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">Recent Orders</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Date</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentOrders as $order)
                                    <tr>
                                        <td><span class="fw-bold">#ORD-{{ str_pad($order->id, 3, '0', STR_PAD_LEFT) }}</span></td>
                                        <td>{{ $order->created_at->format('Y-m-d') }}</td>
                                        <td>{{ number_format($order->total_price, 2) }} SAR</td>
                                        <td>
                                            {{-- تم تعديل الألوان هنا لتطابق صفحة الطلبات --}}
                                            <span class="badge rounded-pill {{ $order->status == 'cancelled' ? 'bg-danger' : ($order->status == 'processing' ? 'bg-primary' : ($order->status == 'delivered' ? 'bg-success' : 'bg-info')) }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary view-order-btn" 
                                                    data-id="{{ str_pad($order->id, 3, '0', STR_PAD_LEFT) }}" 
                                                    data-date="{{ $order->created_at->format('Y-m-d') }}"
                                                    data-total="{{ number_format($order->total_price, 2) }}"
                                                    data-status="{{ strtolower($order->status) }}">
                                                <i class="fas fa-eye"></i> View
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="5" class="text-center">No orders found yet.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- المنتجات الموصى بها --}}
        <div class="row mt-4">
            <div class="col-12"><h5 class="fw-bold mb-3">Recommended for You</h5></div>
            @foreach($recommendedProducts as $product)
            <div class="col-md-3">
                <div class="card product-card mb-4 shadow-sm h-100 position-relative border-0">
                    @if($product->stock_quantity > 0)
                        <span class="position-absolute top-0 end-0 m-2 badge bg-success" style="z-index: 1;">In Stock</span>
                    @endif
                    <img src="{{ $product->image_url ? asset($product->image_url) : 'https://placehold.co/300x200?text=No+Image' }}" class="card-img-top product-image" alt="{{ $product->name }}">
                    <div class="card-body d-flex flex-column">
                        <h6 class="fw-bold mb-1">{{ $product->name }}</h6>
                        <p class="text-muted small mb-2">{{ $product->category }}</p>
                        <p class="product-price text-primary fw-bold mb-2">{{ number_format($product->price, 2) }} SAR</p>
                        
                        <div class="mt-auto">
                            <form action="{{ route('customer.orders.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn btn-primary w-100 {{ $product->stock_quantity <= 0 ? 'disabled' : '' }}">
                                    <i class="fas fa-cart-plus me-1"></i> Add to Cart
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- نافذة التفاصيل --}}
    <div class="modal fade" id="orderModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Order Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                else if(status === 'out for delivery') { s1="active"; s2="active"; s3="active"; }
                else if(status === 'delivered') { s1="active"; s2="active"; s3="active"; s4="active"; }

                const modalBody = document.getElementById('modal-content');
                modalBody.innerHTML = `
                    <div class="p-2">
                        <div class="d-flex justify-content-between mb-3 small text-muted">
                            <div><strong>Order ID:</strong> #ORD-${id}</div>
                            <div><strong>Date:</strong> ${date}</div>
                        </div>
                        <ul class="progressbar">
                            <li class="${s1}">Confirmed</li>
                            <li class="${s2}">Shipped</li>
                            <li class="${s3}">Out for Delivery</li>
                            <li class="${s4}">Delivered</li>
                        </ul>
                        <div class="text-center mt-3 pt-3 border-top">
                            <h5 class="text-primary fw-bold mb-0">Total: ${total} SAR</h5>
                        </div>
                    </div>
                `;
                new bootstrap.Modal(document.getElementById('orderModal')).show();
            });
        });
    </script>
</body>
</html>