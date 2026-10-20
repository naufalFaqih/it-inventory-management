<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'action',
        'entity_type',
        'entity_id',
        'old_values',
        'new_values',
        'ip_address',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function log(string $action, Model $entity, ?array $oldValues = null, ?array $newValues = null, ?int $userId = null): self
    {
        // Sanitize sensitive keys
        $sensitiveKeys = ['password', 'remember_token', 'token', 'secret'];
        if ($oldValues) {
            foreach ($sensitiveKeys as $key) {
                unset($oldValues[$key]);
            }
        }
        if ($newValues) {
            foreach ($sensitiveKeys as $key) {
                unset($newValues[$key]);
            }
        }

        return self::create([
            'user_id' => $userId ?? auth()->id(),
            'action' => $action,
            'entity_type' => get_class($entity),
            'entity_id' => $entity->getKey(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()?->ip(),
            'created_at' => now(),
        ]);
    }
}
