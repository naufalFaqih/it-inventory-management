<?php

namespace App\Exports;

use App\Models\Asset;
use App\Services\ReportService;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportSummarySheet implements FromArray, ShouldAutoSize, WithColumnFormatting, WithStyles, WithTitle
{
    private ?array $summaryCache = null;

    public function __construct(
        private readonly array $filters,
        private readonly ReportService $reportService,
    ) {}

    public function title(): string
    {
        return 'Ringkasan';
    }

    public function array(): array
    {
        $summary = $this->summary();
        $filterLabels = $this->reportService->filterLabels($this->filters);

        $rows = [
            ['Laporan Inventaris & Nilai Aset'],
            ['Dicetak', now()->translatedFormat('d F Y H:i')],
            ['Oleh', auth()->user()?->name ?? '-'],
            [' '],
            ['Filter Aktif', 'Nilai'],
        ];

        foreach ($filterLabels as $label => $value) {
            $rows[] = [$label, $value];
        }

        $rows[] = [' '];
        $rows[] = ['Ringkasan Nilai'];
        $rows[] = ['Jumlah Aset Terfilter', $summary['totalCount']];
        $rows[] = ['Total Nilai Perolehan', $summary['totalValue']];
        $rows[] = ['Rata-rata Nilai per Unit', round($summary['averageValue'], 2)];
        $rows[] = [' '];
        $rows[] = ['Breakdown Status'];
        $rows[] = ['Status', 'Jumlah Aset', 'Total Nilai'];

        foreach ($summary['statusBreakdown'] as $row) {
            $rows[] = [
                Asset::STATUSES[$row->status] ?? $row->status,
                (int) $row->count,
                (float) $row->total_cost,
            ];
        }

        $rows[] = ['TOTAL', $summary['totalCount'], $summary['totalValue']];

        return $rows;
    }

    public function columnFormats(): array
    {
        $totalRow = $this->totalRow();

        return [
            'B12:B14' => '#,##0',
            "C18:C{$totalRow}" => '"Rp" #,##0',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '3730A3']]],
            5 => ['font' => ['bold' => true]],
            11 => ['font' => ['bold' => true]],
            16 => ['font' => ['bold' => true]],
            17 => [
                'font' => ['bold' => true, 'color' => ['rgb' => '3730A3']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'EEF2FF'],
                ],
            ],
            $this->totalRow() => ['font' => ['bold' => true]],
        ];
    }

    private function totalRow(): int
    {
        return 18 + $this->summary()['statusBreakdown']->count();
    }

    private function summary(): array
    {
        return $this->summaryCache ??= $this->reportService->summary($this->filters);
    }
}
