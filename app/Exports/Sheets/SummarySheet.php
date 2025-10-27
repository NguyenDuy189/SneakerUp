<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;

class SummarySheet implements FromArray, WithHeadings, WithEvents
{
    private array $summary;

    public function __construct(array $summary)
    {
        $this->summary = $summary;
    }

    public function array(): array
    {
        return $this->summary;
    }

    public function headings(): array
    {
        return array_keys($this->summary[0] ?? []);
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
                        'startColor' => ['argb' => 'FF1976D2'],
                        'endColor' => ['argb' => 'FF64B5F6'],
                    ],
                ]);

                // Borders + autofit
                foreach (range('A', $highestCol) as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                    $sheet->getStyle($col . '1:' . $col . $highestRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                }

                // Conditional formatting KPI
                for ($row = 2; $row <= $highestRow; $row++) {
                    $value = $sheet->getCell("B$row")->getValue() ?? 0;
                    if (is_numeric($value)) {
                        if ($value > 0) {
                            $sheet->getStyle("B$row")->applyFromArray([
                                'font' => ['color' => ['argb' => Color::COLOR_DARKGREEN], 'bold' => true],
                                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFC8E6C9']],
                            ]);
                            $sheet->setCellValue("B$row", $value . " ✅");
                        } elseif ($value < 0) {
                            $sheet->getStyle("B$row")->applyFromArray([
                                'font' => ['color' => ['argb' => Color::COLOR_DARKRED], 'bold' => true],
                                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFFCDD2']],
                            ]);
                            $sheet->setCellValue("B$row", $value . " ❌");
                        } else {
                            $sheet->getStyle("B$row")->getFont()->setBold(true);
                        }
                    }
                }
            }
        ];
    }
}
