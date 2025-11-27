{{-- Giả sử bạn có layout chung là 'layouts.app' hoặc 'layouts.client' --}}
@extends('layouts.account')

@section('title', 'Thông Tin Tài Khoản')

@section('account_content')
<div class="container">
    <h2>Thông Tin Tài Khoản</h2>
    <p>Quản lý thông tin cá nhân của bạn.</p>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('client.profile.update') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="fullname">Họ và Tên</label>
            <input type="text" id="fullname" name="fullname" value="{{ old('fullname', $user->fullname) }}" class="form-control">
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" class="form-control">
        </div>

        <div class="form-group">
            <label for="phone">Số điện thoại</label>
            <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Lưu Thay Đổi</button>
    </form>
</div>
@endsection
