<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\ProductVariant;
use App\Models\Payment;
use App\Models\ActivityLog;
use App\Models\User;
use App\Notifications\OrderStatusChanged;

class OrderController extends Controller
{
    // ------------------ DANH SÁCH ------------------
    public function index(Request $request)
    {
        $query = Order::with(['user', 'payment', 'provider', 'staff'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('payment')) $query->where('payment_method', $request->payment);
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($query) use ($q) {
                $query->where('code', 'like', "%$q%")
                    ->orWhere('placed_name', 'like', "%$q%")
                    ->orWhere('placed_phone', 'like', "%$q%")
                    ->orWhere('receiver_name', 'like', "%$q%")
                    ->orWhere('receiver_phone', 'like', "%$q%")
                    ->orWhereHas('user', fn($q2) => $q2->where('fullname', 'like', "%$q%"));
            });
        }

        $orders = $query->paginate(15)->withQueryString();
        return view('admin.orders.index', compact('orders'));
    }

    // ------------------ CHI TIẾT ------------------
    public function show($id)
    {
        $order = Order::with(['orderDetails.variant.product', 'payment', 'provider', 'user', 'staff'])
            ->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    // ------------------ CẬP NHẬT TRẠNG THÁI ------------------
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,shipping,completed,cancelled,failed,returned'
        ]);

        $old = $order->status;
        $new = $request->status;

        $allowedTransitions = [
            'pending'   => ['confirmed', 'cancelled', 'failed'],
            'confirmed' => ['shipping', 'cancelled'],
            'shipping'  => ['completed', 'failed', 'returned'],
            'completed' => [],
            'cancelled' => [],
            'failed'    => [],
            'returned'  => [],
        ];

        if (!in_array($new, $allowedTransitions[$old] ?? [])) {
            return back()->with('error', "Không thể chuyển từ [$old] sang [$new].");
        }

        try {
            DB::transaction(function () use ($order, $old, $new) {
                // ✅ Xác nhận đơn: set staff
                if ($new === 'confirmed' && !$order->staff_id) {
                    $order->staff_id = Auth::id();
                    $order->confirm_by = Auth::user()->fullname ?? Auth::user()->username ?? 'Nhân viên không xác định';
                }

                $order->status = $new;
                $order->save();

                // ✅ Khi xác nhận đơn: trừ kho
                if ($old === 'pending' && $new === 'confirmed') {
                    foreach ($order->orderDetails as $detail) {
                        $variant = ProductVariant::find($detail->variant_id);
                        if ($variant) $variant->decrement('stock', $detail->quantity);
                    }
                }

                // ✅ Khi đơn bị hủy hoặc trả hàng: xử lý thanh toán
                if (in_array($new, ['cancelled', 'returned']) && $order->payment) {
                    if ($order->payment->status === 'paid') {
                        $order->payment->update(['status' => 'refunded']);
                    }
                }

                // ✅ Khi đơn trả hàng: trừ điểm thưởng hoặc cộng lại (nếu cần)
                if ($new === 'returned' && $order->user) {
                    $points = round($order->total_price / 1000);
                    $order->user->decrement('points', $points);
                }

                // ✅ Gửi thông báo
                if ($order->user) {
                    $order->user->notify(new OrderStatusChanged($order, $old, $new));
                }

                // ✅ Ghi log
                if (class_exists(ActivityLog::class)) {
                    ActivityLog::create([
                        'user_id'     => Auth::id() ?? 1,
                        'action'      => 'update_status',
                        'model_type'  => Order::class,
                        'model_id'    => $order->id,
                        'description' => "Cập nhật trạng thái: $old → $new",
                        'ip'          => request()->ip(),
                    ]);
                }
            });

            return back()->with('success', "Đã cập nhật trạng thái [$new] thành công.");
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    // ------------------ CẬP NHẬT TRẠNG THÁI (AJAX) ------------------
    public function ajaxUpdateStatus(Request $request, Order $order)
    {
        // Nếu chưa đăng nhập (bị hết session)
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.'
            ], 401);
        }

        $request->validate([
            'status' => 'required|in:pending,confirmed,shipping,completed,cancelled,failed,returned'
        ]);

        $old = $order->status;
        $new = $request->status;

        // Kiểm tra allowed transitions (giống updateStatus)
        $allowedTransitions = [
            'pending'   => ['confirmed', 'cancelled', 'failed'],
            'confirmed' => ['shipping', 'cancelled'],
            'shipping'  => ['completed', 'failed', 'returned'],
            'completed' => [],
            'cancelled' => [],
            'failed'    => [],
            'returned'  => [],
        ];

        if (!in_array($new, $allowedTransitions[$old] ?? [])) {
            return response()->json([
                'success' => false,
                'message' => "Không thể chuyển từ [$old] sang [$new]."
            ]);
        }

        try {
            DB::transaction(function () use ($order, $old, $new) {
                // ✅ Chỉ set staff khi chuyển sang trạng thái "confirmed"
                if ($new === 'confirmed' && !$order->staff_id) {
                    $order->staff_id = Auth::id();
                    $order->confirm_by = Auth::user()->fullname ?? Auth::user()->username ?? 'Nhân viên không xác định';
                }

                $order->status = $new;
                $order->save();

                // ✅ Khi xác nhận đơn thì trừ kho
                if ($old === 'pending' && $new === 'confirmed') {
                    foreach ($order->orderDetails as $detail) {
                        $variant = ProductVariant::find($detail->variant_id);
                        if ($variant) $variant->decrement('stock', $detail->quantity);
                    }
                }

                // ✅ Khi trả hàng
                if ($new === 'returned') {
                    if ($order->payment && $order->payment->status === 'paid') {
                        $order->payment->update(['status' => 'refunded']);
                    } elseif ($order->user) {
                        $points = round($order->total_price / 1000);
                        $order->user->increment('points', $points);
                    }
                }

                // ✅ Gửi thông báo
                if ($order->user) {
                    $order->user->notify(new OrderStatusChanged($order, $old, $new));
                }

                // ✅ Ghi log
                if (class_exists(ActivityLog::class)) {
                    ActivityLog::create([
                        'user_id'     => Auth::id() ?? 1,
                        'action'      => 'update_status_ajax',
                        'model_type'  => Order::class,
                        'model_id'    => $order->id,
                        'description' => "AJAX Status: $old → $new",
                        'ip'          => request()->ip(),
                    ]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => "Cập nhật trạng thái [$new] thành công."
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi server: ' . $e->getMessage()
            ], 500);
        }
    }


    // ------------------ GHI CHÚ NỘI BỘ ------------------
    public function addNote(Request $request, Order $order)
    {
        $request->validate([
            'note' => 'nullable|string|max:2000',
        ]);

        $order->note = $request->input('note');
        $order->save();

        if (class_exists(ActivityLog::class)) {
            ActivityLog::create([
                'user_id' => Auth::id() ?? 1,
                'action' => 'add_internal_note',
                'model_type' => Order::class,
                'model_id' => $order->id,
                'description' => 'Cập nhật ghi chú nội bộ',
                'ip' => request()->ip(),
            ]);
        }

        return back()->with('success', 'Đã lưu ghi chú nội bộ.');
    }

    // ------------------ XUẤT PDF HOÁ ĐƠN ------------------
    public function invoice(Order $order)
    {
        $order->load(['orderDetails.variant.product', 'user', 'payment']);
        $pdf = Pdf::loadView('admin.orders.invoice', compact('order'));
        $fileName = 'invoice_' . $order->code . '.pdf';
        return $pdf->download($fileName);
    }

    // ------------------ XUẤT CSV ------------------
    public function exportCsv()
    {
        $fileName = 'orders_export_'.date('Ymd_His').'.csv';
        $orders = Order::with('user')->orderBy('created_at','desc')->get();
        $headers = ['Content-Type'=>'text/csv','Content-Disposition'=>"attachment; filename={$fileName}"];
        $callback = function() use($orders){
            $handle = fopen('php://output','w');
            fputcsv($handle,['ID','Code','Customer','Phone','Total','Status','Payment','Created At']);
            foreach($orders as $o){
                fputcsv($handle,[
                    $o->id,
                    $o->code,
                    $o->fullname ?? ($o->user->fullname ?? ''),
                    $o->phone,
                    $o->total_price,
                    $o->status,
                    $o->payment_method,
                    $o->created_at
                ]);
            }
            fclose($handle);
        };
        return new StreamedResponse($callback,200,$headers);
    }

    // ------------------ XUẤT EXCEL ------------------
    public function exportExcel()
    {
        $orders = Order::with('user')->orderBy('created_at', 'desc')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Order List');

        $headings = ['ID', 'Code', 'Customer', 'Phone', 'Total', 'Status', 'Payment', 'Created At'];
        $colLetter = 'A';
        foreach ($headings as $heading) {
            $sheet->setCellValue($colLetter . '1', $heading);
            $colLetter++;
        }

        $rowNum = 2;
        foreach ($orders as $o) {
            $sheet->setCellValue("A{$rowNum}", $o->id);
            $sheet->setCellValue("B{$rowNum}", $o->code);
            $sheet->setCellValue("C{$rowNum}", $o->fullname ?? ($o->user->fullname ?? ''));
            $sheet->setCellValue("D{$rowNum}", $o->phone);
            $sheet->setCellValue("E{$rowNum}", $o->total_price);
            $sheet->setCellValue("F{$rowNum}", ucfirst($o->status));
            $sheet->setCellValue("G{$rowNum}", $o->payment_method);
            $sheet->setCellValue("H{$rowNum}", $o->created_at ? $o->created_at->format('Y-m-d H:i:s') : '');
            $rowNum++;
        }

        foreach (range('A', 'H') as $colID) {
            $sheet->getColumnDimension($colID)->setAutoSize(true);
        }

        $fileName = 'orders_' . date('Ymd_His') . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
