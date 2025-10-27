{{-- resources/views/admin/reports/orders_pdf.blade.php --}}
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Báo cáo đơn hàng (PDF)</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }
        th { background-color: #f4f4f4; }
        h2 { text-align: center; margin-top: 0; }
    </style>
</head>
<body>
    <h2>BÁO CÁO ĐƠN HÀNG</h2>
    <p>Từ {{ $startDate }} đến {{ $endDate }}</p>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Mã đơn</th>
                <th>Khách hàng</th>
                <th>Trạng thái</th>
                <th>Tổng tiền</th>
                <th>Ngày tạo</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->code }}</td>
                    <td>{{ $order->customer }}</td>
                    <td>{{ ucfirst($order->status) }}</td>
                    <td>{{ number_format($order->total_price, 0, ',', '.') }}₫</td>
                    <td>{{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
