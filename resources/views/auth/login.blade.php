<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập Admin | SneakerUp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            height: 100vh;
            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.25);
            padding: 40px 35px;
            width: 380px;
            animation: fadeIn 0.6s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .logo {
            display: block;
            width: 120px;
            margin: 0 auto 15px auto;
        }
        .form-title {
            text-align: center;
            font-weight: 700;
            color: #203a43;
            margin-bottom: 20px;
        }
        .btn-login {
            background: linear-gradient(90deg, #203a43, #2c5364);
            border: none;
            color: #fff;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.2s ease;
        }
        .btn-login:hover {
            transform: scale(1.03);
            background: linear-gradient(90deg, #2c5364, #203a43);
        }
        .form-check-label {
            color: #555;
        }
        .error-box {
            background: #ffe5e5;
            color: #b30000;
            border-left: 5px solid #b30000;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

    <div class="login-card">
        {{-- LOGO --}}
        <img src="{{ asset('images/logo.jpg') }}" alt="SneakerUp Logo" class="logo">

        <h3 class="form-title">Đăng nhập Quản trị</h3>

        {{-- Hiển thị lỗi --}}
        @if ($errors->any())
            <div class="error-box">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Hiển thị thông báo --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Form đăng nhập --}}
        <form method="POST" action="{{ route('login.post') }}">
            @csrf
            <div class="mb-3">
                <label for="username" class="form-label fw-semibold">Tên đăng nhập</label>
                <input type="text" name="username" id="username"
                    value="{{ old('username') }}"
                    class="form-control form-control-lg rounded-3"
                    placeholder="Nhập tên đăng nhập"
                    required autofocus>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label fw-semibold">Mật khẩu</label>
                <input type="password" name="password" id="password"
                    class="form-control form-control-lg rounded-3"
                    placeholder="••••••••" required>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label" for="remember">Ghi nhớ đăng nhập</label>
                </div>
                {{-- <a href="#" class="small text-decoration-none">Quên mật khẩu?</a> --}}
            </div>

            <button class="btn btn-login w-100 py-2">Đăng nhập</button>
        </form>

        <p class="text-center text-muted mt-4 mb-0" style="font-size: 13px;">
            © {{ date('Y') }} SneakerUp — Trang quản trị hệ thống
        </p>
    </div>

</body>
</html>
