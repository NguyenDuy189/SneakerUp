@extends('admin.layout')

@section('content')
<div class="container">
    <h3>Quản lý mã giảm giá</h3>
    <a href="{{ route('admin.discounts.create') }}" class="btn btn-success mb-3">+ Thêm mã</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Mã</th>
                <th>Loại</th>
                <th>Giá trị</th>
                <th>Lượt dùng</th>
                <th>Giới hạn</th>
                <th>Ngày hết hạn</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($discounts as $key => $d)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $d->code }}</td>
                <td>{{ $d->type == 'percent' ? 'Phần trăm' : 'Cố định' }}</td>
                <td>{{ $d->value }}</td>
                <td>{{ $d->used_count }}</td>
                <td>{{ $d->usage_limit }}</td>
                <td>{{ $d->expiry_date }}</td>
                <td>
                    <span class="badge {{ $d->status ? 'bg-success' : 'bg-secondary' }}">
                        {{ $d->status ? 'Đang hoạt động' : 'Ngưng' }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('admin.discounts.edit', $d->id) }}" class="btn btn-warning btn-sm">Sửa</a>
                    <form action="{{ route('admin.discounts.destroy', $d->id) }}" method="POST" style="display:inline">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm" onclick="return confirm('Xoá mã này?')">Xoá</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $discounts->links() }}
</div>
@endsection
