@extends('admin.layout')

@section('content')
<div class="container mt-4">
    <h3>📜 Nhật ký nhập – xuất kho</h3>

    <div class="mb-3">
        <a href="{{ route('admin.warehouse.logs', ['type' => 'all']) }}" class="btn btn-secondary btn-sm">Tất cả</a>
        <a href="{{ route('admin.warehouse.logs', ['type' => 'import']) }}" class="btn btn-success btn-sm">Chỉ nhập</a>
        <a href="{{ route('admin.warehouse.logs', ['type' => 'export']) }}" class="btn btn-danger btn-sm">Chỉ xuất</a>
    </div>

    <table class="table table-bordered text-center align-middle">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Loại</th>
                <th>Sản phẩm</th>
                <th>Biến thể</th>
                <th>Số lượng</th>
                <th>Giá</th>
                <th>Ngày</th>
            </tr>
        </thead>
        <tbody>
            @foreach($imports as $i)
                <tr class="table-success">
                    <td>{{ $loop->iteration }}</td>
                    <td>Nhập</td>
                    <td>{{ $i->variant->product->name ?? '---' }}</td>
                    <td>{{ $i->variant->color ?? '-' }} / {{ $i->variant->size ?? '-' }}</td>
                    <td>+{{ $i->quantity }}</td>
                    <td>{{ number_format($i->price, 0, ',', '.') }}₫</td>
                    <td>{{ $i->created_at }}</td>
                </tr>
            @endforeach

            @foreach($exports as $e)
                <tr class="table-danger">
                    <td>{{ $loop->iteration }}</td>
                    <td>Xuất</td>
                    <td>{{ $e->variant->product->name ?? '---' }}</td>
                    <td>{{ $e->variant->color ?? '-' }} / {{ $e->variant->size ?? '-' }}</td>
                    <td>-{{ $e->quantity }}</td>
                    <td>{{ number_format($e->price, 0, ',', '.') }}₫</td>
                    <td>{{ $e->created_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
