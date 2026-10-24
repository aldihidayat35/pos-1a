<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_id',
        'product_id',
        'on_hand_qty',
        'reserved_qty',
        'avg_unit_cost',
        'version',
    ];

    protected function casts(): array
    {
        return [
            'on_hand_qty' => 'decimal:3',
            'reserved_qty' => 'decimal:3',
            'avg_unit_cost' => 'decimal:6',
            'version' => 'integer',
        ];
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
     * Physical available quantity: on_hand - reserved
     */
    public function getAvailableQtyAttribute(): float
    {
        return max(0, (float) $this->on_hand_qty - (float) $this->reserved_qty);
    }
}
