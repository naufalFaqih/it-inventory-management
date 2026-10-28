<?php

namespace App\Exports;

use App\Services\ReportService;
use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class AssetsReportExport implements Export, WithMultipleSheets
{
    public function __construct(
        public readonly array $filters,
        private readonly ReportService $reportService,
    ) {}

    public function sheets(): array
    {
        return [
            new AssetsSheet($this->filters, $this->reportService),
            new ReportSummarySheet($this->filters, $this->reportService),
        ];
    }
}
