<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockReservation extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE = 'ACTIVE';
    public const STATUS_FULFILLED = 'FULFILLED';
    public const STATUS_RELEASED = 'RELEASED';

    protected $fillable = [
        'b2b_order_id',
        'b2b_order_item_id',
        'location_id',
        'product_id',
        'reserved_qty_base',
        'status',
        'reserved_at',
        'released_at',
    ];

    protected function casts(): array
    {
        return [
            'reserved_qty_base' => 'decimal:3',
            'reserved_at' => 'datetime',
            'released_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(B2bOrder::class, 'b2b_order_id');
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(B2bOrderItem::class, 'b2b_order_item_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }
}
