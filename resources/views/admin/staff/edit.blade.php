{{-- File: resources/views/admin/staff/edit.blade.php --}}

@extends('layouts.admin')
@section('title', 'Sửa Thông Tin Nhân Viên')

@section('content')
<div class="form-container">
    {{-- Form sẽ gửi dữ liệu đến StaffController để xử lý --}}
    <form action="{{ route('admin.staff.update', $staff->id) }}" method="POST">
        @csrf
        @method('PUT') {{-- Bắt buộc cho form update --}}

        <div class="form-group">
            <label for="fullname">Họ và Tên</label>
            <input type="text" id="fullname" name="fullname" value="{{ old('fullname', $staff->fullname) }}" required>
            @error('fullname') <div class="alert alert-danger">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email', $staff->email) }}" required>
            @error('email') <div class="alert alert-danger">{{ $message }}</div> @enderror
        </div>

        {{-- === THÊM CÁC TRƯỜNG CÒN THIẾU VÀO ĐÂY === --}}

        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" value="{{ old('username', $staff->username) }}" required>
            @error('username') <div class="alert alert-danger">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="password">Mật khẩu mới (Bỏ trống nếu không muốn thay đổi)</label>
            <input type="password" id="password" name="password">
            @error('password') <div class="alert alert-danger">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="phone">Số điện thoại</label>
            <input type="text" id="phone" name="phone" value="{{ old('phone', $staff->phone) }}">
             @error('phone') <div class="alert alert-danger">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="role">Vai trò</label>
            <select id="role" name="role" required>
                @foreach ($roles as $role)
                    {{-- Dùng $userRoles->contains() để kiểm tra và chọn sẵn vai trò cũ --}}
                    <option value="{{ $role->name }}" {{ $userRoles->contains($role->name) ? 'selected' : '' }}>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>
            @error('role') <div class="alert alert-danger">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="status">Trạng Thái</label>
            <select id="status" name="status">
                <option value="active" {{ old('status', $staff->status) == 'active' ? 'selected' : '' }}>Hoạt động</option>
                <option value="inactive" {{ old('status', $staff->status) == 'inactive' ? 'selected' : '' }}>Không hoạt động</option>
            </select>
            @error('status') <div class="alert alert-danger">{{ $message }}</div> @enderror
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Cập nhật</button>
            <a href="{{ route('admin.staff.index') }}" class="btn btn-secondary">Hủy</a>
        </div>
    </form>
</div>
@endsection
