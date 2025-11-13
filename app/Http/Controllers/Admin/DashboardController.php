<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Throwable;
use App\Exports\ArrayExport;
use App\Exports\DashboardExport;
use App\Models\Dashboard;
use App\Models\Order;
use App\Models\Product;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Controller chính cho Dashboard Admin SneakerUp
 * - Hiển thị thống kê tổng quan
 * - Cung cấp API cho biểu đồ
 * - Hỗ trợ xuất dữ liệu (CSV, Excel, PDF)
 */
class DashboardController extends Controller
{
    /**
     * Hiển thị trang Dashboard nâng cao
     */
    public function index(Request $request)
    {
        // 🗓️ Lọc theo thời gian hoặc mặc định 30 ngày gần nhất
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->toDateString());
        $endDate   = $request->input('end_date', Carbon::now()->toDateString());
        $year      = $request->input('year', Carbon::now()->year);

        // 📊 Dữ liệu tổng quan
        $summary            = Dashboard::getSummary();
        $monthlyRevenue     = Dashboard::getMonthlyRevenue();
        $monthlyOrders      = $this->getMonthlyOrders((int) $year);
        $statusDistribution = $this->getOrderStatusDistribution($startDate, $endDate); // 🆕 thêm dòng này

        // 📈 Dữ liệu chi tiết khác
        $topProducts      = Dashboard::getTopProducts();
        $recentOrders     = Dashboard::getRecentOrders();
        $lowStockProducts = Dashboard::getLowStockProducts();
        $newCustomers     = Dashboard::getNewCustomers();
        $loyalCustomers   = Dashboard::getLoyalCustomers();
        $notifications    = Dashboard::getNotifications();

