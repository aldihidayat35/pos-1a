<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'location_id',
        'channel',
        'tier_id',
        'list_price',
        'market_min_price',
        'market_max_price',
        'effective_from',
        'effective_to',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'list_price' => 'decimal:2',
            'market_min_price' => 'decimal:2',
            'market_max_price' => 'decimal:2',
            'effective_from' => 'date',
            'effective_to' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function tier(): BelongsTo
    {
        return $this->belongsTo(MemberTier::class, 'tier_id');
    }
}
