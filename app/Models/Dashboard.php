<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Dashboard extends Model
{
    /**
     * Lấy thống kê tổng quan (doanh thu, đơn hàng, khách hàng, sản phẩm)
     */
    public static function getSummary(): array
    {
        return [
            'totalRevenue' => DB::table('orders')->where('status', 'paid')->sum('total_price'),
            'totalOrders'  => DB::table('orders')->count(),
            'totalUsers'   => DB::table('users')->count(),
            'totalProducts' => DB::table('products')->count(),
        ];
    }

    /**
     * Lấy dữ liệu doanh thu theo tháng (12 tháng gần nhất)
     */
    public static function getMonthlyRevenue(): array
    {
        $data = DB::table('orders')
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(total_price) as revenue')
            )
            ->where('status', 'paid')
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->pluck('revenue', 'month')
            ->toArray();

        $months = range(1, 12);
        return array_map(fn($m) => $data[$m] ?? 0, $months);
    }

    /**
     * Top 5 sản phẩm bán chạy nhất
     */
    public static function getTopProducts(): array
    {
        return DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select('products.name', DB::raw('SUM(order_items.quantity) as sold'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('sold')
            ->limit(5)
            ->get()
            ->toArray();
    }

    /**
     * Lấy danh sách đơn hàng gần nhất
     */
    public static function getRecentOrders(int $limit = 5)
    {
        return DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->select('orders.id', 'users.name as customer', 'orders.total_price', 'orders.status', 'orders.created_at')
            ->orderByDesc('orders.created_at')
            ->limit($limit)
            ->get();
    }
}
