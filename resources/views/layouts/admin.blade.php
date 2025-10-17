<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Trang Quản Trị') - SneakerUp</title>
    <link rel="stylesheet" href="{{ asset('css/admin_style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
</head>
<body>
    <div class="admin-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <span class="logo">SneakerUp</span>
                <h2>Admin</h2>
            </div>

            <nav class="sidebar-nav">
                <a href="{{ route('admin.dashboard') }}" class="nav-item {{ Request::is('admin/dashboard*') ? 'active' : '' }}">📊 Báo Cáo Và Thống Kê</a>
                <a href="#" class="nav-item">📚 Quản Lý Danh Mục</a>
                <a href="#" class="nav-item">👟 Quản Lý Sản Phẩm</a>
                <a href="#" class="nav-item">📦 Quản Lý Đơn Hàng</a>
                <a href="#" class="nav-item">🎫 Quản Lý Voucher</a>

                @if(auth()->user()->can('user-list') || auth()->user()->can('role-list'))
                <div class="nav-group {{ (Request::is('admin/users*') || Request::is('admin/customers*') || Request::is('admin/staff*') || Request::is('admin/roles*')) ? 'open active' : '' }}">
                    <button class="nav-group-title-button">
                        <span>👥 Quản lý người dùng</span>
                        <i class="fas fa-chevron-down arrow-icon"></i>
                    </button>
                    <div class="nav-submenu">
                        @can('user-list')
                            {{-- SỬA LỖI LOGIC NẰM Ở ĐÂY: Xóa bỏ điều kiện thừa --}}
                            <a href="{{ route('admin.users.index') }}" class="nav-item nav-submenu-item {{ Request::is('admin/users*') ? 'active' : '' }}">Tất cả tài khoản</a>
                            <a href="{{ route('admin.staff.index') }}" class="nav-item nav-submenu-item {{ Request::is('admin/staff*') ? 'active' : '' }}">Nhân viên</a>
                            <a href="{{ route('admin.customers.index') }}" class="nav-item nav-submenu-item {{ Request::is('admin/customers*') ? 'active' : '' }}">Khách hàng</a>
                        @endcan
                        @can('role-list')
                            <a href="{{ route('admin.roles.index') }}" class="nav-item nav-submenu-item {{ Request::is('admin/roles*') ? 'active' : '' }}">Vai trò & Quyền hạn</a>
                        @endcan
                    </div>
                </div>
                @endif

                <a href="#" class="nav-item">📞 Quản Lý Liên Hệ</a>
            </nav>
        </aside>

        <main class="main-content">
            <header class="header">
                <div class="header-left">
                    <h3>@yield('title', 'Dashboard')</h3>
                </div>
                <div class="header-right">
                    <a href="{{ url('/') }}" target="_blank">Trang Chủ</a>
                    <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <a href="{{ route('admin.logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            Đăng Xuất
                        </a>
                    </form>
                </div>
            </header>
            <section class="content">
                <div class="messages-container">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                </div>
                @yield('content')
            </section>
            <footer class="footer">
                SneakerUp &copy; {{ date('Y') }}
            </footer>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var groupTitles = document.querySelectorAll('.nav-group-title-button');
            groupTitles.forEach(function (title) {
                title.addEventListener('click', function () {
                    var parentGroup = this.parentElement;
                    parentGroup.classList.toggle('open');
                });
            });
            if (document.getElementById('checkAll')) {
                const checkAll = document.getElementById('checkAll');
                const groupCheckboxes = document.querySelectorAll('.group-checkbox');
                const permissionCheckboxes = document.querySelectorAll('.permission-checkbox');
                checkAll.addEventListener('click', function() {
                    permissionCheckboxes.forEach(checkbox => { checkbox.checked = this.checked; });
                    groupCheckboxes.forEach(checkbox => { checkbox.checked = this.checked; });
                });
                groupCheckboxes.forEach(groupCheckbox => {
                    groupCheckbox.addEventListener('click', function() {
                        const group = this.dataset.group;
                        document.querySelectorAll(`.group-${group}`).forEach(checkbox => { checkbox.checked = this.checked; });
                    });
                });
            }
        });
    </script>
</body>
</html>
