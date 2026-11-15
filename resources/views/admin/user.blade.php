
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users | Supply Chain.Web APP</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{asset('css/style.css')}}">
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
                        <a class="nav-link active" href="{{ route('admin.users.index') }}">
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
                            <i class="fas fa-user"></i> {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user-circle"></i> Profile</a></li>
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
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row mb-4 align-items-center">
            <div class="col-md-8">
                <h2 class="fw-bold">User Management</h2>
                <p class="text-muted">View and manage system users</p>
                <button class="btn btn-warning shadow-sm" data-bs-toggle="modal" data-bs-target="#createUserModal">
                    <i class="fas fa-user-plus"></i> Add New User
                </button>
            </div>
            <div class="col-md-4">
                <form action="{{ route('admin.users.index') }}" method="GET">
                    @if(request('role'))
                        <input type="hidden" name="role" value="{{ request('role') }}">
                    @endif
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search by name or email..." value="{{ request('search') }}">
                        <button class="btn btn-warning" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row g-4 mb-4 text-center">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-3">
                    <div class="stat-value fw-bold fs-3 text-primary">{{ $totalCustomers ?? 0 }}</div>
                    <div class="stat-label text-muted">Customers</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-3">
                    <div class="stat-value fw-bold fs-3 text-success">{{ $totalSuppliers ?? 0 }}</div>
                    <div class="stat-label text-muted">Suppliers</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-3">
                    <div class="stat-value fw-bold fs-3 text-warning">{{ $totalAdmins ?? 0 }}</div>
                    <div class="stat-label text-muted">Admins</div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <ul class="nav nav-tabs mb-3">
                    <li class="nav-item">
                        <a class="nav-link {{ !request('role') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">All Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request('role') == 'customer' ? 'active' : '' }}" href="{{ route('admin.users.index', ['role' => 'customer']) }}">Customers</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request('role') == 'supplier' ? 'active' : '' }}" href="{{ route('admin.users.index', ['role' => 'supplier']) }}">Suppliers</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request('role') == 'admin' ? 'active' : '' }}" href="{{ route('admin.users.index', ['role' => 'admin']) }}">Admins</a>
                    </li>
                </ul>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                
                                @if(request()->get('role') === 'supplier')
                                    <th>Nationality</th>
                                @endif
                                <th>Role</th>
                                <th>Status</th>
                                <th>Joined Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>#{{ str_pad($user->id, 3, '0', STR_PAD_LEFT) }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                
                                @if(request()->get('role') === 'supplier')
                                    <td>{{ $user->nationality ?? '-' }}</td>
                                @endif
                                <td>
                                    <span class="badge {{ $user->role == 'admin' ? 'bg-warning text-dark' : ($user->role == 'supplier' ? 'bg-success' : 'bg-primary') }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $user->status == 'active' ? 'bg-success' : 'bg-secondary' }}">
                                        {{ ucfirst($user->status ?? 'active') }}
                                    </span>
                                </td>
                                <td>{{ $user->created_at->format('Y-m-d') }}</td>
                                <td>
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        
                                        <form action="{{ route('admin.users.status', $user) }}" method="POST" style="display:inline;">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="btn btn-sm {{ $user->status == 'active' ? 'btn-outline-secondary' : 'btn-outline-success' }}" title="Toggle Status">
                                                <i class="fas {{ $user->status == 'active' ? 'fa-ban' : 'fa-check' }}"></i>
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $users->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="createUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Add New User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3"><label class="form-label">Name</label><input type="text" name="name" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select name="role" class="form-select" id="createRole" onchange="toggleNationality('create')">
                                <option value="customer">Customer</option>
                                <option value="supplier">Supplier</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="mb-3 d-none" id="createNationalityGroup">
                            <label class="form-label">Nationality</label>
                            <input type="text" name="nationality" class="form-control" placeholder="Enter supplier nationality">
                        </div>
                        <div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label">Confirm Password</label><input type="password" name="password_confirmation" class="form-control" required></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-warning">Create User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @foreach($users as $user)
    <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.users.update', $user) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Edit User: {{ $user->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3"><label class="form-label">Name</label><input type="text" name="name" class="form-control" value="{{ $user->name }}" required></div>
                        <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="{{ $user->email }}" required></div>
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select name="role" class="form-select" onchange="toggleNationality('edit', {{ $user->id }})" id="editRole{{ $user->id }}">
                                <option value="customer" {{ $user->role == 'customer' ? 'selected' : '' }}>Customer</option>
                                <option value="supplier" {{ $user->role == 'supplier' ? 'selected' : '' }}>Supplier</option>
                                <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                        </div>
                        <div class="mb-3 {{ $user->role == 'supplier' ? '' : 'd-none' }}" id="editNationalityGroup{{ $user->id }}">
                            <label class="form-label">Nationality</label>
                            <input type="text" name="nationality" class="form-control" value="{{ $user->nationality }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleNationality(type, id = null) {
            if (type === 'create') {
                const role = document.getElementById('createRole').value;
                const group = document.getElementById('createNationalityGroup');
                role === 'supplier' ? group.classList.remove('d-none') : group.classList.add('d-none');
            } else {
                const role = document.getElementById('editRole' + id).value;
                const group = document.getElementById('editNationalityGroup' + id);
                role === 'supplier' ? group.classList.remove('d-none') : group.classList.add('d-none');
            }
        }
    </script>
</body>
</html>