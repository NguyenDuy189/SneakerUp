<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'SneakerUp')</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />

    <link rel="stylesheet" href="{{ asset('css/client_style.css?v=2') }}">
</head>

<body>
    <header class="client-header">
        <div class="container">
            <nav class="client-header-nav">
                <a href="{{ url('/') }}" class="logo">SneakerUp</a>

                <ul class="main-nav">
                    <li><a href="{{ url('/') }}">Trang Chủ</a></li>
                    <li><a href="#">Sản Phẩm</a></li>
                    <li><a href="#">Khuyến Mãi</a></li>
                    <li><a href="#">Tin Tức</a></li>
                    <li><a href="#">Liên Hệ</a></li>
                </ul>

                <div class="user-actions">
                    <form action="#" class="search-bar">
                        <input type="text" placeholder="Tìm kiếm...">
                        <button type="submit"><i class="fa fa-search"></i></button>
                    </form>
                    <a href="#" class="icon-btn" aria-label="Giỏ hàng">
                        <i class="fa fa-shopping-cart"></i>
                    </a>

                    @guest
                        <a href="{{ route('login') }}" class="login-btn">Đăng Nhập</a>
                    @else
        <a href="{{ route('profile.edit') }}" class="icon-btn" aria-label="Tài khoản của tôi">
            <i class="fa fa-user"></i>
        </a>
    @endguest
                </div>
            </nav>
        </div>
    </header>

    <main class="client-main-content">
        <div class="container">
            @yield('content')
        </div>
    </main>

    <footer class="client-footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-column">
                    <h4>ĐĂNG KÝ NGAY</h4>
                    <p>Nhận thông tin và các chương trình khuyến mãi sớm nhất.</p>
                    <form action="#" class="newsletter-form">
                        <input type="email" placeholder="Nhập email của bạn...">
                        <button type="submit">GỬI</button>
                    </form>
                </div>

                <div class="footer-column">
                    <h4>THÔNG TIN</h4>
                    <ul>
                        <li><a href="#">Về chúng tôi</a></li>
                        <li><a href="#">Chính sách bảo mật</a></li>
                        <li><a href="#">Điều khoản sử dụng</a></li>
                    </ul>
                </div>

                <div class="footer-column">
                    <h4>HỖ TRỢ</h4>
                    <ul>
                        <li><a href="#">Vận chuyển & Giao hàng</a></li>
                        <li><a href="#">Chính sách đổi trả</a></li>
                        <li><a href="#">Câu hỏi thường gặp (FAQ)</a></li>
                    </ul>
                </div>

                <div class="footer-column">
                    <h4>KẾT NỐI VỚI CHÚNG TÔI</h4>
                    <div class="social-icons">
                        <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" aria-label="Tiktok"><i class="fab fa-tiktok"></i></a>
                    </div>
                    <h4 style="margin-top: 20px;">PHƯƠNG THỨC THANH TOÁN</h4>
                    </div>
            </div>
            <div class="footer-copyright">
                <p>&copy; {{ date('Y') }} SneakerUp. Tất cả bản quyền được bảo lưu.</p>
            </div>
        </div>
    </footer>
</body>
</html>
