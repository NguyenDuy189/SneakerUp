@extends('admin.layout')

@section('content')
<div class="container mt-4">
    <h3 class="mb-3">📦 Quản lý kho hàng</h3>

    @if($lowStockProducts->count() > 0)
    <div class="alert alert-danger">
        ⚠️ <strong>Cảnh báo:</strong> Có {{ $lowStockProducts->count() }} sản phẩm sắp hết hàng:
        <ul class="mb-0">
            @foreach($lowStockProducts as $p)
                <li>{{ $p->name }} (Còn {{ $p->variants->sum('stock') + $p->quantity }})</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="GET" class="d-flex mb-3">
        <input type="text" name="keyword" class="form-control me-2" placeholder="Tìm sản phẩm..." value="{{ request('keyword') }}">
        <button class="btn btn-primary">Tìm kiếm</button>
    </form>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered align-middle text-center">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Ảnh</th>
                <th>Tên sản phẩm</th>
                <th>Thương hiệu</th>
                <th>Danh mục</th>
                <th>Tổng tồn kho</th>
                <th>Giá</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $p)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        @if($p->image)
                            <img src="{{ asset('storage/' . $p->image) }}" width="60">
                        @else
                            Không có
                        @endif
                    </td>
                    <td>{{ $p->name }}</td>
                    <td>{{ $p->brand->name ?? 'Không có' }}</td>
                    <td>{{ $p->category->name ?? 'Không có' }}</td>
                    <td>{{ $p->variants->sum('stock') + $p->quantity }}</td>
                    <td>{{ number_format($p->price, 0, ',', '.') }}₫</td>
                    <td>
                        <a href="{{ route('admin.warehouse.import', $p->id) }}" class="btn btn-success btn-sm">Nhập kho</a>
                        <a href="{{ route('admin.warehouse.export', $p->id) }}" class="btn btn-danger btn-sm">Xuất kho</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $products->links() }}
</div>
@endsection
