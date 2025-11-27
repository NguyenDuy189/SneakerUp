<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User; // Giả sử bạn có Model User
// use App\Models\Product; // Giả sử bạn có Model Product
// use App\Models\Order; // Giả sử bạn có Model Order

class DashboardController extends Controller
{
    public function index()
    {
        // --- TÍNH TOÁN CÁC SỐ LIỆU THỐNG KÊ ---

        // 1. Đếm tổng số khách hàng mới (có vai trò là 'customer')
        $newCustomersCount = User::where('role', 'customer')->count();

        // 2. Đếm tổng số sản phẩm đã bán (tạm thời để số giả định)
        $productsSoldCount = 1240; // Sẽ thay bằng logic đếm từ bảng order_details sau

        // 3. Tính tổng doanh thu (tạm thời để số giả định)
        $totalRevenue = 120000000; // Sẽ thay bằng logic tính tổng từ bảng orders sau


        // --- TRUYỀN DỮ LIỆU RA VIEW ---

        // Sử dụng hàm view() để trả về giao diện dashboard
        // và dùng hàm compact() để gửi các biến chứa số liệu ra ngoài
        return view('admin.dashboard', compact(
            'newCustomersCount',
            'productsSoldCount',
            'totalRevenue'
        ));
    }
}
