@extends('layouts.admin')
@section('title', 'Sửa Thông Tin Khách Hàng')
@section('content')
<div class="form-container">
    <form action="{{ route('admin.customers.update', $customer->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>Họ và Tên</label>
            <input type="text" name="fullname" value="{{ $customer->fullname }}" required>
        </div>
        <div class="form-group">
            <label>Email (Không thể thay đổi)</label>
            <input type="email" value="{{ $customer->email }}" disabled>
        </div>
        <div class="form-group">
            <label>Số điện thoại</label>
            <input type="text" name="phone" value="{{ $customer->phone }}">
        </div>
        <div class="form-group">
            <label for="status">Trạng Thái (Khóa/Mở)</label>
            <select id="status" name="status">
                <option value="active" @if($customer->status == 'active') selected @endif>Hoạt động</option>
                <option value="inactive" @if($customer->status == 'inactive') selected @endif>Bị khóa (Inactive)</option>
            </select>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Cập nhật</button>
            <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">Hủy</a>
        </div>
    </form>
</div>
@endsection
