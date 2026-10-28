<?php

namespace App\Exports;

use App\Models\Asset;
use App\Services\ReportService;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Columns\Date;
use Maatwebsite\Excel\Columns\Price;
use Maatwebsite\Excel\Columns\Text;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumns;
use Maatwebsite\Excel\Concerns\WithFreezePane;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AssetsSheet implements FromQuery, ShouldAutoSize, WithColumns, WithFreezePane, WithStyles, WithTitle
{
    public function __construct(
        private readonly array $filters,
        private readonly ReportService $reportService,
    ) {}

    public function title(): string
    {
        return 'Data Aset';
    }

    public function query(): Builder
    {
        return $this->reportService->filteredQuery($this->filters);
    }

    public function freezePane(): string
    {
        return 'A2';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => '3730A3']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'EEF2FF'],
                ],
            ],
        ];
    }

    public function columns(): array
    {
        return [
            Text::make('Asset Tag', 'asset_tag'),
            Text::make('Nama Perangkat', 'name'),
            Text::make('Merek', 'brand')->nullable(),
            Text::make('Model', 'model')->nullable(),
            Text::make('Serial Number', 'serial_number')->nullable(),
            Text::make('Kategori', 'category.name')->nullable(),
            Text::make('Lokasi', 'location.name')->nullable(),
            Text::make('PIC', fn (Asset $asset) => $asset->assignedUser?->name ?? 'Tanpa PIC'),
            Text::make('Status', fn (Asset $asset) => Asset::STATUSES[$asset->status] ?? $asset->status),
            Text::make('Kondisi', fn (Asset $asset) => Asset::CONDITIONS[$asset->condition] ?? $asset->condition),
            Date::make('Tanggal Pembelian', 'purchase_date')->nullable(),
            Price::make('Biaya Perolehan', 'purchase_cost')->currency('"Rp" #,##0')->nullable(),
            Text::make('Catatan', 'notes')->nullable(),
        ];
    }
}
