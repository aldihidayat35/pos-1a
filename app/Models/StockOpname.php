<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockOpname extends Model
{
    use HasFactory;

    public const STATUS_OPEN = 'OPEN';
    public const STATUS_COUNTING = 'COUNTING';
    public const STATUS_RECONCILIATION = 'RECONCILIATION';
    public const STATUS_FINALIZED = 'FINALIZED';
    public const STATUS_CANCELLED = 'CANCELLED';

    public const SCOPE_ALL = 'ALL';
    public const SCOPE_CATEGORY = 'CATEGORY';
    public const SCOPE_CUSTOM = 'CUSTOM';

    protected $fillable = [
        'opname_number',
        'location_id',
        'scope_type',
        'category_id',
        'status',
        'opened_by_user_id',
        'finalized_by_user_id',
        'total_items_counted',
        'total_items_discrepancy',
        'total_variance_value',
        'total_loss_value',
        'notes',
        'started_at',
        'reconciled_at',
        'finalized_at',
    ];

    protected function casts(): array
    {
        return [
            'total_items_counted' => 'integer',
            'total_items_discrepancy' => 'integer',
            'total_variance_value' => 'decimal:2',
            'total_loss_value' => 'decimal:2',
            'started_at' => 'datetime',
            'reconciled_at' => 'datetime',
            'finalized_at' => 'datetime',
        ];
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function openedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'opened_by_user_id');
    }

    public function finalizedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'finalized_by_user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(StockOpnameItem::class, 'stock_opname_id');
    }

    public function isOpen(): bool
    {
        return in_array($this->status, [self::STATUS_OPEN, self::STATUS_COUNTING]);
    }

    public function isReconciliation(): bool
    {
        return $this->status === self::STATUS_RECONCILIATION;
    }

    public function isFinalized(): bool
    {
        return $this->status === self::STATUS_FINALIZED;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            self::STATUS_OPEN => [
                'label' => 'Terbuka / Siap Hitung',
                'class' => 'bg-blue-50 text-blue-700 border-blue-200',
            ],
            self::STATUS_COUNTING => [
                'label' => 'Sedang Hitung (Blind)',
                'class' => 'bg-amber-50 text-amber-700 border-amber-200',
            ],
            self::STATUS_RECONCILIATION => [
                'label' => 'Rekonsiliasi Selisih',
                'class' => 'bg-purple-50 text-purple-700 border-purple-200',
            ],
            self::STATUS_FINALIZED => [
                'label' => 'Final / Terposting',
                'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            ],
            self::STATUS_CANCELLED => [
                'label' => 'Dibatalkan',
                'class' => 'bg-gray-100 text-gray-500 border-gray-200',
            ],
            default => [
                'label' => $this->status,
                'class' => 'bg-gray-100 text-gray-700 border-gray-200',
            ],
        };
    }
}
