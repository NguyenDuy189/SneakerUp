@extends('layouts.admin')

@section('title', 'Báo Cáo và Thống Kê')

@section('content')
<div class="dashboard-container">

    {{-- ================= BẮT ĐẦU PHẦN SỬA ================= --}}

    {{-- Hàng trên cùng chứa các card thống kê --}}
    {{-- Cần có thẻ div này để xếp 3 card thành hàng ngang --}}
    <div class="stat-cards-container">
        <div class="stat-card">
            <p class="stat-title">Doanh thu tháng</p>
            {{-- Thay số tĩnh bằng biến --}}
            <p class="stat-value">{{ number_format($totalRevenue, 0, ',', '.') }}đ</p>
            <p class="stat-comparison">+15% so với tháng trước</p>
        </div>
        <div class="stat-card">
            <p class="stat-title">Sản phẩm đã bán</p>
            {{-- Thay số tĩnh bằng biến --}}
            <p class="stat-value">{{ number_format($productsSoldCount, 0, ',', '.') }}</p>
            <p class="stat-comparison">+120 sản phẩm so với tháng trước</p>
        </div>
        <div class="stat-card">
            <p class="stat-title">Khách hàng mới</p>
            {{-- Thay số tĩnh bằng biến --}}
            <p class="stat-value">{{ $newCustomersCount }}</p>
            <p class="stat-comparison">+11% so với tháng trước</p>
        </div>
    </div>

    {{-- ================= KẾT THÚC PHẦN SỬA ================= --}}

    {{-- Hàng thứ hai chứa 2 bảng nhỏ --}}
    <div class="tables-row">
        <div class="table-container">
            <h4>Doanh thu theo tuần</h4>
            <table>
                <thead>
                    <tr>
                        <th>Tuần</th>
                        <th>Doanh thu</th>
                        <th>Thay đổi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>20.000.000đ</td>
                        <td class="positive">+5%</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>35.000.000đ</td>
                        <td class="positive">+10%</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>28.000.000đ</td>
                        <td class="negative">-8%</td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>42.000.000đ</td>
                        <td class="positive">+15%</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="table-container">
            <h4>Top sản phẩm bán chạy</h4>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên sản phẩm</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Xxxxx</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Xxxxx</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>Xxxxx</td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>Xxxxx</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Bảng lớn ở dưới cùng --}}
    <div class="table-container large-table">
        <h4>Báo cáo chi tiết</h4>
        <table>
            <thead>
                <tr>
                    <th>Ngày</th>
                    <th>Đơn hàng</th>
                    <th>Doanh thu</th>
                    <th>Khách mới</th>
                    <th>Sản phẩm bán chạy</th>
                    <th>Tỉ lệ hủy</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>01/09/2025</td>
                    <td>50</td>
                    <td>20.000.000đ</td>
                    <td>10</td>
                    <td>Xxxxx</td>
                    <td>1%</td>
                </tr>
                <tr>
                    <td>02/09/2025</td>
                    <td>70</td>
                    <td>35.000.000đ</td>
                    <td>15</td>
                    <td>Xxxxx</td>
                    <td>2%</td>
                </tr>
                <tr>
                    <td>03/09/2025</td>
                    <td>68</td>
                    <td>28.000.000đ</td>
                    <td>9</td>
                    <td>Xxxxx</td>
                    <td>2.7%</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
