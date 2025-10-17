<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang quản lý - SneakerUp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial; background: #f8f9fa; }
        .sidebar {
            width: 220px;
            background: #343a40;
            color: #fff;
            height: 100vh;
            position: fixed;
            top: 0; left: 0;
            padding-top: 60px;
        }
        .sidebar a {
            color: #ddd; display: block;
            padding: 10px 20px;
            text-decoration: none;
        }
        .sidebar a.active, .sidebar a:hover {
            background: #ff6600; color: #fff;
        }
        .content-wrapper {
            margin-left: 240px; /* rộng hơn một chút so với sidebar */
            padding: 30px 40px;
            background: #fff;
            min-height: 100vh;
            padding-top: 80px;
}
        header {
            background: #ff6600; color: #fff;
            height: 60px; line-height: 60px;
            padding: 0 20px;
            position: fixed; width: 100%;
            top: 0; left: 0;
            display: flex; justify-content: space-between;
        }
    </style>
</head>
<body>
<header>
    <div>SneakerUp Admin</div>
    <a href="#" class="text-white">Đăng xuất</a>
</header>

<div class="sidebar">
    <a href="#" class="active">Quản lý sản phẩm</a>
    <a href="#">Quản lý đơn hàng</a>
    <a href="#">Quản lý danh mục</a>
    <a href="#">Báo cáo & Thống kê</a>
</div>

<main>
    @yield('content')
</main>

</body>
</html>