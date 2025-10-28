@extends('layouts.admin')

@section('content')
<div class="container mt-4">
<h2>Sửa sản phẩm</h2>
<form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <label>Tên sản phẩm:</label>
    <input type="text" name="name" value="{{ $product->name }}" class="form-control" required>

    <label>Danh mục:</label>
    <select name="category_id" class="form-control">
        @foreach($categories as $cate)
            <option value="{{ $cate->id }}" {{ $product->category_id == $cate->id ? 'selected' : '' }}>{{ $cate->name }}</option>
        @endforeach
    </select>

    <label>Thương hiệu:</label>
    <select name="brand_id" class="form-control">
        @foreach($brands as $brand)
            <option value="{{ $brand->id }}" {{ $product->brand_id == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
        @endforeach
    </select>

    <label>Số lượng:</label>
    <input type="number" name="quantity" value="{{ $product->quantity }}" class="form-control" min="0" required>

    <label>Giá:</label>
    <input type="number" name="price" value="{{ $product->price }}" class="form-control" min="0" required>

    <div class="mb-3">
    <label for="discount" class="form-label">Giảm giá (%)</label>
    <input type="number" name="discount" id="discount" 
           class="form-control" 
           value="{{ old('discount', $product->discount ?? 0) }}" 
           min="0" max="100" placeholder="Nhập phần trăm giảm giá (0 - 100)">
    </div>
    <!--  -->
    <div class="mb-3">
    <label for="discount_id" class="form-label">Giảm giá</label>
    <select name="discount_id" id="discount_id" class="form-select">
        <option value="">-- Không giảm giá --</option>
        @foreach ($discounts as $discount)
            <option value="{{ $discount->id }}" {{ $product->discount_id == $discount->id ? 'selected' : '' }}>
                {{ $discount->name }} ({{ $discount->percent }}%)
            </option>
        @endforeach
    </select>
    </div>

    <label>Ảnh:</label><br>
    @if($product->image)
        <img src="{{ asset('uploads/products/'.$product->image) }}" width="100"><br>
    @endif
    <input type="file" name="image" class="form-control">

    <button type="submit" class="btn btn-primary mt-2">Cập nhật</button>
</form>
</div>
@endsection