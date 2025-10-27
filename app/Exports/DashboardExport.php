<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Sheets\OrdersSheet;
use App\Exports\Sheets\SummarySheet;
use App\Exports\Sheets\TopProductsSheet;

class DashboardExport implements WithMultipleSheets
{
    private array $orders;
    private array $summary;
    private array $topProducts;

    public function __construct(array $orders, array $summary, array $topProducts)
    {
        $this->orders = $orders;
        $this->summary = $summary;
        $this->topProducts = $topProducts;
    }

    public function sheets(): array
    {
        return [
            new OrdersSheet($this->orders),
            new SummarySheet($this->summary),
            new TopProductsSheet($this->topProducts),
        ];
    }
}
