<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Maatwebsite\Excel\Facades\Excel; // nếu dùng package
use Barryvdh\DomPDF\Facade\Pdf;       // nếu dùng DOMPDF (barryvdh/laravel-dompdf)

class DashboardController extends Controller
{
    /**
     * Show advanced admin dashboard
     */
    public function index(Request $request)
    {
        // default date range: last 30 days
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->toDateString());
        $endDate   = $request->input('end_date', Carbon::now()->toDateString());

        // summary for cards (filtered by date range)
        $summary = [
            'totalRevenue'  => $this->getTotalRevenue($startDate, $endDate),
            'totalOrders'   => $this->getTotalOrders($startDate, $endDate),
            'totalUsers'    => DB::table('users')->count(),
            'totalProducts' => DB::table('products')->count(),
        ];

        // monthly totals for charts (current year)
        $year = Carbon::now()->year;
        $monthlyRevenue = $this->getMonthlyRevenue($year);
        $monthlyOrders  = $this->getMonthlyOrders($year);

        // top products and recent orders (filtered)
        $topProducts  = $this->getTopProducts($startDate, $endDate);
        $recentOrders = $this->getRecentOrders();

        // statuses for pie chart
        $statusDistribution = $this->getOrderStatusDistribution($startDate, $endDate);

        return view('admin.dashboard.index', compact(
            'summary',
            'monthlyRevenue',
            'monthlyOrders',
            'topProducts',
            'recentOrders',
            'statusDistribution',
            'startDate',
            'endDate'
        ));
    }

    /**
     * AJAX endpoint — trả về JSON cho charts khi lọc thời gian
     */
    public function data(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->toDateString());
        $endDate   = $request->input('end_date', Carbon::now()->toDateString());
        $year      = $request->input('year', Carbon::now()->year);

        $payload = [
            'summary' => [
                'totalRevenue' => $this->getTotalRevenue($startDate, $endDate),
                'totalOrders' => $this->getTotalOrders($startDate, $endDate),
            ],
            'monthlyRevenue' => $this->getMonthlyRevenue((int)$year),
            'monthlyOrders' => $this->getMonthlyOrders((int)$year),
            'topProducts' => $this->getTopProducts($startDate, $endDate),
            'statusDistribution' => $this->getOrderStatusDistribution($startDate, $endDate),
        ];

        return response()->json($payload);
    }

    /**
     * Export CSV (streamed)
     */
    public function exportCsv(Request $request)
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
            // header
            fputcsv($handle, ['ID', 'Code', 'Customer', 'Status', 'Total Price', 'Created At']);
            foreach ($rows as $r) {
                fputcsv($handle, [
                    $r->id,
                    $r->code,
                    $r->fullname,
                    $r->status,
                    number_format($r->total_price, 2, '.', ''),
                    $r->created_at
                ]);
            }
            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', "attachment; filename={$filename}");
        return $response;
    }

    /**
     * Export Excel (requires maatwebsite/excel) - returns download
     * Simple implementation: export orders as array
     */
    public function exportExcel(Request $request)
    {
        // If you installed maatwebsite/excel, you could implement a proper Export class.
        // For brevity, do CSV download but with xlsx extension if package absent.
        return $this->exportCsv($request); // fallback to CSV stream
    }

    /**
     * Export PDF (requires barryvdh/laravel-dompdf)
     */
    public function exportPdf(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->toDateString());
        $endDate   = $request->input('end_date', Carbon::now()->toDateString());

        $orders = DB::table('orders')
            ->leftJoin('users', 'users.id', '=', 'orders.user_id')
            ->select('orders.*', 'users.fullname')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->orderByDesc('orders.created_at')
            ->get();

        try {
    if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
                $pdf = Pdf::loadView('admin.reports.orders_pdf', compact('orders', 'startDate', 'endDate'));
                return $pdf->download('orders_' . now()->format('Ymd_His') . '.pdf');
            }
        } catch (\Throwable $e) {
            report($e);
        }

        // fallback CSV nếu có lỗi
        return $this->exportCsv($request);
    }

    /* ----------------- Helpers / Queries ----------------- */

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

        // normalize common statuses order
        $statuses = ['pending', 'confirmed', 'shipping', 'completed', 'cancelled', 'failed', 'returned'];
        return array_map(fn($s) => $data[$s] ?? 0, $statuses);
    }
}
