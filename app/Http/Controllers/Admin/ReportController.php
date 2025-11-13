<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Exports\ReportExport;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Class ReportController
 *
 * Báo cáo & export cho SneakerUp
 */
class ReportController extends Controller
{
    /**
     * Hiển thị trang báo cáo.
     */
    public function index(Request $request)
    {
        // ✅ Ép kiểu Carbon để tránh lỗi format()
        $start = $request->input('start_date')
            ? Carbon::parse($request->input('start_date'))
            : Carbon::now()->startOfMonth();

        $end = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'))
            : Carbon::now();

        // Tổng doanh thu (khoảng)
        $revenue = Order::whereBetween('created_at', [$start, $end])
            ->where('status', 'completed')
            ->sum('total_price');

        // Top sản phẩm bán chạy
        $topProducts = OrderItem::select(
            'order_items.product_id',
            DB::raw('SUM(order_items.quantity) as total_sold'),
            DB::raw('SUM(order_items.quantity * order_items.price) as revenue')
        )
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'completed')
            ->whereBetween('orders.created_at', [$start, $end])
            ->groupBy('order_items.product_id')
            ->with('product:id,name,base_price')
            ->orderByDesc('total_sold')
            ->limit(10)
            ->get();

        // Tồn kho: tổng stock từ variants (nếu variants tồn tại)
        $lowStockProducts = Product::query()
            ->select('products.*')
            ->with('variants')
            ->get()
            ->map(function ($p) {
                $stock = 0;
                if ($p->relationLoaded('variants') && $p->variants->count()) {
                    $stock = $p->variants->sum('stock');
                } else {
                    $stock = $p->stock ?? 0;
                }
                $p->computed_stock = $stock;
                return $p;
            })
            ->sortBy('computed_stock')
            ->take(10);

        // Doanh thu theo ngày (phục vụ biểu đồ)
        $dailyRevenue = Order::select(
            DB::raw('DATE(created_at) as label'),
            DB::raw('SUM(total_price) as total')
        )
            ->where('status', 'completed')
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('label')
            ->orderBy('label')
            ->get();

        // Thống kê tổng hợp
        $orderCount = Order::whereBetween('created_at', [$start, $end])->count();
        $customerCount = User::whereBetween('created_at', [$start, $end])->count();
        $productCount = Product::count();

        return view('admin.reports.index', compact(
            'start',
            'end',
            'revenue',
            'topProducts',
            'lowStockProducts',
            'dailyRevenue',
            'orderCount',
            'customerCount',
            'productCount'
        ));
    }


    /**
     * Xuất CSV (streamed) chứa orders trong khoảng
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $start = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $end = $request->input('end_date', Carbon::now()->toDateString());

        $orders = Order::with('user')->whereBetween('created_at', [$start, $end])->orderByDesc('created_at');

        $filename = 'sneakerup_orders_' . now()->format('Ymd_His') . '.csv';

        $response = new StreamedResponse(function () use ($orders) {
            $handle = fopen('php://output', 'w');
            // BOM for Excel compatibility in some clients
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($handle, ['ID', 'Code', 'Customer', 'Email', 'Total', 'Status', 'Created At']);
            $orders->chunk(200, function ($chunk) use ($handle) {
                foreach ($chunk as $order) {
                    fputcsv($handle, [
                        $order->id,
                        $order->code ?? '',
                        $order->user->fullname ?? '',
                        $order->user->email ?? '',
                        number_format($order->total_price, 2, '.', ''),
                        $order->status,
                        $order->created_at->toDateTimeString(),
                    ]);
                }
            });
            fclose($handle);
        });

        $disposition = "attachment; filename=\"{$filename}\"";
        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }

    /**
     * Xuất Excel bằng Maatwebsite\Excel (ReportExport)
     */
    public function exportExcel(Request $request)
    {
        $start = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $end = $request->input('end_date', Carbon::now()->toDateString());

        $export = new ReportExport($start, $end);

        return Excel::download($export, 'sneakerup_report_' . now()->format('Ymd_His') . '.xlsx');
    }

    /**
     * Xuất PDF báo cáo tóm tắt (DomPDF)
     */
    public function exportPdf(Request $request)
    {
        $start = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $end = $request->input('end_date', Carbon::now()->toDateString());

        // re-use index aggregation to populate PDF
        $revenue = Order::whereBetween('created_at', [$start, $end])->where('status', 'completed')->sum('total_price');

        $topProducts = OrderItem::select(
            'order_items.product_id',
            DB::raw('SUM(order_items.quantity) as total_sold'),
            DB::raw('SUM(order_items.quantity * order_items.price) as revenue')
        )
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'completed')
            ->whereBetween('orders.created_at', [$start, $end])
            ->groupBy('order_items.product_id')
            ->with('product:id,name,base_price')
            ->orderByDesc('total_sold')
            ->limit(10)
            ->get();

        $lowStockProducts = Product::with('variants')->get()->map(function ($p) {
            $stock = ($p->relationLoaded('variants') && $p->variants->count()) ? $p->variants->sum('stock') : ($p->stock ?? 0);
            $p->computed_stock = $stock;
            return $p;
        })->sortBy('computed_stock')->take(10);

        $pdf = Pdf::loadView('admin.reports.pdf', compact('start', 'end', 'revenue', 'topProducts', 'lowStockProducts'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('sneakerup_report_' . now()->format('Ymd_His') . '.pdf');
    }
}
