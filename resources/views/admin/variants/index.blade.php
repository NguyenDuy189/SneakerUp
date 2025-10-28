@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <h3>Biến thể của sản phẩm: {{ $product->name }}</h3>
    <a href="{{ route('variants.create', $product->id) }}" class="btn btn-success mb-3">+ Thêm biến thể</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Size</th>
                <th>Màu sắc</th>
                <th>Hình ảnh</th>
                <th>Số lượng</th>
                <th>Giá</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($variants as $variant)
                <tr>
                    <td>{{ $variant->id }}</td>
                    <td>{{ $variant->size }}</td>
                    <td>{{ $variant->color }}</td>
                    <td>
                        @if ($variant->image)
                            <img src="{{ asset('storage/'.$variant->image) }}" width="60">
                        @else
                            Không có
                        @endif
                    </td>
                    <td>{{ $variant->stock }}</td>
                    <td>{{ number_format($variant->price) }}đ</td>
                    <td>
                        <form action="{{ route('variants.destroy', $variant->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm">Xóa</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
