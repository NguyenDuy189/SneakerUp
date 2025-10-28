@extends('layouts.admin')

@section('content')
<div class="content-wrapper">
    <div class="mb-3">
    <a href="{{ route('products.export') }}" class="btn btn-success">📤 Xuất Excel</a>
</div>

<form action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="input-group mb-3" style="max-width: 400px;">
        <input type="file" name="file" class="form-control" required>
        <button type="submit" class="btn btn-primary">📥 Nhập Excel</button>
    </div>
</form>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
    <form method="GET" action="{{ route('admin.products.index') }}" class="mb-3 d-flex align-items-center gap-3">
        <input type="text" name="search" placeholder="Tìm sản phẩm..." 
               value="{{ $search ?? '' }}" class="form-control w-25">

        <select name="category" class="form-select w-25">
            <option value="">-- Tất cả danh mục --</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ ($cat->id == ($category ?? '')) ? 'selected' : '' }}>
                    {{ $cat->name }}
                </option>
            @endforeach
        </select>

        <select name="sort" class="form-select w-25">
            <option value="">-- Sắp xếp theo --</option>
            <option value="price_asc" {{ ($sort ?? '') == 'price_asc' ? 'selected' : '' }}>Giá tăng dần</option>
            <option value="price_desc" {{ ($sort ?? '') == 'price_desc' ? 'selected' : '' }}>Giá giảm dần</option>
            <option value="name_asc" {{ ($sort ?? '') == 'name_asc' ? 'selected' : '' }}>Tên A-Z</option>
            <option value="name_desc" {{ ($sort ?? '') == 'name_desc' ? 'selected' : '' }}>Tên Z-A</option>
        </select>

        <button type="submit" class="btn btn-primary">Lọc</button>
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Reset</a>
    </form>

    <h2 class="mb-4">Quản lý sản phẩm</h2>
    <a href="{{ route('admin.products.create') }}" class="btn btn-success mb-3">+ Thêm sản phẩm</a>

    <table class="table table-bordered text-center align-middle">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Ảnh</th>
                <th>Tên sản phẩm</th>
                <th>Danh mục</th>
                <th>Thương hiệu</th>
                <th>Giá</th>
                <th>Giảm giá</th>
                <th>Tags</th>
                <th>Nổi bật</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $index => $product)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        @if($product->image)
                            <img src="{{ asset('uploads/products/' . $product->image) }}" alt="Ảnh" width="70">
                        @else
                            <span class="text-muted">Không có</span>
                        @endif
                    </td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->category->name ?? '---' }}</td>
                    <td>{{ $product->brand->name ?? '---' }}</td>

                    {{-- Giá --}}
                    <td>
                        @if($product->final_price < $product->price)
                            <span style="text-decoration: line-through; color: #999;">
                                {{ number_format($product->price) }}₫
                            </span><br>
                            <span style="color: red; font-weight: bold;">
                                {{ number_format($product->final_price) }}₫
                            </span>
                        @else
                            {{ number_format($product->price) }}₫
                        @endif
                    </td>

                    {{-- Giảm giá --}}
                    <td>
                        @if($product->discount && $product->discount->percent)
                            {{ $product->discount->percent }}%
                        @elseif($product->discount && $product->discount->amount)
                            {{ number_format($product->discount->amount) }}₫
                        @else
                            <span class="text-muted">Không có</span>
                        @endif
                    </td>

                    {{-- Tags --}}
                    <td>
                        @if($product->tags->count())
                            @foreach($product->tags as $tag)
                                <span class="badge bg-secondary">{{ $tag->name }}</span>
                            @endforeach
                        @else
                            <span class="text-muted">Không có</span>
                        @endif
                    </td>
                    
                    {{-- Nổi bật --}}
                    <td>
                        <button 
                        class="btn btn-sm toggle-featured {{ $product->is_featured ? 'btn-success' : 'btn-secondary' }}" 
                        data-id="{{ $product->id }}">
                        {{ $product->is_featured ? 'Đang bật' : 'Đang tắt' }}
                        </button>
                    </td>

                    {{-- Trạng thái --}}
                    <td>
                        @if($product->status)
                            <span class="badge bg-success">Còn hàng</span>
                        @else
                            <span class="badge bg-danger">Hết hàng</span>
                        @endif
                    </td>

                    {{-- Hành động --}}
                    <td>
                        <a href="{{ route('variants.index', $product->id) }}" class="btn btn-info btn-sm">Biến thể</a>
                        <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-warning btn-sm">Sửa</a>
                        <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm" onclick="return confirm('Xóa sản phẩm này?')">Xóa</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="d-flex justify-content-center mt-3">
        {{ $products->links() }}
    </div>
</div>
@endsection
