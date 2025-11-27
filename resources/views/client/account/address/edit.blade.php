@extends('layouts.account')

@section('title', 'Sửa Địa Chỉ')

@section('account_content')
<div class="container">
    <h2>Sửa Địa Chỉ</h2>

    @if ($errors->any())
        <div class="alert alert-danger" style="padding: 15px; background-color: #f8d7da; border: 1px solid #f5c6cb; border-radius: 5px; color: #721c24; margin-bottom: 20px;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('client.address.update', $address->id) }}" method="POST" style="max-width: 600px;">
        @csrf
        @method('PUT') <div class="form-group" style="margin-bottom: 20px;">
            <label for="fullname" style="display: block; font-weight: bold; margin-bottom: 5px;">Họ và Tên</label>
            <input type="text" id="fullname" name="fullname" value="{{ old('fullname', $address->fullname) }}" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
        </div>

        <div class="form-group" style="margin-bottom: 20px;">
            <label for="phone" style="display: block; font-weight: bold; margin-bottom: 5px;">Số điện thoại</label>
            <input type="text" id="phone" name="phone" value="{{ old('phone', $address->phone) }}" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
        </div>

        <div class="form-group" style="margin-bottom: 20px;">
            <label for="address" style="display: block; font-weight: bold; margin-bottom: 5px;">Địa chỉ chi tiết</label>
            <input type="text" id="address" name="address" value="{{ old('address', $address->address) }}" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
        </div>

        <div class="form-group" style="margin-bottom: 20px;">
            <label for="is_default" style="display: inline-flex; align-items: center; gap: 10px; font-weight: normal;">
                <input type="checkbox" id="is_default" name="is_default" value="1" {{ old('is_default', $address->is_default) ? 'checked' : '' }} style="width: 18px; height: 18px;">
                <span>Đặt làm địa chỉ mặc định</span>
            </label>
        </div>

        <div class="form-actions" style="display: flex; gap: 10px;">
            <button type="submit" class="btn btn-primary" style="background-color: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">Cập Nhật</button>
            <a href="{{ route('client.address.index') }}" class="btn btn-secondary" style="background-color: #6c757d; color: white; padding: 10px 20px; border: none; border-radius: 5px; text-decoration: none;">Hủy</a>
        </div>
    </form>
</div>
@endsection
