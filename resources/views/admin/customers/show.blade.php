@extends('layouts.admin')

@section('title', 'Chi Tiết Khách Hàng: ' . $customer->fullname)

@section('content')
<div class="customer-detail-container">
    {{-- Phần thông tin cơ bản của khách hàng --}}
    <div class="card">
        <h4>Thông tin cơ bản</h4>
        <p><strong>ID:</strong> {{ $customer->id }}</p>
        <p><strong>Họ và tên:</strong> {{ $customer->fullname }}</p>
        <p><strong>Email:</strong> {{ $customer->email }}</p>
        <p><strong>Số điện thoại:</strong> {{ $customer->phone }}</p>
        <p><strong>Ngày tham gia:</strong> {{ $customer->created_at->format('d/m/Y') }}</p>
        <a href="{{ route('admin.customers.edit', $customer->id) }}" class="btn btn-primary" style="margin-top: 10px;">Sửa thông tin</a>
    </div>

    {{-- Phần lịch sử mua hàng --}}
    <div class="card">
        <h4>Lịch sử mua hàng</h4>
        <table>
            <thead>
                <tr>
                    <th>Mã Đơn Hàng</th>
                    <th>Ngày Đặt</th>
                    <th>Tổng Tiền</th>
                    <th>Trạng Thái</th>
                    <th>Hành Động</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr>
                        <td>#{{ $order->code }}</td>
                        <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ number_format($order->total_price, 0, ',', '.') }}đ</td>
                        <td>
                            <span class="order-status status-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
                        </td>
                        <td>
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="action-icon view-icon" title="Xem chi tiết đơn hàng"><i class="fas fa-eye"></i></a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center;">Khách hàng này chưa có đơn hàng nào.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card {
        background-color: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }
</style>
@endpush
