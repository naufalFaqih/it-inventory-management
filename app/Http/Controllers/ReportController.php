<?php

namespace App\Http\Controllers;

use App\Exports\AssetsReportExport;
use App\Http\Requests\ReportFilterRequest;
use App\Models\Category;
use App\Models\Location;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;

class ReportController extends Controller
{
    public function __construct(private readonly ReportService $reportService) {}

    public function index(ReportFilterRequest $request): View
    {
        $filters = $request->validated();
        $summary = $this->reportService->summary($filters);

        $assets = $this->reportService
            ->filteredQuery($filters)
            ->paginate(20)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();

        return view('reports.index', [
            'assets' => $assets,
            'categories' => $categories,
            'locations' => $locations,
            'totalCount' => $summary['totalCount'],
            'totalValue' => $summary['totalValue'],
            'averageValue' => $summary['averageValue'],
            'statusBreakdown' => $summary['statusBreakdown'],
        ]);
    }

    public function exportPdf(ReportFilterRequest $request): Response
    {
        $filters = $request->validated();
        $summary = $this->reportService->summary($filters);

        $pdf = Pdf::loadView('reports.pdf', [
            'assets' => $this->reportService->filteredQuery($filters)->get(),
            'totalCount' => $summary['totalCount'],
            'totalValue' => $summary['totalValue'],
            'averageValue' => $summary['averageValue'],
            'statusBreakdown' => $summary['statusBreakdown'],
            'filterLabels' => $this->reportService->filterLabels($filters),
            'generatedAt' => now(),
            'generatedBy' => $request->user()?->name ?? '-',
        ])->setPaper('a4', 'landscape');

        return $pdf->download($this->exportFilename('pdf'));
    }

    public function exportExcel(ReportFilterRequest $request): Response
    {
        $export = new AssetsReportExport($request->validated(), $this->reportService);

        return Excel::download($export, $this->exportFilename('xlsx'));
    }

    private function exportFilename(string $extension): string
    {
        return 'laporan-aset-'.now()->format('Ymd-His').'.'.$extension;
    }
}
