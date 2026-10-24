<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'product_id',
        'product_uom_id',
        'quantity_input',
        'factor_snapshot',
        'quantity_base',
        'unit_price',
        'floor_price_snapshot',
        'unit_cost_snapshot',
        'subtotal',
        'cogs_amount',
    ];

    protected function casts(): array
    {
        return [
            'quantity_input' => 'decimal:3',
            'factor_snapshot' => 'decimal:6',
            'quantity_base' => 'decimal:3',
            'unit_price' => 'decimal:2',
            'floor_price_snapshot' => 'decimal:2',
            'unit_cost_snapshot' => 'decimal:6',
            'subtotal' => 'decimal:2',
            'cogs_amount' => 'decimal:2',
        ];
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class, 'sale_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function productUom(): BelongsTo
    {
        return $this->belongsTo(ProductUom::class, 'product_uom_id');
    }
}
