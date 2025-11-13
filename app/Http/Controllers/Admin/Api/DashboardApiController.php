<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DashboardApiController extends Controller
{
    /** Tổng quan (Cards) */
    public function stats(Request $request)
    {
        $start = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : now()->subDays(30)->startOfDay();
        $end   = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : now()->endOfDay();

        $ordersQuery = Order::whereBetween('created_at', [$start, $end]);

        return response()->json([
            'data' => [
                'revenue' => (float)$ordersQuery->sum('total_price'),
                'orders' => (int)$ordersQuery->count(),
                'active_customers' => (int)User::whereHas('orders', fn($q) => $q->whereBetween('created_at', [$start, $end]))->count()
            ]
        ]);
    }

    /** Doanh thu & đơn hàng theo ngày */
    public function salesTrend(Request $request)
    {
        try {
            $start = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : now()->subDays(30)->startOfDay();
            $end   = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : now()->endOfDay();

            $query = Order::query()->whereBetween('created_at', [$start, $end]);

            if ($request->filled('customer_id')) $query->where('user_id', $request->customer_id);
            if ($request->filled('category_id')) {
                $query->whereHas('items.product', fn($q) => $q->where('category_id', $request->category_id));
            }

            $data = $query
                ->selectRaw('DATE(created_at) as date, SUM(total_price) as revenue, COUNT(id) as orders')
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->keyBy('date');

            $period = new \DatePeriod($start, new \DateInterval('P1D'), $end->addDay());

            $result = collect($period)->map(fn($day) => [
                'date' => $day->format('Y-m-d'),
                'revenue' => (float)($data->get($day->format('Y-m-d'))->revenue ?? 0),
                'orders' => (int)($data->get($day->format('Y-m-d'))->orders ?? 0),
            ]);

            return response()->json(['data' => $result]);

        } catch (\Exception $e) {
            Log::error('SalesTrend Error: '.$e->getMessage(), ['stack'=>$e->getTraceAsString()]);
            return response()->json(['error'=>'Có lỗi khi lấy dữ liệu.'], 500);
        }
    }

    /** Top sản phẩm */
    public function productAnalysis(Request $request)
    {
        try {
            $start = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : now()->subDays(30)->startOfDay();
            $end   = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : now()->endOfDay();

            $query = OrderItem::with('product:id,name')
                ->whereHas('order', fn($q) => $q->whereBetween('created_at', [$start, $end]));

            if ($request->filled('customer_id')) $query->whereHas('order', fn($q) => $q->where('user_id', $request->customer_id));
            if ($request->filled('category_id')) $query->whereHas('product', fn($q) => $q->where('category_id', $request->category_id));

            $data = $query->select(
                'product_id',
                DB::raw('SUM(quantity) as total_sold'),
                DB::raw('SUM(price * quantity) as total_revenue')
            )
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->limit(10)
            ->get();

            $result = $data->map(fn($item) => [
                'product_name' => $item->product->name ?? 'Không xác định',
                'total_sold' => (int)($item->total_sold ?? 0),
                'total_revenue' => (float)($item->total_revenue ?? 0),
            ]);

            return response()->json(['data'=>$result]);

        } catch (\Exception $e) {
            Log::error('ProductAnalysis Error: '.$e->getMessage());
            return response()->json(['error'=>'Có lỗi khi lấy dữ liệu sản phẩm.'], 500);
        }
    }

    /** Top khách hàng */
    public function topCustomers(Request $request)
    {
        $start = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : now()->subMonths(6)->startOfDay();
        $end   = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : now()->endOfDay();

        $users = User::withCount(['orders as total_orders'=>fn($q)=>$q->whereBetween('created_at', [$start, $end])])
                        ->withSum(['orders as total_spent'=>fn($q)=>$q->whereBetween('created_at', [$start,$end])], 'total_price')
                        ->orderByDesc('total_spent')
                        ->limit($request->limit ?? 10)
                        ->get();

        $result = $users->map(fn($u)=>[
            'id'=>$u->id,
            'name'=>$u->name,
            'total_orders'=>(int)($u->total_orders ?? 0),
            'total_spent'=>(float)($u->total_spent ?? 0)
        ]);

        return response()->json(['data'=>$result]);
    }
}
