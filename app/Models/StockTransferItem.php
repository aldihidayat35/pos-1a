<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockTransferItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_transfer_id',
        'product_id',
        'product_uom_id',
        'requested_qty_input',
        'factor_snapshot',
        'requested_qty_base',
        'dispatched_qty_base',
        'received_qty_base',
        'cost_snapshot',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'requested_qty_input' => 'decimal:3',
            'factor_snapshot' => 'decimal:6',
            'requested_qty_base' => 'decimal:3',
            'dispatched_qty_base' => 'decimal:3',
            'received_qty_base' => 'decimal:3',
            'cost_snapshot' => 'decimal:6',
        ];
    }

    public function stockTransfer(): BelongsTo
    {
        return $this->belongsTo(StockTransfer::class, 'stock_transfer_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function productUom(): BelongsTo
    {
        return $this->belongsTo(ProductUom::class, 'product_uom_id');
    }

    public function getRemainingToReceiveBaseAttribute(): float
    {
        return max(0, round((float) $this->dispatched_qty_base - (float) $this->received_qty_base, 3));
    }
}
