@extends('layouts.client')

@section('title', 'Đăng Nhập')

@section('content')
<div class="auth-container">

    <h2 class="auth-title">Đăng Nhập</h2>

    @if ($errors->any())
        <div class="alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
        </div>

        <div class="form-group">
            <label for="password">Mật khẩu</label>
            <input id="password" type="password" name="password" required>
        </div>

        <div class="form-group-flex">
            <label for="remember_me" class="remember-me">
                <input id="remember_me" type="checkbox" name="remember">
                <span>Ghi nhớ tôi</span>
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="forgot-password">
                    Quên mật khẩu?
                </a>
            @endif
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary" style="width: 100%;">
                Đăng Nhập
            </button>
        </div>

        <div class="auth-switch">
            Chưa có tài khoản? <a href="{{ route('register') }}">Đăng ký ngay</a>
        </div>
    </form>
</div>
@endsection
