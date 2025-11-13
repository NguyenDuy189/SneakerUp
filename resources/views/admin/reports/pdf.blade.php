<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Báo cáo SneakerUp - {{ $start }} → {{ $end }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; }
        .header { display:flex; align-items:center; gap:16px; margin-bottom:20px; }
        .logo { width:160px; }
        .title { font-size:18px; font-weight:700; }
        table { width:100%; border-collapse: collapse; margin-bottom:12px; }
        th, td { border:1px solid #ddd; padding:6px 8px; text-align:left; }
        th { background:#f5f5f5; }
        .small { font-size:11px; color:#666; }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/logo.png') }}" class="logo" alt="SneakerUp">
        <div>
            <div class="title">Báo cáo SneakerUp</div>
            <div class="small">Thời gian: {{ $start }} → {{ $end }}</div>
            <div class="small">Xuất lúc: {{ now()->toDateTimeString() }}</div>
        </div>
    </div>

    <h3>1. Tổng quan</h3>
    <table>
        <tbody>
            <tr>
                <th>Doanh thu (khoảng)</th>
                <td>{{ number_format($revenue, 0, ',', '.') }} ₫</td>
            </tr>
        </tbody>
    </table>

    <h3>2. Top sản phẩm bán chạy</h3>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Sản phẩm</th>
                <th>Đã bán</th>
                <th>Doanh thu</th>
            </tr>
        </thead>
        <tbody>
            @foreach($topProducts as $i => $p)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $p->product->name ?? 'N/A' }}</td>
                    <td>{{ $p->total_sold }}</td>
                    <td>{{ number_format($p->revenue, 0, ',', '.') }} ₫</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3>3. Sản phẩm tồn kho thấp</h3>
    <table>
        <thead>
            <tr><th>#</th><th>Sản phẩm</th><th>Tồn kho</th></tr>
        </thead>
        <tbody>
            @foreach($lowStockProducts as $i => $p)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $p->name }}</td>
                    <td>{{ $p->computed_stock ?? ($p->stock ?? 0) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="small">Báo cáo được tự động tạo bởi hệ thống SneakerUp</div>
</body>
</html>
