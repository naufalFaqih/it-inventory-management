<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_AVAILABLE = 'available';
    public const STATUS_ASSIGNED = 'assigned';
    public const STATUS_BORROWED = 'borrowed';
    public const STATUS_MAINTENANCE = 'maintenance';
    public const STATUS_RETIRED = 'retired';

    public const CONDITION_GOOD = 'good';
    public const CONDITION_MINOR_DAMAGE = 'minor_damage';
    public const CONDITION_MAJOR_DAMAGE = 'major_damage';

    public const STATUSES = [
        self::STATUS_AVAILABLE => 'Tersedia',
        self::STATUS_ASSIGNED => 'Ditetapkan (Assigned)',
        self::STATUS_BORROWED => 'Dipinjam',
        self::STATUS_MAINTENANCE => 'Dalam Perbaikan',
        self::STATUS_RETIRED => 'Dinonaktifkan (Retired)',
    ];

    public const CONDITIONS = [
        self::CONDITION_GOOD => 'Baik',
        self::CONDITION_MINOR_DAMAGE => 'Rusak Ringan',
        self::CONDITION_MAJOR_DAMAGE => 'Rusak Berat',
    ];

    protected $fillable = [
        'asset_tag',
        'name',
        'category_id',
        'brand',
        'model',
        'serial_number',
        'location_id',
        'assigned_to',
        'condition',
        'status',
        'purchase_date',
        'purchase_cost',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'purchase_date' => 'date',
            'purchase_cost' => 'decimal:2',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function movements(): HasMany
    {
        return $this->hasMany(AssetMovement::class)->orderBy('moved_at', 'desc');
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class)->orderBy('borrowed_at', 'desc');
    }

    public function activeLoan(): HasMany
    {
        return $this->hasMany(Loan::class)->where('status', 'active');
    }

    public function maintenanceRecords(): HasMany
    {
        return $this->hasMany(MaintenanceRecord::class)->orderBy('started_at', 'desc');
    }

    public function activeMaintenance(): HasMany
    {
        return $this->hasMany(MaintenanceRecord::class)->where('status', 'in_progress');
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_AVAILABLE);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('asset_tag', 'like', "%{$term}%")
                ->orWhere('name', 'like', "%{$term}%")
                ->orWhere('brand', 'like', "%{$term}%")
                ->orWhere('model', 'like', "%{$term}%")
                ->orWhere('serial_number', 'like', "%{$term}%");
        });
    }

    public function scopeFilterByCategory(Builder $query, $categoryId): Builder
    {
        if (blank($categoryId)) {
            return $query;
        }

        return $query->where('category_id', $categoryId);
    }

    public function scopeFilterByLocation(Builder $query, $locationId): Builder
    {
        if (blank($locationId)) {
            return $query;
        }

        return $query->where('location_id', $locationId);
    }

    public function scopeFilterByStatus(Builder $query, $status): Builder
    {
        if (blank($status)) {
            return $query;
        }

        return $query->where('status', $status);
    }

    public function hasTransactionHistory(): bool
    {
        return $this->movements()->exists()
            || $this->loans()->exists()
            || $this->maintenanceRecords()->exists();
    }
}
