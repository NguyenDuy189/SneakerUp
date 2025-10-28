<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Dashboard extends Model
{
    /**
     * Tổng quan: Doanh thu, đơn hàng, khách hàng, sản phẩm
     */
    public static function getSummary(): array
    {
        return [
            'totalRevenue'  => DB::table('orders')->where('status', 'completed')->sum('total_price'),
            'totalOrders'   => DB::table('orders')->count(),
            'totalUsers'    => DB::table('users')->count(),
            'totalProducts' => DB::table('products')->count(),
        ];
    }

    /**
     * Doanh thu theo tháng (12 tháng gần nhất)
     */
    public static function getMonthlyRevenue(): array
    {
        $data = DB::table('orders')
            ->selectRaw('MONTH(created_at) as month, SUM(total_price) as revenue')
            ->where('status', 'completed')
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->pluck('revenue', 'month')
            ->toArray();

        return array_map(fn($m) => $data[$m] ?? 0, range(1, 12));
    }

    /**
     * Top 5 sản phẩm bán chạy nhất
     */
    public static function getTopProducts(): array
    {
        return DB::table('order_details')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->join('orders', 'orders.id', '=', 'order_details.order_id')
            ->select('products.name', DB::raw('SUM(order_details.quantity) as sold'))
            ->where('orders.status', 'completed')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('sold')
            ->limit(5)
            ->get()
            ->toArray();
    }

    /**
     * Đơn hàng gần nhất
     */
    public static function getRecentOrders(int $limit = 8)
    {
        return DB::table('orders')
            ->leftJoin('users', 'users.id', '=', 'orders.user_id')
            ->select('orders.id', 'orders.code', 'users.fullname', 'orders.total_price', 'orders.status', 'orders.created_at')
            ->orderByDesc('orders.created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Lấy danh sách sản phẩm sắp hết hàng
     * (Tính tổng stock từ tất cả variant của từng sản phẩm)
     */
    public static function getLowStockProducts(int $threshold = 5)
    {
        return DB::table('product_variants')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->select(
                'products.id',
                'products.name',
                DB::raw('SUM(product_variants.stock) as total_stock')
            )
            ->groupBy('products.id', 'products.name')
            ->havingRaw('SUM(product_variants.stock) < ?', [$threshold])
            ->orderBy('total_stock', 'asc')
            ->limit(5)
            ->get();
    }


    /**
     * 🆕 Khách hàng mới (7 ngày gần nhất)
     */
    public static function getNewCustomers()
    {
        return DB::table('users')
            ->select('fullname', 'email', 'created_at')
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();
    }

    /**
     * 🆕 Khách hàng thân thiết (số đơn > 5)
     */
    public static function getLoyalCustomers()
    {
        return DB::table('orders')
            ->join('users', 'users.id', '=', 'orders.user_id')
            ->select('users.fullname', DB::raw('COUNT(orders.id) as total_orders'))
            ->groupBy('users.id', 'users.fullname')
            ->havingRaw('COUNT(orders.id) > 5')
            ->orderByDesc('total_orders')
            ->limit(5)
            ->get();
    }

    /**
     * 🆕 Thông báo hệ thống cơ bản
     */
    public static function getNotifications()
    {
        $lowStockCount = DB::table('product_variants')
                        ->join('products', 'product_variants.product_id', '=', 'products.id')
                        ->select('products.id', DB::raw('SUM(product_variants.stock) as total_stock'))
                        ->groupBy('products.id')
                        ->havingRaw('SUM(product_variants.stock) < 5')
                        ->count();
        $pendingOrders = DB::table('orders')->where('status', 'pending')->count();

        return collect([
            [
                'type'    => 'warning',
                'message' => "{$lowStockCount} sản phẩm sắp hết hàng",
                'time'    => now()->subMinutes(10)->diffForHumans(),
            ],
            [
                'type'    => 'info',
                'message' => "{$pendingOrders} đơn hàng đang chờ xử lý",
                'time'    => now()->subHours(1)->diffForHumans(),
            ],
        ]);
    }
}
