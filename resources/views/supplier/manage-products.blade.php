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
                <h2 class="fw-bold">Manage Products</h2>
                <p class="text-muted">Add, edit, or update your products</p>
            </div>
            <div class="col-md-4 text-md-end">
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addProductModal">
                    <i class="fas fa-plus"></i> Add New Product
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Product Name</th>
                                <th>Serial Number</th>
                                <th>Shipping</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                            <tr>
                                <td>
                                    <img src="{{ $product->image_url ? asset($product->image_url) : 'https://via.placeholder.com/50' }}" 
                                         class="rounded shadow-sm" width="50" height="50" style="object-fit: cover;">
                                </td>
                                <td class="fw-bold">{{ $product->name }}</td>
                                <td>{{ $product->sku }}</td>
                                <td>
                                    @if(is_array($product->shipping_method))
                                        @foreach($product->shipping_method as $method)
                                            <span class="badge bg-secondary mb-1">{{ $method }}</span>
                                        @endforeach
                                    @else
                                        <span class="badge bg-secondary">{{ $product->shipping_method ?? 'N/A' }}</span>
                                    @endif
                                </td>
                                <td>{{ number_format($product->price, 2) }} SAR</td>
                                <td>
                                    <span class="badge {{ $product->stock_quantity > 10 ? 'bg-success' : 'bg-warning' }}">
                                        {{ $product->stock_quantity }}
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-info me-1" data-bs-toggle="modal" data-bs-target="#viewModal{{ $product->id }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#editModal{{ $product->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('supplier.products.delete', $product->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="text-center py-4">No products found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @foreach($products as $product)
        <div class="modal fade" id="viewModal{{ $product->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title">Product Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="{{ $product->image_url ? asset($product->image_url) : 'https://via.placeholder.com/150' }}" 
                             class="img-fluid rounded mb-3" style="max-height: 200px;">
                        <hr>
                        <div class="text-start">
                            <p><strong>Name:</strong> {{ $product->name }}</p>
                            <p><strong>Category:</strong> {{ $product->category }}</p>
                            <p><strong>Serial Number:</strong> {{ $product->sku }}</p>
                            <p><strong>Shipping Method:</strong> 
                                @if(is_array($product->shipping_method))
                                    {{ implode(', ', $product->shipping_method) }}
                                @else
                                    {{ $product->shipping_method }}
                                @endif
                            </p>
                            <p><strong>Price:</strong> {{ number_format($product->price, 2) }} SAR</p>
                            <p><strong>Stock:</strong> {{ $product->stock_quantity }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="editModal{{ $product->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Edit Product</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('supplier.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" name="name" class="form-control" value="{{ $product->name }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Category</label>
                                <select name="category" class="form-select">
                                    <option value="Electronics" {{ $product->category == 'Electronics' ? 'selected' : '' }}>Electronics</option>
                                    <option value="Clothing" {{ $product->category == 'Clothing' ? 'selected' : '' }}>Clothing</option>
                                    <option value="Food & Beverages" {{ $product->category == 'Food & Beverages' ? 'selected' : '' }}>Food & Beverages</option>
                                    <option value="Furniture" {{ $product->category == 'Furniture' ? 'selected' : '' }}>Furniture</option>
                                    <option value="Medical Supplies" {{ $product->category == 'Medical Supplies' ? 'selected' : '' }}>Medical Supplies</option>
                                    <option value="General" {{ $product->category == 'General' ? 'selected' : '' }}>General</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Serial Number</label>
                                <input type="text" name="sku" class="form-control" value="{{ $product->sku }}" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Price (SAR)</label>
                                    <input type="number" name="price" class="form-control" step="0.01" value="{{ $product->price }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Stock</label>
                                    <input type="number" name="stock_quantity" class="form-control" value="{{ $product->stock_quantity }}" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label d-block">Shipping Method</label>
                                <div class="btn-group w-100" role="group">
                                    @php $selectedMethods = is_array($product->shipping_method) ? $product->shipping_method : [$product->shipping_method]; @endphp
                                    
                                    <input type="checkbox" class="btn-check" name="shipping_method[]" id="land_edit{{ $product->id }}" value="Land" autocomplete="off" {{ in_array('Land', $selectedMethods) ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="land_edit{{ $product->id }}"><i class="fas fa-truck"></i> Land</label>

                                    <input type="checkbox" class="btn-check" name="shipping_method[]" id="air_edit{{ $product->id }}" value="Air" autocomplete="off" {{ in_array('Air', $selectedMethods) ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="air_edit{{ $product->id }}"><i class="fas fa-plane"></i> Air</label>

                                    <input type="checkbox" class="btn-check" name="shipping_method[]" id="sea_edit{{ $product->id }}" value="Sea" autocomplete="off" {{ in_array('Sea', $selectedMethods) ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="sea_edit{{ $product->id }}"><i class="fas fa-ship"></i> Sea</label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Change Image (Optional)</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary w-100">Update Product</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <div class="modal fade" id="addProductModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Add New Product</h5>
                    <button type="button" class="btn-close text-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('supplier.products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Product Name</label>
                            <input type="text" name="name" class="form-control" placeholder="Enter product name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select name="category" class="form-select" required>
                                <option value="" selected disabled>Select Category</option>
                                <option value="Electronics">Electronics</option>
                                <option value="Clothing">Clothing</option>
                                <option value="Food & Beverages">Food & Beverages</option>
                                <option value="Furniture">Furniture</option>
                                <option value="Medical Supplies">Medical Supplies</option>
                                <option value="General">General</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Serial Number</label>
                            <input type="text" name="sku" class="form-control" placeholder="SKU/Serial Number" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Price (SAR)</label>
                                <input type="number" name="price" class="form-control" step="0.01" placeholder="0.00" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Quantity</label>
                                <input type="number" name="stock_quantity" class="form-control" placeholder="0" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label d-block">Shipping Method</label>
                            <div class="btn-group w-100" role="group">
                                <input type="checkbox" class="btn-check" name="shipping_method[]" id="land_add" value="Land" autocomplete="off">
                                <label class="btn btn-outline-success" for="land_add"><i class="fas fa-truck"></i> Land</label>

                                <input type="checkbox" class="btn-check" name="shipping_method[]" id="air_add" value="Air" autocomplete="off">
                                <label class="btn btn-outline-success" for="air_add"><i class="fas fa-plane"></i> Air</label>

                                <input type="checkbox" class="btn-check" name="shipping_method[]" id="sea_add" value="Sea" autocomplete="off">
                                <label class="btn btn-outline-success" for="sea_add"><i class="fas fa-ship"></i> Sea</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Product Image</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success w-100">Save Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>