        // ✅ Truyền toàn bộ dữ liệu sang view
        return view('admin.dashboard.index', compact(
            'summary',
            'monthlyRevenue',
            'monthlyOrders',
            'statusDistribution', // 🆕 thêm dòng này
            'topProducts',
            'recentOrders',
            'lowStockProducts',
            'newCustomers',
            'loyalCustomers',
            'notifications',
            'startDate',
            'endDate'
        ));
    }


    /**
     * AJAX endpoint – trả dữ liệu JSON cho biểu đồ (lọc theo thời gian)
     */
    public function data(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->toDateString());
        $endDate   = $request->input('end_date', Carbon::now()->toDateString());
        $year      = $request->input('year', Carbon::now()->year);

        $payload = [
            'summary' => [
                'totalRevenue' => $this->getTotalRevenue($startDate, $endDate),
                'totalOrders'  => $this->getTotalOrders($startDate, $endDate),
            ],
            'monthlyRevenue'     => $this->getMonthlyRevenue((int) $year),
            'monthlyOrders'      => $this->getMonthlyOrders((int) $year),
            'topProducts'        => $this->getTopProducts($startDate, $endDate),
            'statusDistribution' => $this->getOrderStatusDistribution($startDate, $endDate),
        ];

        return response()->json($payload);
    }

    /* ------------------------------------------------------------------------
       📤 EXPORT FUNCTIONS
       ------------------------------------------------------------------------ */

    /**
     * Export CSV (streamed download)
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->toDateString());
        $endDate   = $request->input('end_date', Carbon::now()->toDateString());

        $rows = DB::table('orders')
            ->leftJoin('users', 'users.id', '=', 'orders.user_id')
            ->select('orders.id', 'orders.code', 'users.fullname', 'orders.status', 'orders.total_price', 'orders.created_at')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->orderByDesc('orders.created_at')
            ->get();

        $filename = 'orders_' . now()->format('Ymd_His') . '.csv';

        $response = new StreamedResponse(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Code', 'Customer', 'Status', 'Total Price', 'Created At']);
            foreach ($rows as $r) {
                fputcsv($handle, [
                    $r->id,
                    $r->code,
                    $r->fullname,
                    $r->status,
                    number_format($r->total_price, 2, '.', ''),
                    $r->created_at,
                ]);
            }
            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', "attachment; filename={$filename}");
        return $response;
    }

    /**
     * Export Excel (ưu tiên Excel, fallback PDF hoặc CSV nếu lỗi)
     */
    public function exportExcel(Request $request)
    {
        $orders = Order::all()->map(function ($o) {
            return [
                'ID' => $o->id,
                'Code' => $o->code,
                'Customer' => $o->customer_name,
                'Total' => $o->total,
                'Status' => $o->status,
                'Date' => $o->created_at->format('Y-m-d'),
            ];
        })->toArray();

        $summary = [
            ['Metric' => 'Tổng đơn', 'Value' => count($orders)],
            ['Metric' => 'Tổng tiền', 'Value' => array_sum(array_column($orders, 'Total'))],
            ['Metric' => 'Tăng trưởng đơn hàng', 'Value' => 15],
            ['Metric' => 'Giảm hoàn tiền', 'Value' => -3],
        ];

        $topProducts = Product::limit(10)->get()->map(function ($p) {
            return [
                'ID' => $p->id,
                'Name' => $p->name,
                'Price' => $p->price,
                'Stock' => $p->stock,
            ];
        })->toArray();

        return Excel::download(new DashboardExport($orders, $summary, $topProducts), 'dashboard_pro.xlsx');
    }

    /**
     * Export PDF (ưu tiên DomPDF, fallback CSV nếu lỗi)
     */
    public function exportPdf(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->toDateString());
        $endDate   = $request->input('end_date', Carbon::now()->toDateString());

        // 🔹 Lấy dữ liệu đơn hàng
        $orders = DB::table('orders')
            ->leftJoin('users', 'users.id', '=', 'orders.user_id')
            ->select(
                'orders.id',
                'orders.code',
                'users.fullname as customer',
                'orders.status',
                'orders.total_price',
                'orders.created_at'
            )
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->orderByDesc('orders.created_at')
            ->get();

        $filename = 'orders_' . now()->format('Ymd_His') . '.pdf';

        try {
            if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.dashboard.orders_pdf', [
                    'orders'    => $orders,
                    'startDate' => $startDate,
                    'endDate'   => $endDate,
                ])->setPaper('a4', 'landscape');

                return $pdf->download($filename);
            }
        } catch (\Throwable $e) {
            report($e);
        }

        // 🔹 fallback: CSV export nếu PDF lỗi
        return $this->exportCsv($request);
    }

    /* ------------------------------------------------------------------------
        🔍 HELPER QUERIES
       ------------------------------------------------------------------------ */

    private function getTotalRevenue(string $start, string $end): float
    {
        return (float) DB::table('order_details')
            ->join('orders', 'orders.id', '=', 'order_details.order_id')
            ->whereBetween('orders.created_at', [$start, $end])
            ->where('orders.status', 'completed')
            ->sum('order_details.subtotal');
    }

    private function getTotalOrders(string $start, string $end): int
    {
        return (int) DB::table('orders')
            ->whereBetween('orders.created_at', [$start, $end])
            ->count();
    }

    private function getMonthlyRevenue(int $year): array
    {
        $data = DB::table('order_details')
            ->join('orders', 'orders.id', '=', 'order_details.order_id')
            ->selectRaw('MONTH(orders.created_at) as month, SUM(order_details.subtotal) as revenue')
            ->whereYear('orders.created_at', $year)
            ->where('orders.status', 'completed')
            ->groupBy('month')
            ->pluck('revenue', 'month')
            ->toArray();

        return array_map(fn($m) => $data[$m] ?? 0, range(1, 12));
    }

    private function getMonthlyOrders(int $year): array
    {
        $data = DB::table('orders')
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        return array_map(fn($m) => $data[$m] ?? 0, range(1, 12));
    }

    private function getTopProducts(string $start, string $end)
    {
        return DB::table('order_details')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->join('orders', 'orders.id', '=', 'order_details.order_id')
            ->select('products.name', DB::raw('SUM(order_details.quantity) as sold'))
            ->whereBetween('orders.created_at', [$start, $end])
            ->where('orders.status', 'completed')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('sold')
            ->limit(10)
            ->get();
    }

    private function getRecentOrders()
    {
        return DB::table('orders')
            ->leftJoin('users', 'orders.user_id', '=', 'users.id')
            ->select('orders.id', 'orders.code', 'users.fullname', 'orders.total_price', 'orders.status', 'orders.created_at')
            ->orderByDesc('orders.created_at')
            ->limit(8)
            ->get();
    }

    private function getOrderStatusDistribution(string $start, string $end): array
    {
        $data = DB::table('orders')
            ->selectRaw('status, COUNT(*) as total')
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $statuses = ['pending', 'confirmed', 'shipping', 'completed', 'cancelled', 'failed', 'returned'];
        return array_map(fn($s) => $data[$s] ?? 0, $statuses);
    }

    private function getOrdersData(string $startDate, string $endDate): array
    {
        $orders = DB::table('orders')
            ->leftJoin('users', 'users.id', '=', 'orders.user_id')
            ->select(
                'orders.id',
                'orders.code',
                'users.fullname as customer',
                'orders.status',
                'orders.total_price',
                'orders.created_at'
            )
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->orderByDesc('orders.created_at')
            ->get();

        return $orders->map(function ($order) {
            return [
                'ID'          => $order->id,
                'Mã đơn'      => $order->code,
                'Khách hàng'  => $order->customer,
                'Trạng thái'  => ucfirst($order->status),
                'Tổng tiền'   => number_format($order->total_price, 0, ',', '.') . '₫',
                'Ngày tạo'    => Carbon::parse($order->created_at)->format('d/m/Y H:i'),
            ];
        })->toArray();
    }

    private function getSummaryData(string $startDate, string $endDate): array
    {
        $totalRevenue = DB::table('order_details')
            ->join('orders', 'orders.id', '=', 'order_details.order_id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.status', 'completed')
            ->sum('order_details.subtotal');

        $totalOrders = DB::table('orders')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $totalUsers = DB::table('users')->count();
        $totalProducts = DB::table('products')->count();

        return [
            'Doanh thu'      => number_format($totalRevenue, 0, ',', '.') . '₫',
            'Tổng đơn hàng'  => $totalOrders,
            'Tổng khách hàng'=> $totalUsers,
            'Tổng sản phẩm'  => $totalProducts,
        ];
    }

    private function getTopProductsData(string $startDate, string $endDate): array
    {
        $topProducts = DB::table('order_details')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->join('orders', 'orders.id', '=', 'order_details.order_id')
            ->select('products.name', DB::raw('SUM(order_details.quantity) as sold'))
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.status', 'completed')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('sold')
            ->limit(10)
            ->get();

        return $topProducts->map(function($p){
            return [
                'Tên sản phẩm' => $p->name,
                'Số lượng bán' => $p->sold,
            ];
        })->toArray();
    }

    /**
     * API – Doanh thu theo ngày cho biểu đồ Chart.js
     */
    public function salesTrend(Request $request)
    {
        $start = $request->query('start_date')
            ? Carbon::parse($request->query('start_date'))
            : Carbon::now()->startOfMonth();

        $end = $request->query('end_date')
            ? Carbon::parse($request->query('end_date'))
            : Carbon::now();

        $data = Order::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(total_price) as revenue')
        )
            ->where('status', 'completed')
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json(
            $data->map(function ($item) {
                return [
                    'date' => Carbon::parse($item->date)->format('d/m'),
                    'revenue' => (float)$item->revenue,
                ];
            })
        );
    }

}
