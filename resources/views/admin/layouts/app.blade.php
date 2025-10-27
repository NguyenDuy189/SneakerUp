<!DOCTYPE html>
<html lang="vi" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SneakerUp Admin')</title>

    {{-- Bootstrap & FontAwesome --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">

    {{-- Google Font --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    {{-- Custom Style --}}
    <style>
        :root {
            --sidebar-bg: #1f2937;
            --sidebar-active: #2563eb;
            --sidebar-hover: #374151;
            --sidebar-text: #cbd5e1;
            --header-bg: #fff;
            --body-bg: #f5f7fa;
            --text-color: #333;
        }
        [data-theme="dark"] {
            --sidebar-bg: #111827;
            --sidebar-active: #3b82f6;
            --sidebar-hover: #1f2937;
            --sidebar-text: #9ca3af;
            --header-bg: #1f2937;
            --body-bg: #111827;
            --text-color: #f3f4f6;
        }
        body {
            font-family: "Inter", sans-serif;
            background-color: var(--body-bg);
            color: var(--text-color);
            transition: all 0.3s ease;
        }
        /* Sidebar */
        .sidebar {
            width: 260px;
            background: var(--sidebar-bg);
            color: var(--sidebar-text);
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }
        .sidebar .logo {
            font-weight: 700;
            font-size: 22px;
            text-align: center;
            padding: 22px 0;
            background: var(--sidebar-hover);
            color: #fff;
            letter-spacing: 0.5px;
        }
        .sidebar a {
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--sidebar-text);
            padding: 12px 22px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.25s ease;
            border-left: 3px solid transparent;
        }
        .sidebar a:hover {
            background-color: var(--sidebar-hover);
            color: #fff;
        }
        .sidebar a.active {
            background-color: var(--sidebar-active);
            color: #fff;
            border-left-color: #93c5fd;
        }
        .sidebar a i {
            width: 20px;
            text-align: center;
            transition: transform 0.2s ease;
        }
        .sidebar a:hover i {
            transform: scale(1.15);
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
            background-color: var(--header-bg);
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
            border: 2px solid var(--sidebar-active);
        }
        .content-wrapper {
            flex: 1;
            padding: 30px;
        }

        footer {
            background: var(--header-bg);
            text-align: center;
            padding: 15px;
            font-size: 13px;
            border-top: 1px solid #e5e7eb;
            color: #9ca3af;
        }

        /* Dark mode toggle */
        .toggle-switch {
            position: absolute;
            bottom: 25px;
            left: 25px;
            background: var(--sidebar-hover);
            padding: 8px 15px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            color: #e5e7eb;
            transition: 0.3s;
        }
        .toggle-switch:hover {
            background: var(--sidebar-active);
            color: #fff;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .sidebar { width: 220px; }
            .main-content { margin-left: 220px; }
        }
        @media (max-width: 768px) {
            .sidebar { left: -260px; position: absolute; }
            .main-content { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>

    {{-- Sidebar --}}
    <div class="sidebar" id="sidebar">
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

        <div class="toggle-switch" id="themeToggle">
            <i class="fa-solid fa-moon"></i> <span>Dark Mode</span>
        </div>
    </div>

    {{-- Main --}}
    <div class="main-content">
        <header>
            <div class="fw-bold">Trang quản trị SneakerUp</div>
            <div class="user-info dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                    <img src="https://i.pravatar.cc/50?u={{ Auth::id() }}" alt="Admin Avatar">
                    <div class="ms-2">
                        <strong>{{ Auth::user()->fullname ?? 'Admin' }}</strong><br>
                        <small class="text-muted">{{ Auth::user()->role ?? 'Quản trị viên' }}</small>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                    <li><a class="dropdown-item" href="#"><i class="fa-solid fa-user me-2"></i>Hồ sơ</a></li>
                    <li><a class="dropdown-item text-danger" href="{{ route('logout') }}"><i class="fa-solid fa-right-from-bracket me-2"></i>Đăng xuất</a></li>
                </ul>
            </div>
        </header>

        <main class="content-wrapper">
            {{-- Flash messages --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-3">
                    <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @elseif (session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-3">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </main>

        <footer>
            © {{ date('Y') }} SneakerUp Admin Dashboard — All Rights Reserved
        </footer>
    </div>

    {{-- JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Dark Mode Toggle Script --}}
    <script>
        const html = document.documentElement;
        const themeToggle = document.getElementById('themeToggle');
        const icon = themeToggle.querySelector('i');
        const text = themeToggle.querySelector('span');
        const currentTheme = localStorage.getItem('theme') || 'light';

        function setTheme(theme) {
            html.setAttribute('data-theme', theme);
            localStorage.setItem('theme', theme);
            if (theme === 'dark') {
                icon.classList.replace('fa-moon', 'fa-sun');
                text.textContent = 'Light Mode';
            } else {
                icon.classList.replace('fa-sun', 'fa-moon');
                text.textContent = 'Dark Mode';
            }
        }

        setTheme(currentTheme);
        themeToggle.addEventListener('click', () => {
            const newTheme = html.getAttribute('data-theme') === 'light' ? 'dark' : 'light';
            setTheme(newTheme);
        });
    </script>

    @stack('scripts')
</body>
</html>
