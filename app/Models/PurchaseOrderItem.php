<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'product_id',
        'product_uom_id',
        'quantity_ordered',
        'factor_to_base_snapshot',
        'unit_purchase_price',
        'line_total',
        'quantity_received_base',
    ];

    protected function casts(): array
    {
        return [
            'quantity_ordered' => 'decimal:3',
            'factor_to_base_snapshot' => 'decimal:6',
            'unit_purchase_price' => 'decimal:2',
            'line_total' => 'decimal:2',
            'quantity_received_base' => 'decimal:3',
        ];
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function productUom(): BelongsTo
    {
        return $this->belongsTo(ProductUom::class, 'product_uom_id');
    }

    /**
     * Total ordered in base unit
     */
    public function getOrderedBaseQtyAttribute(): float
    {
        return round((float) $this->quantity_ordered * (float) $this->factor_to_base_snapshot, 3);
    }

    /**
     * Remaining base quantity to be received
     */
    public function getRemainingBaseQtyAttribute(): float
    {
        return max(0, round($this->ordered_base_qty - (float) $this->quantity_received_base, 3));
    }

    /**
     * Remaining quantity in ordered UOM
     */
    public function getRemainingInputQtyAttribute(): float
    {
        $factor = (float) $this->factor_to_base_snapshot ?: 1.0;
        return round($this->remaining_base_qty / $factor, 3);
    }

    public function isFullyReceived(): bool
    {
        return $this->remaining_base_qty <= 0.0001;
    }
}
