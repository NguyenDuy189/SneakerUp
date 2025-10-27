<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Border;

class OrdersSheet implements FromArray, WithHeadings, WithEvents
{
    private array $orders;

    public function __construct(array $orders)
    {
        $this->orders = $orders;
    }

    public function array(): array
    {
        return $this->orders;
    }

    public function headings(): array
    {
        return array_keys($this->orders[0] ?? []);
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
                    'font' => ['bold' => true, 'color' => ['argb' => Color::COLOR_WHITE]],
                    'fill' => [
                        'fillType' => Fill::FILL_GRADIENT_LINEAR,
                        'rotation' => 0,
                        'startColor' => ['argb' => 'FF4CAF50'],
                        'endColor' => ['argb' => 'FF81C784'],
                    ],
                ]);

                // Autofit columns + border
                foreach (range('A', $highestCol) as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                    $sheet->getStyle($col . '1:' . $col . $highestRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                }

                // Conditional formatting status
                for ($row = 2; $row <= $highestRow; $row++) {
                    $status = strtolower($sheet->getCell("E$row")->getValue() ?? '');
                    if ($status === 'completed') {
                        $sheet->getStyle("A$row:F$row")->getFont()->getColor()->setARGB(Color::COLOR_GREEN);
                        $sheet->setCellValue("E$row", $sheet->getCell("E$row")->getValue() . " ✅");
                    } elseif ($status === 'cancelled') {
                        $sheet->getStyle("A$row:F$row")->getFont()->getColor()->setARGB(Color::COLOR_RED);
                        $sheet->setCellValue("E$row", $sheet->getCell("E$row")->getValue() . " ❌");
                    } elseif ($status === 'pending') {
                        $sheet->getStyle("A$row:F$row")->getFont()->getColor()->setARGB('FFFF8800');
                    }
                }

                // Tổng cộng cột tiền
                $sheet->setCellValue("E" . ($highestRow + 1), '=SUM(E2:E' . $highestRow . ')');
                $sheet->setCellValue("D" . ($highestRow + 1), 'Tổng cộng');
                $sheet->getStyle("D" . ($highestRow + 1) . ":E" . ($highestRow + 1))->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFFF59D']],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
            }
        ];
    }
}
