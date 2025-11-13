<?php

namespace App\Exports;

use App\Models\Order;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Class ReportExport
 * Xuất 1 file Excel chứa: Orders, TopProducts, Summary (tách sheet)
 */
class ReportExport implements FromCollection, WithHeadings
{
    private string $start;
    private string $end;

    public function __construct(string $start, string $end)
    {
        $this->start = $start;
        $this->end = $end;
    }

    /**
     * For simplicity we export orders list. For multi-sheet export implement WithMultipleSheets.
     */
    public function collection()
    {
        $orders = Order::with('user')
            ->whereBetween('created_at', [$this->start, $this->end])
            ->orderByDesc('created_at')
            ->get();

        return $orders->map(function ($o) {
            return collect([
                'ID' => $o->id,
                'Code' => $o->code,
                'Customer' => $o->user->fullname ?? '',
                'Email' => $o->user->email ?? '',
                'Total' => $o->total_price,
                'Status' => $o->status,
                'Created At' => $o->created_at->toDateTimeString(),
            ]);
        })->values();
    }

    public function headings(): array
    {
        return ['ID', 'Code', 'Customer', 'Email', 'Total', 'Status', 'Created At'];
    }

    /**
     * Smart Analytics v2 page
     */
    public function smartAnalytics()
    {
        // You can pass initial data if needed
        return view('admin.reports.smart_analytics_v2');
    }
}
