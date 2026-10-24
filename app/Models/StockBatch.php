<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_id',
        'product_id',
        'batch_number',
        'initial_qty',
        'current_qty',
        'unit_cost',
        'received_date',
        'expiry_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'initial_qty' => 'decimal:3',
            'current_qty' => 'decimal:3',
            'unit_cost' => 'decimal:6',
            'received_date' => 'date',
            'expiry_date' => 'date',
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

    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }
}
