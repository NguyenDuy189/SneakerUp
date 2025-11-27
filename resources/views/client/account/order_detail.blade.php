@extends('layouts.account')

@section('title', 'Chi Tiết Đơn Hàng #' . $order->code)

@section('account_content')
<div class="container">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('client.orders.index') }}" style="text-decoration: none;">&larr; Quay lại Lịch sử đơn hàng</a>
    </div>

    <h2>Chi Tiết Đơn Hàng #{{ $order->code }}</h2>
    <p>Ngày đặt: {{ $order->created_at->format('d/m/Y H:i') }}</p>

    <div class="order-summary" style="display: flex; gap: 20px; margin-top: 20px;">

        <div style="flex: 1; border: 1px solid #eee; padding: 20px; border-radius: 8px;">
            <h4>Thông tin Giao hàng</h4>
            <hr style="margin: 10px 0;">
            <p><strong>Tên người nhận:</strong> {{ $order->fullname }}</p>
            <p><strong>Điện thoại:</strong> {{ $order->phone }}</p>
            <p><strong>Địa chỉ:</strong> {{ $order->address }}</p>
        </div>

        <div style="flex: 1; border: 1px solid #eee; padding: 20px; border-radius: 8px;">
            <h4>Tóm tắt Đơn hàng</h4>
            <hr style="margin: 10px 0;">
            <p><strong>Hình thức thanh toán:</strong> {{ $order->payment_method }}</p>
            <p><strong>Trạng thái giao hàng:</strong> {{ $order->status }}</p>
            <p><strong>Phí vận chuyển:</strong> {{ number_format($order->shipping_fee, 0, ',', '.') }} đ</p>
            <p><strong>Tổng tiền:</strong> <strong style="color: #dc3545; font-size: 1.2em;">{{ number_format($order->total_price, 0, ',', '.') }} đ</strong></p>
        </div>
    </div>

    <div class="order-items" style="margin-top: 30px;">
        <h4>Các sản phẩm đã đặt</h4>
        <table class="table" style="width: 100%; border-collapse: collapse; margin-top: 15px;">
            <thead style="background-color: #f8f9fa;">
                <tr>
                    <th style="padding: 12px; border-bottom: 2px solid #dee2e6; text-align: left;">Sản phẩm</th>
                    <th style="padding: 12px; border-bottom: 2px solid #dee2e6; text-align: left;">Thông tin</th>
                    <th style="padding: 12px; border-bottom: 2px solid #dee2e6; text-align: right;">Đơn giá</th>
                    <th style="padding: 12px; border-bottom: 2px solid #dee2e6; text-align: center;">Số lượng</th>
                    <th style="padding: 12px; border-bottom: 2px solid #dee2e6; text-align: right;">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->details as $item)
                    <tr style="border-bottom: 1px solid #dee2e6;">
                        <td style="padding: 12px;">{{ $item->product_name }}</td>
                        <td style="padding: 12px;">Màu: {{ $item->color_name }}, Size: {{ $item->size_value }}</td>
                        <td style="padding: 12px; text-align: right;">{{ number_format($item->price, 0, ',', '.') }} đ</td>
                        <td style="padding: 12px; text-align: center;">{{ $item->quantity }}</td>
                        <td style="padding: 12px; text-align: right;">{{ number_format($item->subtotal, 0, ',', '.') }} đ</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
