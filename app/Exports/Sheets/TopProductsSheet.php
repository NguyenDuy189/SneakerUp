<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class TopProductsSheet implements FromArray, WithHeadings, WithEvents
{
    private array $products;

    public function __construct(array $products)
    {
        $this->products = $products;
    }

    public function array(): array
    {
        return $this->products;
    }

    public function headings(): array
    {
        return array_keys($this->products[0] ?? []);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $highestCol = $sheet->getHighestColumn();

                // Gradient header
                $sheet->getStyle('A1:' . $highestCol . '1')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                    'fill' => [
                        'fillType' => Fill::FILL_GRADIENT_LINEAR,
                        'rotation' => 0,
                        'startColor' => ['argb' => 'FFFF9800'],
                        'endColor' => ['argb' => 'FFFFB74D'],
                    ],
                ]);

                // Borders + autofit
                foreach (range('A', $highestCol) as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                    $sheet->getStyle($col . '1:' . $col . $highestRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                }

                // Highlight stock < 10
                for ($row = 2; $row <= $highestRow; $row++) {
                    $stock = $sheet->getCell("D$row")->getValue() ?? 0;
                    if ($stock < 10) {
                        $sheet->getStyle("A$row:D$row")->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB('FFFFCDD2'); // light red
                    }
                }
            }
        ];
    }
}
