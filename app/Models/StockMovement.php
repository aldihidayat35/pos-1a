<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'operation_id',
        'location_id',
        'product_id',
        'delta_qty_base',
        'unit_cost_snapshot',
        'amount_value',
        'balance_after_qty',
        'movement_type',
        'semantic_status',
        'occurred_at',
    ];

    protected function casts(): array
    {
        return [
            'delta_qty_base' => 'decimal:3',
            'unit_cost_snapshot' => 'decimal:6',
            'amount_value' => 'decimal:2',
            'balance_after_qty' => 'decimal:3',
            'occurred_at' => 'datetime',
        ];
    }

    public function operation(): BelongsTo
    {
        return $this->belongsTo(StockOperation::class, 'operation_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Semantic badge color info based on PRD v2 5-Color Movement Stream
     */
    public function getSemanticColorAttribute(): string
    {
        return match ($this->semantic_status) {
            'GREEN' => '#10B981',
            'BLUE' => '#3B82F6',
            'PURPLE' => '#8B5CF6',
            'ORANGE' => '#F59E0B',
            'RED' => '#EF4444',
            default => '#6B7280',
        };
    }

    public function getSemanticLabelAttribute(): string
    {
        return match ($this->semantic_status) {
            'GREEN' => 'Masuk / Inbound PO',
            'BLUE' => 'Internal / Transfer',
            'PURPLE' => 'Keluar / Penjualan',
            'ORANGE' => 'Peringatan / Emergency',
            'RED' => 'Kritis / Selisih Opname',
            default => 'Lainnya',
        };
    }
}
