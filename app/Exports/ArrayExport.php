<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OrdersExport implements FromArray, WithHeadings, WithStyles, WithEvents
{
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        return $this->data;
    }

    public function headings(): array
    {
        return array_keys($this->data[0] ?? []);
    }

    // Tô đậm header
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]], // hàng đầu tiên
        ];
    }

    // Sự kiện sau khi tạo sheet
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Autofit các cột
                foreach (range('A', $sheet->getHighestColumn()) as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                // Filter cho header
                $sheet->setAutoFilter($sheet->calculateWorksheetDimension());

                // Màu nền cho header
                $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()->setARGB('FFCCE5FF');
            },
        ];
    }
}
