<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | SneakerUp</title>

    {{-- Bootstrap & FontAwesome --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    {{-- Google Font --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    {{-- Custom CSS --}}
    <style>
        body {
            font-family: "Inter", sans-serif;
            background-color: #f5f7fa;
            color: #333;
        }

        /* Sidebar */
        .sidebar {
            width: 260px;
            background: #1f2937;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            color: #fff;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }
        .sidebar .logo {
            font-weight: 700;
            font-size: 22px;
            text-align: center;
            padding: 22px 0;
            background: #111827;
            letter-spacing: 1px;
        }
        .sidebar a {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #cbd5e1;
            padding: 12px 20px;
            text-decoration: none;
            font-weight: 500;
            transition: 0.25s;
            border-left: 3px solid transparent;
        }
        .sidebar a:hover {
            background-color: #374151;
            color: #fff;
        }
        .sidebar a.active {
            background-color: #2563eb;
            color: #fff;
            border-left-color: #93c5fd;
        }
        .sidebar a i {
            width: 20px;
            text-align: center;
        }

        /* Main layout */
        .main-content {
            margin-left: 260px;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            transition: all 0.3s;
        }

        header {
            background-color: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            padding: 15px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .user-info img {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #2563eb;
        }

        .content-wrapper {
            flex: 1;
            padding: 30px;
        }

        footer {
            background: #fff;
            text-align: center;
            padding: 15px;
            font-size: 13px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
        }

        /* Toast container */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1055;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .sidebar { width: 220px; }
            .main-content { margin-left: 220px; }
        }

        @media (max-width: 768px) {
            .sidebar { position: absolute; left: -260px; }
            .main-content { margin-left: 0; }
        }
    </style>
</head>
<body>

    {{-- Sidebar --}}
    <div class="sidebar">
        <div class="logo">👟 SneakerUp</div>

        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-house"></i> Dashboard
        </a>

        <a href="{{ route('admin.orders.index') }}" class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
            <i class="fa-solid fa-box"></i> Đơn hàng
        </a>

        <a href="{{ route('admin.payments.index') }}" class="{{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
            <i class="fa-solid fa-credit-card"></i> Thanh toán
        </a>

        <a href="#"><i class="fa-solid fa-shoe-prints"></i> Sản phẩm</a>
        <a href="#"><i class="fa-solid fa-tags"></i> Danh mục</a>
        <a href="#"><i class="fa-solid fa-users"></i> Người dùng</a>
        <a href="#"><i class="fa-solid fa-chart-line"></i> Báo cáo</a>
        <a href="{{ route('logout') }}"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a>
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
            {{-- ⚡ Hiển thị thông báo Flash (dưới dạng Alert và Toast) --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('info'))
                <div class="alert alert-info alert-dismissible fade show mb-3" role="alert">
                    <i class="fa-solid fa-circle-info me-2"></i>{{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Nội dung trang con --}}
            @yield('content')
        </main>

        <footer>
            &copy; {{ date('Y') }} SneakerUp Admin Panel — All rights reserved.
        </footer>
    </div>

    {{-- Toast hiển thị ở góc phải (tự ẩn) --}}
    <div class="toast-container">
        @if (session('success'))
            <div class="toast align-items-center text-white bg-success border-0" role="alert" id="toast-success">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        @elseif (session('error'))
            <div class="toast align-items-center text-white bg-danger border-0" role="alert" id="toast-error">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fa-solid fa-triangle-exclamation me-2"></i>{{ session('error') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        @elseif (session('info'))
            <div class="toast align-items-center text-white bg-info border-0" role="alert" id="toast-info">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fa-solid fa-circle-info me-2"></i>{{ session('info') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        @endif
    </div>

    {{-- JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Kích hoạt toast tự động --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toastElList = [].slice.call(document.querySelectorAll('.toast'))
            toastElList.map(function (toastEl) {
                const toast = new bootstrap.Toast(toastEl, { delay: 4000 })
                toast.show()
            })
        });
    </script>
</body>
</html>
