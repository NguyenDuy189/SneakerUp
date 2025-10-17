<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | SneakerUp</title>

    {{-- Bootstrap & FontAwesome --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    {{-- Custom CSS --}}
    <style>
        body {
            font-family: "Poppins", sans-serif;
            background-color: #f4f6f9;
        }
        .sidebar {
            width: 250px;
            background: linear-gradient(180deg, #1e1e2f, #2d2d44);
            position: fixed;
            top: 0; left: 0; bottom: 0;
            color: #fff;
            overflow-y: auto;
        }
        .sidebar a {
            display: block;
            color: #ccc;
            padding: 12px 20px;
            text-decoration: none;
            transition: 0.2s;
        }
        .sidebar a:hover, .sidebar a.active {
            background-color: #007bff;
            color: #fff;
        }
        .sidebar .logo {
            font-weight: 700;
            font-size: 22px;
            text-align: center;
            padding: 18px;
            background: #11111e;
            letter-spacing: 1px;
        }
        .main-content {
            margin-left: 250px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        header {
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 12px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .content-wrapper {
            flex: 1;
            padding: 30px;
        }
        footer {
            background: #f8f9fa;
            text-align: center;
            padding: 10px;
            font-size: 13px;
            color: #777;
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .user-info img {
            width: 35px; height: 35px;
            border-radius: 50%;
        }
    </style>
</head>
<body>

    {{-- Sidebar --}}
    <div class="sidebar">
        <div class="logo">👟 SneakerUp</div>

        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-house me-2"></i> Dashboard
        </a>

        <a href="{{ route('admin.orders.index') }}" class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
            <i class="fa-solid fa-box me-2"></i> Đơn hàng
        </a>

        <a href="#">
            <i class="fa-solid fa-shoe-prints me-2"></i> Sản phẩm
        </a>

        <a href="#">
            <i class="fa-solid fa-tags me-2"></i> Danh mục
        </a>

        <a href="#">
            <i class="fa-solid fa-user me-2"></i> Người dùng
        </a>

        <a href="#">
            <i class="fa-solid fa-chart-line me-2"></i> Báo cáo
        </a>

        <a href="{{ route('logout') }}">
            <i class="fa-solid fa-right-from-bracket me-2"></i> Đăng xuất
        </a>
    </div>

    {{-- Main content --}}
    <div class="main-content">
        <header>
            <div><strong>Trang quản trị SneakerUp</strong></div>

            <div class="user-info">
                <img src="https://i.pravatar.cc/50?u={{ Auth::id() }}" alt="Admin Avatar">
                <div>
                    <strong>{{ Auth::user()->fullname ?? 'Admin SneakerUp' }}</strong><br>
                    <small class="text-muted">{{ Auth::user()->role ?? 'admin' }}</small>
                </div>
            </div>
        </header>

        <main class="content-wrapper">
            {{-- Nội dung trang con --}}
            @yield('content')
        </main>

        <footer>
            &copy; {{ date('Y') }} SneakerUp Admin Panel — All rights reserved.
        </footer>
    </div>

    {{-- JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
