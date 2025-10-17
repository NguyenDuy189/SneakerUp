@extends('layouts.admin')


@section('content')
<div class="content-wrapper">
    <h2 class="mb-4">Quản lý sản phẩm</h2>
    <a href="{{ route('admin.products.create') }}" class="btn btn-success mb-3">+ Thêm sản phẩm</a>
    <table class="table table-bordered text-center align-middle">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Ảnh</th>
                <th>Tên Sản Phẩm</th>
                <th>Danh Mục</th>
                <th>Số Lượng</th>
                <th>Giá</th>
                <th>Trạng Thái</th>
                <th>Hành Động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $index => $product)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>
                    @if($product->images->first())
                        <img src="{{ asset('storage/'.$product->images->first()->image) }}" width="50" height="50" style="object-fit: cover;">
                    @else
                        <span>X</span>
                    @endif
                </td>
                <td>{{ $product->name }}</td>
                <td>{{ $product->category->name ?? '---' }}</td>
                <td>{{ $product->quantity }}</td>
                <td>{{ number_format($product->price, 0, ',', '.') }}đ</td>
                <td>
                    @if($product->status == 1)
                        <span class="badge bg-success">Còn hàng</span>
                    @else
                        <span class="badge bg-danger">Hết hàng</span>
                    @endif
                </td>
                <td>
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