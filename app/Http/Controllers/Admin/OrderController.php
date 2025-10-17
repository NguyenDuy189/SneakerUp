<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\ProductVariant;
use App\Models\Payment;
use App\Models\ActivityLog;
use App\Notifications\OrderStatusChanged;


class OrderController extends Controller
{
    /**
     * Hiển thị danh sách đơn hàng với tìm kiếm + bộ lọc
     */
    public function index(Request $request)
    {
        $query = Order::with(['user', 'payment', 'provider'])->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment')) {
            $query->where('payment_method', $request->payment);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($q2) use ($q) {
                $q2->where('code', 'like', "%$q%")
                    ->orWhere('fullname', 'like', "%$q%")
                    ->orWhere('phone', 'like', "%$q%");
            });
        }

        $orders = $query->paginate(15)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Xem chi tiết đơn hàng
     */
    public function show(Order $order)
    {
        $order->load(['orderDetails', 'user', 'payment', 'provider']);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Cập nhật trạng thái đơn hàng (Form)
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,shipping,completed,cancelled,failed'
        ]);

        $old = $order->status;
        $new = $request->status;

        // Quy tắc chuyển trạng thái hợp lệ
        $allowedTransitions = [
            'pending'   => ['confirmed', 'cancelled', 'failed'],
            'confirmed' => ['shipping', 'cancelled'],
            'shipping'  => ['completed', 'failed'],
            'completed' => [],
            'cancelled' => [],
            'failed'    => [],
        ];

        // Kiểm tra hợp lệ
        if (!in_array($new, $allowedTransitions[$old] ?? [])) {
            return back()->with('error', "Không thể chuyển từ trạng thái [$old] sang [$new].");
        }

        DB::transaction(function () use ($order, $old, $new) {
            $order->update(['status' => $new]);

            // Giảm stock khi xác nhận
            if ($old !== 'confirmed' && $new === 'confirmed') {
                foreach ($order->orderDetails as $detail) {
                    $variant = ProductVariant::find($detail->variant_id);
                    if ($variant) $variant->decrement('stock', $detail->quantity);
                }
            }

            // Gửi thông báo (nếu có)
            if ($order->user) {
                $order->user->notify(new OrderStatusChanged($order, $old, $new));
            }

            // Log hoạt động
            if (class_exists(ActivityLog::class)) {
                ActivityLog::create([
                    'user_id' => Auth::id() ?? 1,
                    'action' => 'update_status',
                    'model_type' => Order::class,
                    'model_id' => $order->id,
                    'description' => "Status: $old -> $new",
                    'ip' => request()->ip(),
                ]);
            }
        });

        return back()->with('success', "Cập nhật trạng thái [$new] thành công.");
    }
    /**
     * Thêm ghi chú vào đơn hàng
     */
    public function addNote(Request $request, Order $order)
    {
        $request->validate(['note' => 'required|string|max:1000']);

        $adminName = Auth::check() ? Auth::user()->username : 'Admin SneakerUp';
        $order->notes = ($order->notes ? $order->notes . "\n\n" : '') .
            "[{$adminName} @ " . now() . "] " . $request->note;
        $order->save();

        ActivityLog::create([
            'user_id' => Auth::id() ?? 1,
            'action' => 'add_note',
            'model_type' => Order::class,
            'model_id' => $order->id,
            'description' => "Added note",
            'ip' => request()->ip(),
        ]);

        return back()->with('success', 'Đã thêm ghi chú.');
    }

    /**
     * Xuất hóa đơn PDF
     */
    public function invoice(Order $order)
    {
        $order->load(['orderDetails', 'user', 'payment', 'provider']);
        $pdf = Pdf::loadView('admin.orders.invoice', compact('order'))->setPaper('a4', 'portrait');
        $fileName = 'invoice-' . $order->code . '.pdf';
        return $pdf->download($fileName);
    }

    /**
     * Cập nhật trạng thái đơn hàng qua AJAX
     */
    public function ajaxUpdateStatus(Request $request, Order $order)
    {
        $request->validate(['status' => 'required|in:pending,confirmed,shipping,completed,cancelled,failed']);
        
        $old = $order->status;
        $new = $request->status;

        // Quy tắc chuyển trạng thái
        $allowedTransitions = [
            'pending'   => ['confirmed', 'cancelled', 'failed'],
            'confirmed' => ['shipping', 'cancelled'],
            'shipping'  => ['completed', 'failed'],
            'completed' => [],
            'cancelled' => [],
            'failed'    => [],
        ];

        if (!in_array($new, $allowedTransitions[$old] ?? [])) {
            return response()->json([
                'success' => false,
                'message' => "Không thể chuyển từ [$old] sang [$new].",
            ]);
        }

        DB::transaction(function () use ($order, $old, $new) {
            $order->update(['status' => $new]);

            if ($old !== 'confirmed' && $new === 'confirmed') {
                foreach ($order->orderDetails as $detail) {
                    $variant = ProductVariant::find($detail->variant_id);
                    if ($variant) $variant->decrement('stock', $detail->quantity);
                }
            }

            ActivityLog::create([
                'user_id' => Auth::id() ?? 1,
                'action' => 'update_status_ajax',
                'model_type' => Order::class,
                'model_id' => $order->id,
                'description' => "AJAX Status: $old -> $new",
                'ip' => request()->ip(),
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => "Cập nhật trạng thái [$new] thành công.",
            'status' => $new
        ]);
    }
    /**
     * Xuất danh sách đơn hàng ra CSV
     */
    public function exportCsv()
    {
        $fileName = 'orders_export_' . date('Ymd_His') . '.csv';
        $orders = Order::with('user')->orderBy('created_at', 'desc')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$fileName}",
        ];

        $callback = function () use ($orders) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Code', 'Customer', 'Phone', 'Total', 'Status', 'Payment', 'Created At']);
            foreach ($orders as $o) {
                fputcsv($handle, [
                    $o->id,
                    $o->code,
                    $o->fullname,
                    $o->phone,
                    $o->total_price,
                    $o->status,
                    $o->payment_method,
                    $o->created_at
                ]);
            }
            fclose($handle);
        };

        return new StreamedResponse($callback, 200, $headers);
    }

    /**
     * Xuất Excel chuyên nghiệp bằng PhpSpreadsheet
     */
    public function exportExcel()
    {
        $orders = \App\Models\Order::with('user')->orderBy('created_at', 'desc')->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Order List');

        // ==== Header ====
        $headings = ['ID', 'Code', 'Customer', 'Phone', 'Total', 'Status', 'Payment', 'Created At'];
        $colLetter = 'A';
        foreach ($headings as $heading) {
            $sheet->setCellValue($colLetter . '1', $heading);
            $colLetter++;
        }

        // ==== Style Header ====
        $headerStyle = [
            'font' => ['bold' => true, 'size' => 12],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFEFEFEF'],
            ],
        ];
        $sheet->getStyle('A1:H1')->applyFromArray($headerStyle);

        // ==== Ghi dữ liệu ====
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

        $lastRow = $rowNum - 1;

        // ==== Style dữ liệu ====
        $dataStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_HAIR,
                ],
            ],
            'alignment' => ['vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
        ];
        $sheet->getStyle("A2:H{$lastRow}")->applyFromArray($dataStyle);

        // Format tiền
        $sheet->getStyle("E2:E{$lastRow}")
            ->getNumberFormat()
            ->setFormatCode('#,##0 "₫"');

        // Căn giữa các cột trạng thái, thanh toán
        $sheet->getStyle("F2:G{$lastRow}")
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // ==== Auto width ====
        foreach (range('A', 'H') as $colID) {
            $sheet->getColumnDimension($colID)->setAutoSize(true);
        }

        // ==== Ghi chú cuối file ====
        $footerRow = $lastRow + 2;
        $sheet->setCellValue("A{$footerRow}", 'Exported at: ' . now()->format('Y-m-d H:i:s'));
        $sheet->setCellValue("F{$footerRow}", 'Generated by: SneakerUp Admin');

        $sheet->mergeCells("A{$footerRow}:C{$footerRow}");
        $sheet->mergeCells("F{$footerRow}:H{$footerRow}");

        $sheet->getStyle("A{$footerRow}:H{$footerRow}")->applyFromArray([
            'font' => ['italic' => true, 'color' => ['argb' => 'FF777777']],
        ]);

        // ==== Xuất file ====
        $fileName = 'orders_' . date('Ymd_His') . '.xlsx';
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
