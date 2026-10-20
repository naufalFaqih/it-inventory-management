<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\AssetMovement;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class MovementService
{
    /**
     * Record an asset movement atomically (BR-04, BR-08).
     */
    public function recordMovement(Asset $asset, array $data, User $actor): AssetMovement
    {
        return DB::transaction(function () use ($asset, $data, $actor) {
            $fromLocationId = $asset->location_id;
            $fromUserId = $asset->assigned_to;

            $movement = AssetMovement::create([
                'asset_id' => $asset->id,
                'from_location_id' => $fromLocationId,
                'to_location_id' => $data['to_location_id'],
                'from_user_id' => $fromUserId,
                'to_user_id' => $data['to_user_id'] ?? null,
                'moved_at' => $data['moved_at'] ?? now(),
                'reason' => $data['reason'],
                'created_by' => $actor->id,
            ]);

            // Update current location and assigned PIC on asset
            $asset->update([
                'location_id' => $data['to_location_id'],
                'assigned_to' => $data['to_user_id'] ?? null,
                'status' => ! empty($data['to_user_id']) ? Asset::STATUS_ASSIGNED : Asset::STATUS_AVAILABLE,
            ]);

            AuditLog::log('asset_movement', $asset, [
                'location_id' => $fromLocationId,
                'assigned_to' => $fromUserId,
            ], [
                'location_id' => $data['to_location_id'],
                'assigned_to' => $data['to_user_id'] ?? null,
                'reason' => $data['reason'],
            ], $actor->id);

            return $movement;
        });
    }
}
