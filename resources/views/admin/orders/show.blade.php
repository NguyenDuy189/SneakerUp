@extends('layouts.admin')

@section('title', 'Chi Tiết Đơn Hàng: #' . $order->code)

@section('content')
<div class="order-detail-container">
    <div class="order-actions">
        <a href="{{ route('admin.customers.show', $order->user_id) }}" class="btn btn-secondary">Quay lại khách hàng</a>
    </div>

    {{-- Chia làm 2 cột: Thông tin giao hàng và thông tin đơn hàng --}}
    <div class="order-detail-grid">
        <div class="card">
            <h4>Thông tin giao hàng</h4>
            <p><strong>Khách hàng:</strong> {{ $order->fullname }}</p>
            <p><strong>Địa chỉ:</strong> {{ $order->address }}</p>
            <p><strong>Số điện thoại:</strong> {{ $order->phone }}</p>
        </div>
        <div class="card">
            <h4>Thông tin đơn hàng</h4>
            <p><strong>Mã đơn hàng:</strong> #{{ $order->code }}</p>
            <p><strong>Ngày đặt:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
            <p><strong>Phương thức thanh toán:</strong> {{ $order->payment_method }}</p>
            <p><strong>Trạng thái:</strong> <span class="order-status status-{{ $order->status }}">{{ $order->status }}</span></p>
        </div>
    </div>

    {{-- Bảng chi tiết sản phẩm --}}
    <div class="card">
        <h4>Các sản phẩm trong đơn hàng</h4>
        <table>
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th>Số lượng</th>
                    <th>Đơn giá</th>
                    <th>Thành tiền</th>
                </tr>
            </thead>
            <tbody>
    @forelse ($order->details as $detail)
        <tr>
            <td>
                <strong>{{ $detail->product_name }}</strong><br>
                <small>Màu: {{ $detail->color_name }} - Size: {{ $detail->size_value }}</small>
            </td>
            <td>{{ $detail->quantity }}</td>
            <td>{{ number_format($detail->price, 0, ',', '.') }}đ</td>
            <td>{{ number_format($detail->subtotal, 0, ',', '.') }}đ</td>
        </tr>
    @empty
        <tr>
            <td colspan="4" style="text-align: center;">Không có chi tiết sản phẩm cho đơn hàng này.</td>
        </tr>
    @endforelse
</tbody>
        </table>
    </div>
</div>
@endsection

@push('styles')
<style>
    .order-detail-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
    .order-actions {
        margin-bottom: 20px;
    }
</style>
@endpush
