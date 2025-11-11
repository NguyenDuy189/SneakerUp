@extends('admin.layout')

@section('content')
<div class="container mt-4">
    <h4>{{ $action == 'import' ? '➕ Nhập kho' : '➖ Xuất kho' }} - {{ $product->name }}</h4>

    <form method="POST" action="{{ $action == 'import' ? route('admin.warehouse.import.store', $product->id) : route('admin.warehouse.export.store', $product->id) }}">
        @csrf

        <div class="mb-3">
            <label>Chọn biến thể sản phẩm:</label>
            <select name="variant_id" class="form-select" required>
                <option value="">-- Chọn biến thể --</option>
                @foreach($product->variants as $v)
                    <option value="{{ $v->id }}">{{ $v->color }} - Size {{ $v->size }} (Tồn: {{ $v->stock }})</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Số lượng:</label>
            <input type="number" name="quantity" class="form-control" min="1" required>
        </div>

        @if($action == 'import')
        <div class="mb-3">
            <label>Giá nhập (VNĐ):</label>
            <input type="number" name="price" class="form-control" min="0" required>
        </div>
        @endif

        <button class="btn btn-primary">{{ $action == 'import' ? 'Lưu nhập kho' : 'Lưu xuất kho' }}</button>
        <a href="{{ route('admin.warehouse.index') }}" class="btn btn-secondary">Quay lại</a>
    </form>
</div>
@endsection
