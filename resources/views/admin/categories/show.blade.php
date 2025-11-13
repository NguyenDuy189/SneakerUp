@extends('admin.layouts.app')

@section('title', $category->name)

@section('content')
<div class="container-fluid py-4">
    <h4>Danh mục: {{ $category->name }}</h4>
    <p>Trạng thái: <strong>{{ $category->status_label }}</strong></p>
    <p>Số sản phẩm (bao gồm danh mục con): <strong>{{ $products->count() }}</strong></p>

    <div class="table-responsive mt-3">
        <table class="table table-striped table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>STT</th>
                    <th>Tên sản phẩm</th>
                    <th>Giá</th>
                    <th>Trạng thái</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $index => $product)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $product->name }}</td>
                        <td>{{ number_format($product->price, 0, ',', '.') }} đ</td>
                        <td>{{ $product->status_label ?? '' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">Không có sản phẩm nào</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <a href="{{ route('admin.categories.tree') }}" class="btn btn-secondary mt-3">Quay lại cây danh mục</a>
</div>
@endsection
