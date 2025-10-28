@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <h3>Thêm biến thể cho: {{ $product->name }}</h3>
    <form action="{{ route('variants.store', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label class="form-label">Size</label>
            <input type="text" name="size" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Màu sắc</label>
            <input type="text" name="color" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Số lượng</label>
            <input type="number" name="stock" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Giá</label>
            <input type="number" name="price" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Ảnh</label>
            <input type="file" name="image" class="form-control">
        </div>
        <button class="btn btn-primary">Lưu</button>
        <a href="{{ route('variants.index', $product->id) }}" class="btn btn-secondary">Hủy</a>
    </form>
</div>
@endsection
