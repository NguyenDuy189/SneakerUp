@extends('admin.layout')

@section('content')
<div class="container">
    <h3>{{ isset($discount) ? 'Sửa mã giảm giá' : 'Thêm mã giảm giá' }}</h3>

    <form action="{{ isset($discount) ? route('admin.discounts.update', $discount->id) : route('admin.discounts.store') }}" method="POST">
        @csrf
        @if(isset($discount)) @method('PUT') @endif

        <div class="mb-3">
            <label>Mã giảm giá</label>
            <input type="text" name="code" class="form-control" value="{{ old('code', $discount->code ?? '') }}">
        </div>

        <div class="mb-3">
            <label>Loại giảm giá</label>
            <select name="type" class="form-select">
                <option value="percent" {{ (isset($discount) && $discount->type == 'percent') ? 'selected' : '' }}>Phần trăm (%)</option>
                <option value="fixed" {{ (isset($discount) && $discount->type == 'fixed') ? 'selected' : '' }}>Cố định (VNĐ)</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Giá trị giảm</label>
            <input type="number" name="value" class="form-control" value="{{ old('value', $discount->value ?? '') }}">
        </div>

        <div class="mb-3">
            <label>Giới hạn lượt dùng</label>
            <input type="number" name="usage_limit" class="form-control" value="{{ old('usage_limit', $discount->usage_limit ?? 0) }}">
        </div>

        <div class="mb-3">
            <label>Ngày hết hạn</label>
            <input type="date" name="expiry_date" class="form-control" value="{{ old('expiry_date', $discount->expiry_date ?? '') }}">
        </div>

        <div class="mb-3">
            <label>Giá trị đơn hàng tối thiểu</label>
            <input type="number" name="min_order_value" class="form-control" value="{{ old('min_order_value', $discount->min_order_value ?? '') }}">
        </div>

        <button type="submit" class="btn btn-primary">Lưu</button>
        <a href="{{ route('admin.discounts.index') }}" class="btn btn-secondary">Quay lại</a>
    </form>
</div>
@endsection
