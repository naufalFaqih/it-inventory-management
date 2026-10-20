<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;

class AssetService
{
    /**
     * Generate next unique asset tag (AST-YYYY-XXXX).
     * Must be unique even among soft-deleted/retired assets (BR-01).
     */
    public function generateAssetTag(): string
    {
        $year = date('Y');
        $prefix = "AST-{$year}-";

        // Query including trashed assets
        $lastAsset = Asset::withTrashed()
            ->where('asset_tag', 'like', "{$prefix}%")
            ->orderBy('asset_tag', 'desc')
            ->first();

        if ($lastAsset) {
            $lastNumber = (int) substr($lastAsset->asset_tag, strlen($prefix));
            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }

        $candidate = "{$prefix}{$nextNumber}";

        // Ensure safety check in loop
        $counter = (int) $nextNumber;
        while (Asset::withTrashed()->where('asset_tag', $candidate)->exists()) {
            $counter++;
            $candidate = "{$prefix}" . str_pad($counter, 4, '0', STR_PAD_LEFT);
        }

        return $candidate;
    }

    /**
     * Retire an asset safely preserving history.
     */
    public function retire(Asset $asset, ?string $reason = null): Asset
    {
        return DB::transaction(function () use ($asset, $reason) {
            $oldValues = [
                'status' => $asset->status,
                'assigned_to' => $asset->assigned_to,
                'notes' => $asset->notes,
            ];

            $notes = $asset->notes;
            if ($reason) {
                $notes = ($notes ? $notes . "\n" : '') . "[Retired " . now()->format('Y-m-d') . "]: " . $reason;
            }

            $asset->update([
                'status' => Asset::STATUS_RETIRED,
                'assigned_to' => null,
                'notes' => $notes,
            ]);

            AuditLog::log('retire_asset', $asset, $oldValues, [
                'status' => Asset::STATUS_RETIRED,
                'reason' => $reason,
            ]);

            return $asset;
        });
    }
}
