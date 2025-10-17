@extends('layouts.admin')

@section('content')
<div class="container mt-4">
<h2>Thêm sản phẩm mới</h2>
<form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
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

    <label>Ảnh:</label>
    <input type="file" name="image" class="form-control">

    <button type="submit" class="btn btn-success mt-2">Lưu</button>
</form>
</div>
@endsection