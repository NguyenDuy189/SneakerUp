{{-- File: resources/views/admin/staff/create.blade.php --}}

@extends('layouts.admin')
@section('title', 'Thêm Nhân Viên Mới')

@section('content')
<div class="form-container">
    <form action="{{ route('admin.staff.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="fullname">Họ và Tên</label>
            <input type="text" id="fullname" name="fullname" value="{{ old('fullname') }}" required>
            @error('fullname') <div class="alert alert-danger">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required>
            @error('email') <div class="alert alert-danger">{{ $message }}</div> @enderror
        </div>

        {{-- THÊM CÁC TRƯỜNG CÒN THIẾU VÀO ĐÂY --}}
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" value="{{ old('username') }}" required>
            @error('username') <div class="alert alert-danger">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="password">Mật khẩu</label>
            <input type="password" id="password" name="password" required>
            @error('password') <div class="alert alert-danger">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="phone">Số điện thoại</label>
            <input type="text" id="phone" name="phone" value="{{ old('phone') }}">
             @error('phone') <div class="alert alert-danger">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="role">Vai trò</label>
            <select id="role" name="role" required>
                <option value="">-- Chọn vai trò --</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected'
 : '' }}>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>
            @error('role') <div class="alert alert-danger">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="status">Trạng Thái</label>
            <select id="status" name="status">
                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Hoạt động</option>
                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Không hoạt động</option>
            </select>
            @error('status') <div class="alert alert-danger">{{ $message }}</div> @enderror
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Lưu</button>
            <a href="{{ route('admin.staff.index') }}" class="btn btn-secondary">Hủy</a>
        </div>
    </form>
</div>
@endsection
