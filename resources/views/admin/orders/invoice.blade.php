<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .logo {
            width: 100px;
            margin-bottom: 10px;
        }
        .invoice-title {
            font-size: 20px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .info-table td {
            padding: 4px 8px;
            vertical-align: top;
        }
        table.product-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table.product-table th, table.product-table td {
            border: 1px solid #555;
            padding: 6px;
            text-align: center;
        }
        table.product-table th {
            background-color: #f2f2f2;
        }
        .totals {
            margin-top: 15px;
            width: 100%;
        }
        .totals td {
            padding: 6px;
        }
        .signatures {
            margin-top: 40px;
            text-align: center;
        }
        .signatures td {
            width: 50%;
            vertical-align: top;
        }
        .footer {
            text-align: center;
            margin-top: 50px;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>

<div class="header">
    <img src="{{ public_path('images/logo.jpg') }}" alt="SneakerUp" class="logo">
    <div class="invoice-title">HÓA ĐƠN BÁN HÀNG</div>
    <div>Mã đơn: {{ $order->code }}</div>
</div>

<table class="info-table" width="100%">
    <tr>
        <td>
            <strong>Người mua:</strong> {{ $order->fullname }} <br>
            <strong>Địa chỉ:</strong> {{ $order->address }} <br>
            <strong>Điện thoại:</strong> {{ $order->phone }}
        </td>
        <td align="right">
            <strong>Ngày lập:</strong> {{ $order->created_at->format('d/m/Y') }} <br>
            <strong>Phương thức thanh toán:</strong> {{ strtoupper($order->payment_method ?? '---') }} <br>
            <strong>Trạng thái:</strong> {{ ucfirst($order->status) }}
        </td>
    </tr>
</table>

<table class="product-table">
    <thead>
        <tr>
            <th>STT</th>
            <th>Tên sản phẩm</th>
            <th>Màu</th>
            <th>Size</th>
            <th>Số lượng</th>
            <th>Giá</th>
            <th>Thành tiền</th>
        </tr>
    </thead>
    <tbody>
        @foreach($order->orderDetails as $index => $detail)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td align="left">{{ $detail->product_name }}</td>
            <td>{{ $detail->color_name ?? '-' }}</td>
            <td>{{ $detail->size_value ?? '-' }}</td>
            <td>{{ $detail->quantity }}</td>
            <td>{{ number_format($detail->price, 0, ',', '.') }} ₫</td>
            <td>{{ number_format($detail->subtotal, 0, ',', '.') }} ₫</td>
        </tr>
        @endforeach
    </tbody>
</table>

<table class="totals">
    <tr>
        <td align="right"><strong>Tạm tính:</strong></td>
        <td align="right">{{ number_format($order->total_price - ($order->shipping_fee ?? 0), 0, ',', '.') }} ₫</td>
    </tr>
    <tr>
        <td align="right"><strong>Phí vận chuyển:</strong></td>
        <td align="right">{{ number_format($order->shipping_fee ?? 0, 0, ',', '.') }} ₫</td>
    </tr>
    <tr>
        <td align="right"><strong>Tổng cộng:</strong></td>
        <td align="right"><strong>{{ number_format($order->total_price, 0, ',', '.') }} ₫</strong></td>
    </tr>
</table>

<table class="signatures" width="100%">
    <tr>
        <td>
            <strong>Người lập hóa đơn</strong><br><br><br>
            <em>(Ký và ghi rõ họ tên)</em>
        </td>
        <td>
            <strong>Khách hàng</strong><br><br><br>
            <em>(Ký và ghi rõ họ tên)</em>
        </td>
    </tr>
</table>

<div class="footer">
    Cảm ơn bạn đã mua sắm tại SneakerUp!<br>
    Website: www.sneakerup.vn — Hotline: 0123 456 789
</div>

</body>
</html>
