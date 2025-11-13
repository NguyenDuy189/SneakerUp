<!DOCTYPE html>
<html lang="vi" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SneakerUp Admin')</title>

    {{-- Bootstrap & FontAwesome --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">

    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    {{-- Custom CSS --}}
    <style>
        :root {
            --primary: #ff6b00;
            --primary-light: #ffa552;
            --sidebar-bg: #1f2937;
            --sidebar-hover: #374151;
            --sidebar-active: #ff6b00;
            --sidebar-text: #cbd5e1;
            --bg-light: #f8fafc;
            --text-color: #1e293b;
            --transition: all 0.3s ease;
        }

        [data-theme="dark"] {
            --bg-light: #0f172a;
            --sidebar-bg: #111827;
            --sidebar-hover: #1e293b;
            --sidebar-text: #9ca3af;
            --text-color: #e2e8f0;
        }

        body {
            font-family: "Inter", sans-serif;
            background-color: var(--bg-light);
            color: var(--text-color);
            transition: var(--transition);
        }

        /* SIDEBAR */
        .sidebar {
            width: 250px;
            background: var(--sidebar-bg);
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            color: var(--sidebar-text);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: var(--transition);
            box-shadow: 3px 0 10px rgba(0, 0, 0, 0.1);
        }

        .sidebar-header {
            background: var(--primary);
            color: white;
            text-align: center;
            font-size: 22px;
            font-weight: 700;
            padding: 18px 0;
            letter-spacing: 1px;
        }

        .sidebar-nav a {
            color: var(--sidebar-text);
            padding: 12px 20px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 500;
            border-left: 4px solid transparent;
            transition: var(--transition);
        }

        .sidebar-nav a:hover {
            background: var(--sidebar-hover);
            color: #fff;
        }

        .sidebar-nav a.active {
            background: rgba(255, 107, 0, 0.15);
            color: #fff;
            border-left: 4px solid var(--primary);
        }

        .sidebar-nav a i {
            font-size: 16px;
            width: 20px;
            text-align: center;
        }

        .sidebar-footer {
            text-align: center;
            padding: 15px 0;
            font-size: 13px;
            background: var(--sidebar-hover);
            color: #9ca3af;
        }

        /* MAIN CONTENT */
        .main-content {
            margin-left: 250px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        header {
            background: white;
            padding: 14px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e5e7eb;
            position: sticky;
            top: 0;
            z-index: 999;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        .content-wrapper {
            flex: 1;
            padding: 25px 35px;
            background: var(--bg-light);
            animation: fadeIn 0.4s ease;
        }

        footer {
            text-align: center;
            padding: 15px;
            background: white;
            font-size: 13px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
        }

        /* BUTTONS */
        .btn-primary {
            background-color: var(--primary);
            border: none;
            transition: var(--transition);
        }

        .btn-primary:hover {
            background-color: var(--primary-light);
        }

        /* FLASH MESSAGES */
        .alert i {
            margin-right: 6px;
        }

        /* ANIMATION */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* DARK MODE SWITCH */
        .theme-toggle {
            padding: 10px;
            margin: 10px;
            border-radius: 20px;
            background: var(--sidebar-hover);
            color: #f3f4f6;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 14px;
            transition: var(--transition);
        }

        .theme-toggle:hover {
            background: var(--primary);
            color: #fff;
        }

        @media (max-width: 992px) {
            .sidebar { left: -250px; }
            .main-content { margin-left: 0; }
        }
    </style>

    @stack('styles')
</head>
<body>

    {{-- SIDEBAR --}}
    <aside class="sidebar">
        <div>
            <div class="sidebar-header">👟 SneakerUp</div>

            <nav class="sidebar-nav">
                <a href="{{ route('admin.dashboard.index') }}" class="{{ request()->routeIs('admin.dashboard.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-gauge"></i> Dashboard
                </a>
                <a href="{{ route('admin.orders.index') }}" class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-box"></i> Đơn hàng
                </a>
                <a href="{{ route('admin.payments.index') }}" class="{{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-credit-card"></i> Thanh toán
                </a>
                <a href="#"><i class="fa-solid fa-shoe-prints"></i> Sản phẩm</a>
                <a href="{{route('admin.categories.index')}}"><i class="fa-solid fa-tags"></i> Danh mục</a>
                <a href="#"><i class="fa-solid fa-users"></i> Người dùng</a>
                <a href="{{ route('admin.reports.index')}}"><i class="fa-solid fa-chart-line"></i> Báo cáo</a>
                <a href="{{ route('logout') }}" class="text-danger">
                    <i class="fa-solid fa-right-from-bracket"></i> Đăng xuất
                </a>
            </nav>
        </div>

        <div class="theme-toggle" id="themeToggle">
            <i class="fa-solid fa-moon"></i> <span>Dark Mode</span>
        </div>

        <div class="sidebar-footer">
            © {{ date('Y') }} SneakerUp<br> All Rights Reserved
        </div>
    </aside>

    {{-- MAIN --}}
    <div class="main-content">
        <header>
            <h5 class="mb-0 fw-bold text-primary">Trang quản trị SneakerUp</h5>
            <div class="d-flex align-items-center gap-3">
                <div class="text-end">
                    <strong>{{ Auth::user()->fullname ?? 'Admin' }}</strong><br>
                    <small class="text-muted">{{ Auth::user()->role ?? 'Quản trị viên' }}</small>
                </div>
                <img src="https://i.pravatar.cc/50?u={{ Auth::id() }}" alt="Avatar" class="rounded-circle border border-2 border-warning" width="45" height="45">
            </div>
        </header>

        <main class="content-wrapper">
            {{-- Thông báo --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @elseif (session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fa-solid fa-triangle-exclamation"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </main>

        <footer>
            SneakerUp Admin Dashboard – Nền tảng quản trị chuyên nghiệp dành cho cửa hàng giày
        </footer>
    </div>

    {{-- SCRIPTS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Dark mode toggle
        const html = document.documentElement;
        const toggle = document.getElementById('themeToggle');
        const icon = toggle.querySelector('i');
        const text = toggle.querySelector('span');
        const current = localStorage.getItem('theme') || 'light';

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

        setTheme(current);
        toggle.addEventListener('click', () => {
            const theme = html.getAttribute('data-theme') === 'light' ? 'dark' : 'light';
            setTheme(theme);
        });
    </script>

    @stack('scripts')
</body>
</html>
