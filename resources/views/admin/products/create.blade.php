@extends('layouts.admin')

@section('content')
<div class="container mt-5">
<form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="mb-3">
    <label for="tags" class="form-label">Tags</label>
    <select name="tags[]" id="tags" class="form-select" multiple>
        @foreach($tags as $tag)
            <option value="{{ $tag->id }}">{{ $tag->name }}</option>
        @endforeach
    </select>
    <small class="text-muted">Giữ Ctrl (Windows) hoặc Command (Mac) để chọn nhiều tag</small>
    </div>
    <label>Tên sản phẩm:</label>
    <input type="text" name="name" class="form-control" required>

    <label>Danh mục:</label>
    <select name="category_id" class="form-control">
        @foreach($categories as $cate)
            <option value="{{ $cate->id }}">{{ $cate->name }}</option>
        @endforeach
    </select>

    <label>Thương hiệu:</label>
    <select name="brand_id" class="form-control">
        @foreach($brands as $brand)
            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
        @endforeach
    </select>

    <label>Số lượng:</label>
    <input type="number" name="quantity" class="form-control" min="0" required>

    <label>Giá:</label>
    <input type="number" name="price" class="form-control" min="0" required>

    <div class="mb-3">
    <label for="discount" class="form-label">Giảm giá (%)</label>
    <input type="number" name="discount" id="discount" 
           class="form-control" 
           value="{{ old('discount', $product->discount ?? 0) }}" 
           min="0" max="100" placeholder="Nhập phần trăm giảm giá (0 - 100)">
    </div>

    <label>Ảnh:</label>
    <input type="file" name="image" class="form-control">

    <button type="submit" class="btn btn-success mt-2">Lưu</button>
</form>
</div>
@endsection