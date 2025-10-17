@extends('layouts.admin')

@section('title', 'Quản Lý Khách Hàng')

@section('content')
<div class="table-container">
    <div class="table-header">
        <form action="{{ route('admin.customers.index') }}" method="GET" class="search-container">
            <input type="text" name="search" placeholder="Tìm kiếm khách hàng..." value="{{ request('search') }}">
            <button type="submit" class="search-button">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>
    <table>
        <thead>
            <tr>
                <th>Họ và Tên</th>
                <th>Email</th>
                <th class="text-right">Số đơn hàng</th>
                <th class="text-right">Tổng chi tiêu</th>
                <th>Ngày đăng ký</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            {{-- SỬA LỖI NẰM Ở ĐÂY: Đổi biến $users thành $customers --}}
            @forelse ($customers as $customer)
                <tr>
                    <td>{{ $customer->fullname }}</td>
                    <td>{{ $customer->email }}</td>
                    <td class="text-right">{{ $customer->orders_count }}</td>
                    <td class="text-right">{{ number_format($customer->orders_sum_total_price ?? 0, 0, ',', '.') }}đ</td>
                    <td>{{ $customer->created_at->format('d/m/Y') }}</td>
                    <td>
                        <span class="status-tag status-{{ $customer->status }}">{{ ucfirst($customer->status) }}</span>
                    </td>
                    <td class="action-buttons">
                        <a href="{{ route('admin.customers.show', $customer->id) }}" class="action-icon view-icon" title="Xem chi tiết"><i class="fas fa-eye"></i></a>
                        @can('user-edit')
                            <a href="{{ route('admin.customers.edit', $customer->id) }}" class="action-icon edit-icon" title="Sửa (Khóa/Mở)"><i class="fas fa-user-lock"></i></a>
                        @endcan
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center;">Không tìm thấy khách hàng nào.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="pagination-container">
        {{-- SỬA LỖI NẰM Ở ĐÂY: Đổi biến $users thành $customers --}}
        {{ $customers->appends(request()->query())->links() }}
    </div>
</div>
@endsection
