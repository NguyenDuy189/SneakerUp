@extends('layouts.account')

@section('title', 'Lịch Sử Mua Hàng')

@section('account_content')
<div class="container"> {{-- Giả sử bạn có class container của Bootstrap/CSS --}}
    <h2>Lịch Sử Mua Hàng</h2>
    <p>Theo dõi các đơn hàng của bạn tại đây.</p>

    <table class="table" style="width: 100%; border-collapse: collapse; margin-top: 20px;">
        <thead style="background-color: #f8f9fa;">
            <tr>
                <th style="padding: 12px; border-bottom: 2px solid #dee2e6; text-align: left;">Mã Đơn Hàng</th>
                <th style="padding: 12px; border-bottom: 2px solid #dee2e6; text-align: left;">Ngày Đặt</th>
                <th style="padding: 12px; border-bottom: 2px solid #dee2e6; text-align: left;">Tổng Tiền</th>
                <th style="padding: 12px; border-bottom: 2px solid #dee2e6; text-align: left;">Trạng Thái Giao Hàng</th>
                <th style="padding: 12px; border-bottom: 2px solid #dee2e6; text-align: left;">Hình Thức Thanh Toán</th>
                <th style="padding: 12px; border-bottom: 2px solid #dee2e6; text-align: left;"></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($orders as $order)
                <tr style="border-bottom: 1px solid #dee2e6;">
                    {{-- SỬA 1: Dùng "code" thay vì "id" cho chuyên nghiệp --}}
                    <td style="padding: 12px;">#{{ $order->code }}</td>
                    <td style="padding: 12px;">{{ $order->created_at->format('d/m/Y') }}</td>

                    {{-- SỬA 2: Dùng "total_price" thay vì "total_amount" --}}
                    <td style="padding: 12px;">{{ number_format($order->total_price, 0, ',', '.') }} đ</td>

                    <td style="padding: 12px;">{{ $order->status }}</td>

                    {{-- SỬA 3: Dùng "payment_method" thay vì "payment_status" --}}
                    <td style="padding: 12px;">{{ $order->payment_method }}</td>

                    <td style="padding: 12px;">

                        <a href="{{ route('client.orders.show', $order->id) }}" class="btn btn-sm btn-info" style="padding: 5px 10px; background-color: #17a2b8; color: white; text-decoration: none; border-radius: 4px;">Xem Chi Tiết</a>

                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="padding: 12px; text-align: center;">Bạn chưa có đơn hàng nào.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="pagination-container" style="margin-top: 20px;">
        {{ $orders->links() }}
    </div>
</div>
@endsection
