<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class B2bOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'b2b_order_id',
        'product_id',
        'product_uom_id',
        'quantity_input',
        'quantity_base',
        'unit_price',
        'cogs_snapshot',
        'discount_percent',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'quantity_input' => 'decimal:3',
            'quantity_base' => 'decimal:3',
            'unit_price' => 'decimal:2',
            'cogs_snapshot' => 'decimal:6',
            'discount_percent' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(B2bOrder::class, 'b2b_order_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function productUom(): BelongsTo
    {
        return $this->belongsTo(ProductUom::class, 'product_uom_id');
    }

    public function reservation(): HasOne
    {
        return $this->hasOne(StockReservation::class, 'b2b_order_item_id');
    }
}
