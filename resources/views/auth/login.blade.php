<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- تم تعديل العنوان هنا --}}
    <title>Login | SCA </title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-5">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <a href="/" class="text-decoration-none">
                                <i class="fas fa-truck fa-3x text-primary"></i>
                                <h3 class="fw-bold mt-2">SCA</h3>
                            </a>
                            <p class="text-muted">Welcome back! Please enter your details</p>
                        </div>

                        <form method="POST" action="{{ route('login') }}">
                            @csrf 
                            <div class="mb-3">
                                <label for="email" class="form-label fw-bold">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" name="email" value="{{ old('email') }}"
                                        class="form-control" id="email" placeholder="name@example.com" required
                                        autofocus>
                                </div>
                                @error('email')
                                    <small class="text-danger mt-1 d-block">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label fw-bold">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" name="password" class="form-control" id="password"
                                        placeholder="********" required autocomplete="current-password">
                                </div>
                                @error('password')
                                    <small class="text-danger mt-1 d-block">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" name="remember" class="form-check-input" id="remember">
                                <label class="form-check-label" for="remember">Remember me</label>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2 mb-3">
                                <i class="fas fa-sign-in-alt me-2"></i>Login
                            </button>

                            <div class="text-center">
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="text-decoration-none">Forgot
                                        password?</a>
                                @endif
                            </div>
                        </form>

                        <div class="position-relative my-4">
                            <hr>
                            <span
                                class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-muted">OR</span>
                        </div>

                        <div class="text-center mt-4">
                            <p class="mb-0">Don't have an account? <a href="{{ route('register') }}"
                                    class="fw-bold text-decoration-none">Sign up</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>