<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Products | Supply Chain.Web APP</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        .shipping-badge {
            font-size: 0.75rem;
            padding: 4px 8px;
            border-radius: 5px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            display: inline-block;
            margin-bottom: 5px;
            margin-right: 2px;
        }
        .product-image {
            height: 200px;
            object-fit: cover;
        }
        .product-price {
            color: #0d6efd;
            font-size: 1.25rem;
            font-weight: bold;
        }
        /* تنسيق اسم المتجر والوسيلة */
        .info-label {
            display: block;
            font-size: 0.75rem;
            color: #6c757d;
            margin-bottom: 2px;
        }
        .store-name {
            font-size: 0.85rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }
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
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('customer.dashboard') }}">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('customer.products') }}">
                            <i class="fas fa-box"></i> Products
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('customer.orders') }}">
                            <i class="fas fa-shopping-cart"></i> My Orders
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i> {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user-circle"></i> Profile</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-cog"></i> Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger border-0 bg-transparent">
                                        <i class="fas fa-sign-out-alt"></i> Logout
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
        <div class="row mb-4">
            <div class="col-md-8">
                <h2 class="fw-bold">Browse Products</h2>
                <p class="text-muted">Discover our wide range of products</p>
            </div>
            <div class="col-md-4">
                <form action="{{ route('customer.products') }}" method="GET">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search products..." value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <div class="card dashboard-card p-3">
                    <form action="{{ route('customer.products') }}" method="GET" class="row g-3">
                        <div class="col-md-2">
                            <select name="category" class="form-select" onchange="this.form.submit()">
                                <option value="">All Categories</option>
                                <option value="Electronics" {{ request('category') == 'Electronics' ? 'selected' : '' }}>Electronics</option>
                                <option value="Clothing" {{ request('category') == 'Clothing' ? 'selected' : '' }}>Clothing</option>
                                <option value="Food & Beverages" {{ request('category') == 'Food & Beverages' ? 'selected' : '' }}>Food & Beverages</option>
                                <option value="Furniture" {{ request('category') == 'Furniture' ? 'selected' : '' }}>Furniture</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="sort" class="form-select" onchange="this.form.submit()">
                                <option value="low_high" {{ request('sort') == 'low_high' ? 'selected' : '' }}>Price: Low to High</option>
                                <option value="high_low" {{ request('sort') == 'high_low' ? 'selected' : '' }}>Price: High to Low</option>
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <select name="shipping_method" class="form-select" onchange="this.form.submit()">
                                <option value="">Shipping Method</option>
                                <option value="Air" {{ request('shipping_method') == 'Air' ? 'selected' : '' }}>Air Shipping</option>
                                <option value="Land" {{ request('shipping_method') == 'Land' ? 'selected' : '' }}>Land Shipping</option>
                                <option value="Sea" {{ request('shipping_method') == 'Sea' ? 'selected' : '' }}>Sea Shipping</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <select name="supplier_id" class="form-select" onchange="this.form.submit()">
                                <option value="">All Suppliers</option>
                                @php $suppliers = \App\Models\User::where('role', 'supplier')->get(); @endphp
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <div class="form-check mt-1">
                                <input class="form-check-input" type="checkbox" name="in_stock" id="inStock" value="1" {{ request('in_stock') ? 'checked' : '' }} onchange="this.form.submit()">
                                <label class="form-check-label" for="inStock">
                                    In Stock
                                </label>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row g-4">
            @foreach($products as $product)
            <div class="col-md-3">
                <div class="card product-card h-100">
                    @if($product->stock_quantity > 0)
                        <span class="position-absolute top-0 end-0 m-2 badge bg-success">In Stock</span>
                    @else
                        <span class="position-absolute top-0 end-0 m-2 badge bg-danger">Out of Stock</span>
                    @endif
                    
                    <img src="{{ $product->image_url ? asset($product->image_url) : 'https://placehold.co/300x200?text=No+Image' }}" 
                         class="card-img-top product-image" 
                         alt="{{ $product->name }}">
                    
                    <div class="card-body d-flex flex-column">
                        <h6 class="fw-bold">{{ $product->name }}</h6>
                        <p class="text-muted small mb-1">{{ $product->category }}</p>
                        
                        <p class="product-price mb-2">{{ number_format($product->price, 2) }} <span style="font-size: 0.9rem;">SAR</span></p>

                        <div class="mb-2">
                            <span class="info-label">Shipping method:</span>
                            @if($product->shipping_method && is_array($product->shipping_method))
                                @foreach($product->shipping_method as $method)
                                    <div class="shipping-badge text-primary">
                                        <i class="fas {{ $method == 'Air' ? 'fa-plane' : ($method == 'Sea' ? 'fa-ship' : 'fa-truck') }}"></i> {{ $method }}
                                    </div>
                                @endforeach
                            @else
                                <div class="shipping-badge text-muted">
                                    <i class="fas fa-box"></i> Standard
                                </div>
                            @endif
                        </div>

                        <div class="mb-2">
                            <span class="info-label">Sold by:</span>
                            <div class="store-name">
                                <i class="fas fa-store text-primary me-1"></i> {{ $product->user->name ?? 'Unknown Store' }}
                            </div>
                        </div>

                        <div class="mt-auto d-grid gap-2">
                            <form action="{{ route('customer.orders.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn btn-primary w-100 {{ $product->stock_quantity <= 0 ? 'disabled' : '' }}">
                                    <i class="fas fa-cart-plus"></i> {{ $product->stock_quantity <= 0 ? 'Out of Stock' : 'Add to Cart' }}
                                </button>
                            </form>
                            <button class="btn btn-outline-danger">
                                <i class="far fa-heart"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="row mt-4">
            <div class="col-12 d-flex justify-content-center">
                {{ $products->links() }}
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>