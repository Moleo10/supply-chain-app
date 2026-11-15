<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | SCM System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    
    <style>
        /* إضافة خطوط تدعم رموز الأعلام بشكل أفضل على مختلف الأنظمة */
        #nationality {
            font-family: "Segoe UI Emoji", "Apple Color Emoji", "Noto Color Emoji", "Segoe UI Symbol", "Android Emoji", sans-serif;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100 py-4">
            <div class="col-md-6">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <a href="/" class="text-decoration-none">
                                <i class="fas fa-truck fa-3x text-primary"></i>
                                <h3 class="fw-bold mt-2">Create New Account</h3>
                            </a>
                            <p class="text-muted">Enter your details to join the platform</p>
                        </div>

                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="name" class="form-label fw-bold">Full Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        <input type="text" name="name" class="form-control" id="name" value="{{ old('name') }}" required autofocus>
                                    </div>
                                    @if($errors->has('name'))
                                        <small class="text-danger">{{ $errors->first('name') }}</small>
                                    @endif
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label fw-bold">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" name="email" class="form-control" id="email" value="{{ old('email') }}" placeholder="name@example.com" required>
                                </div>
                                @if($errors->has('email'))
                                    <small class="text-danger">{{ $errors->first('email') }}</small>
                                @endif
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Account Type</label>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="role" id="roleCustomer" value="customer" {{ old('role', 'customer') == 'customer' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="roleCustomer">Customer</label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="role" id="roleSupplier" value="supplier" {{ old('role') == 'supplier' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="roleSupplier">Supplier</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3" id="nationality_field" style="display: none;">
                                <label for="nationality" class="form-label fw-bold">Nationality</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-globe"></i></span>
                                    <select name="nationality" class="form-select" id="nationality">
                                        <option value="">Select Nationality</option>
                                        <option value="Saudi Arabia">&#127480;&#127462; Saudi Arabia</option>
                                        <option value="Egypt">&#127466;&#127468; Egypt</option>
                                        <option value="United Arab Emirates">&#127462;&#127466; United Arab Emirates</option>
                                        <option value="Jordan">&#127471;&#127476; Jordan</option>
                                        <option value="Kuwait">&#127472;&#127484; Kuwait</option>
                                        <option value="Morocco">&#127474;&#127462; Morocco</option>
                                        <option value="Algeria">&#127465;&#127487; Algeria</option>
                                        <option value="Tunisia">&#127481;&#127475; Tunisia</option>
                                        <option value="China">&#127464;&#127475; China</option>
                                        <option value="Japan">&#127471;&#127477; Japan</option>
                                        <option value="South Korea">&#127472;&#127479; South Korea</option>
                                        <option value="India">&#127470;&#127475; India</option>
                                        <option value="Indonesia">&#127470;&#127465; Indonesia</option>
                                        <option value="Malaysia">&#127474;&#127486; Malaysia</option>
                                        <option value="United Kingdom">&#127468;&#127463; United Kingdom</option>
                                        <option value="Germany">&#127465;&#127466; Germany</option>
                                        <option value="France">&#127467;&#127479; France</option>
                                        <option value="Italy">&#127470;&#127481; Italy</option>
                                        <option value="Spain">&#127466;&#127480; Spain</option>
                                        <option value="Turkey">&#127481;&#127479; Turkey</option>
                                        <option value="Switzerland">&#127464;&#127470; Switzerland</option>
                                        <option value="United States">&#127482;&#127480; United States</option>
                                        <option value="Canada">&#127464;&#127462; Canada</option>
                                        <option value="Brazil">&#127463;&#127479; Brazil</option>
                                        <option value="Mexico">&#127474;&#127480; Mexico</option>
                                        <option value="Argentina">&#127462;&#127479; Argentina</option>
                                        <option value="South Africa">&#127487;&#127462; South Africa</option>
                                        <option value="Nigeria">&#127475;&#127468; Nigeria</option>
                                        <option value="Kenya">&#127472;&#127466; Kenya</option>
                                        <option value="Australia">&#127462;&#127482; Australia</option>
                                        <option value="Other">🏳️ Other</option>
                                    </select>
                                </div>
                                @if($errors->has('nationality'))
                                    <small class="text-danger">{{ $errors->first('nationality') }}</small>
                                @endif
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label fw-bold">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" name="password" class="form-control" id="password" required>
                                </div>
                                @if($errors->has('password'))
                                    <small class="text-danger">{{ $errors->first('password') }}</small>
                                @endif
                            </div>

                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label fw-bold">Confirm Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" required>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2 mb-3">
                                <i class="fas fa-user-plus me-2"></i>Create Account
                            </button>

                            <div class="text-center">
                                <p class="mb-0">Already have an account? <a href="{{ route('login') }}" class="fw-bold text-decoration-none">Login</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function toggleNationality() {
            const nationalityField = document.getElementById('nationality_field');
            const selectedRole = document.querySelector('input[name="role"]:checked');
            
            if (selectedRole && selectedRole.value === 'supplier') {
                nationalityField.style.display = 'block';
            } else {
                nationalityField.style.display = 'none';
                const selectElement = document.getElementById('nationality');
                if(selectElement) selectElement.value = ""; 
            }
        }

        document.querySelectorAll('input[name="role"]').forEach((elem) => {
            elem.addEventListener("change", toggleNationality);
        });

        window.addEventListener('load', toggleNationality);
    </script>
</body>
</html>