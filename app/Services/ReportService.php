<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ReportService
{
    /**
     * Build the filtered asset query used by both the report page and its exports.
     */
    public function filteredQuery(array $filters): Builder
    {
        return Asset::query()
            ->with(['category', 'location', 'assignedUser'])
            ->filterByCategory($filters['category_id'] ?? null)
            ->filterByLocation($filters['location_id'] ?? null)
            ->filterByStatus($filters['status'] ?? null)
            ->when(
                filled($filters['start_date'] ?? null),
                fn (Builder $query) => $query->whereDate('purchase_date', '>=', $filters['start_date'])
            )
            ->when(
                filled($filters['end_date'] ?? null),
                fn (Builder $query) => $query->whereDate('purchase_date', '<=', $filters['end_date'])
            )
            ->orderByDesc('purchase_date')
            ->orderBy('asset_tag');
    }

    /**
     * Aggregate the filtered assets for the report summary cards.
     *
     * @return array{totalCount: int, totalValue: float, averageValue: float, statusBreakdown: Collection}
     */
    public function summary(array $filters): array
    {
        $query = $this->filteredQuery($filters);

        $totalCount = (clone $query)->count();
        $totalValue = (float) (clone $query)->sum('purchase_cost');

        $statusBreakdown = (clone $query)
            ->reorder()
            ->selectRaw('status, count(*) as count, sum(purchase_cost) as total_cost')
            ->groupBy('status')
            ->get();

        return [
            'totalCount' => $totalCount,
            'totalValue' => $totalValue,
            'averageValue' => $totalCount > 0 ? $totalValue / $totalCount : 0,
            'statusBreakdown' => $statusBreakdown,
        ];
    }

    /**
     * Human readable labels for the active filters, used in exported files.
     *
     * @return array<string, string>
     */
    public function filterLabels(array $filters): array
    {
        $startDate = $filters['start_date'] ?? null;
        $endDate = $filters['end_date'] ?? null;

        if (filled($startDate) && filled($endDate)) {
            $purchaseDate = $this->formatDate($startDate).' s/d '.$this->formatDate($endDate);
        } elseif (filled($startDate)) {
            $purchaseDate = 'Sejak '.$this->formatDate($startDate);
        } elseif (filled($endDate)) {
            $purchaseDate = 'Sampai '.$this->formatDate($endDate);
        } else {
            $purchaseDate = 'Semua Tanggal';
        }

        return [
            'Kategori' => filled($filters['category_id'] ?? null)
                ? (Category::whereKey($filters['category_id'])->value('name') ?? '-')
                : 'Semua Kategori',
            'Lokasi' => filled($filters['location_id'] ?? null)
                ? (Location::whereKey($filters['location_id'])->value('name') ?? '-')
                : 'Semua Lokasi',
            'Status' => filled($filters['status'] ?? null)
                ? (Asset::STATUSES[$filters['status']] ?? $filters['status'])
                : 'Semua Status',
            'Tanggal Beli' => $purchaseDate,
        ];
    }

    private function formatDate(string $date): string
    {
        return Carbon::parse($date)->translatedFormat('d M Y');
    }
}
