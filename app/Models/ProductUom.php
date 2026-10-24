<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductUom extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'uom_id',
        'factor_to_base',
        'barcode',
        'is_purchase',
        'is_sale',
        'is_base',
    ];

    protected function casts(): array
    {
        return [
            'factor_to_base' => 'decimal:6',
            'is_purchase' => 'boolean',
            'is_sale' => 'boolean',
            'is_base' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function uom(): BelongsTo
    {
        return $this->belongsTo(Uom::class, 'uom_id');
    }
}
