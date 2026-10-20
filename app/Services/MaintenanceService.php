<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\AuditLog;
use App\Models\MaintenanceRecord;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MaintenanceService
{
    /**
     * Start maintenance for an asset (FR-029, FR-031).
     */
    public function start(Asset $asset, array $data, User $actor): MaintenanceRecord
    {
        if ($asset->status === Asset::STATUS_BORROWED) {
            throw ValidationException::withMessages([
                'asset_id' => 'Aset sedang dipinjam dan harus dikembalikan sebelum dilakukan pemeliharaan.',
            ]);
        }

        if ($asset->status === Asset::STATUS_MAINTENANCE) {
            throw ValidationException::withMessages([
                'asset_id' => 'Aset sudah berada dalam status pemeliharaan/perbaikan aktif.',
            ]);
        }

        if ($asset->status === Asset::STATUS_RETIRED) {
            throw ValidationException::withMessages([
                'asset_id' => 'Aset telah dinonaktifkan (retired).',
            ]);
        }

        return DB::transaction(function () use ($asset, $data, $actor) {
            $record = MaintenanceRecord::create([
                'asset_id' => $asset->id,
                'technician_id' => $data['technician_id'],
                'issue' => $data['issue'],
                'action_taken' => $data['action_taken'] ?? null,
                'cost' => $data['cost'] ?? null,
                'started_at' => $data['started_at'] ?? now(),
                'status' => MaintenanceRecord::STATUS_IN_PROGRESS,
                'notes' => $data['notes'] ?? null,
                'created_by' => $actor->id,
            ]);

            $asset->update([
                'status' => Asset::STATUS_MAINTENANCE,
            ]);

            AuditLog::log('start_maintenance', $asset, [
                'status' => $asset->getOriginal('status'),
            ], [
                'status' => Asset::STATUS_MAINTENANCE,
                'maintenance_id' => $record->id,
                'technician_id' => $data['technician_id'],
            ], $actor->id);

            return $record;
        });
    }

    /**
     * Complete maintenance (FR-032).
     */
    public function complete(MaintenanceRecord $record, array $data, User $actor): MaintenanceRecord
    {
        if ($record->status !== MaintenanceRecord::STATUS_IN_PROGRESS) {
            throw ValidationException::withMessages([
                'status' => 'Hanya pekerjaan maintenance yang berstatus sedang dikerjakan yang dapat diselesaikan.',
            ]);
        }

        return DB::transaction(function () use ($record, $data, $actor) {
            $conditionAfter = $data['condition_after'] ?? Asset::CONDITION_GOOD;

            $record->update([
                'completed_at' => $data['completed_at'] ?? now(),
                'action_taken' => $data['action_taken'],
                'cost' => $data['cost'] ?? $record->cost,
                'status' => MaintenanceRecord::STATUS_COMPLETED,
                'condition_after' => $conditionAfter,
                'notes' => ! empty($data['notes']) ? ($record->notes ? $record->notes . "\n" : '') . 'Hasil: ' . $data['notes'] : $record->notes,
            ]);

            $asset = $record->asset;
            $nextStatus = $asset->assigned_to ? Asset::STATUS_ASSIGNED : Asset::STATUS_AVAILABLE;

            $asset->update([
                'status' => $nextStatus,
                'condition' => $conditionAfter,
            ]);

            AuditLog::log('complete_maintenance', $asset, [
                'status' => Asset::STATUS_MAINTENANCE,
            ], [
                'status' => $nextStatus,
                'condition' => $conditionAfter,
                'maintenance_id' => $record->id,
            ], $actor->id);

            return $record;
        });
    }
}
