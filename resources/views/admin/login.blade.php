<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập Trang Quản Trị</title>
    <style>
        /* CSS đơn giản để trang giống thiết kế */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .login-container h1 {
            margin-top: 0;
            margin-bottom: 24px;
            font-size: 24px;
            color: #333;
        }
        .error-message {
            color: red;
            margin-bottom: 15px;
            background-color: #ffebee;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ef9a9a;
        }
        .input-group {
            margin-bottom: 20px;
            text-align: left;
        }
        .input-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }
        .input-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box; /* Quan trọng */
        }
        .login-button {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            background-color: #FF9900; /* Màu cam quen thuộc của bạn */
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }
        .login-button:hover {
            background-color: #e68a00;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <h1>Đăng Nhập Trang Quản Trị</h1>

        {{-- Phần hiển thị lỗi khi đăng nhập sai --}}
        @if ($errors->any())
            <div class="error-message">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('admin.login') }}" method="POST">
            {{-- @csrf là bắt buộc cho mọi form trong Laravel để bảo mật --}}
            @csrf

            <div class="input-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required>
            </div>

            <div class="input-group">
                <label for="password">Mật khẩu</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="login-button">Đăng Nhập</button>
        </form>
    </div>

</body>
</html>
