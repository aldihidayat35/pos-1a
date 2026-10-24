<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PricingPolicy extends Model
{
    use HasFactory;

    protected $fillable = [
        'scope',
        'location_id',
        'channel',
        'min_markup_type',
        'min_markup_value',
        'effective_from',
        'effective_to',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'min_markup_value' => 'decimal:2',
            'effective_from' => 'date',
            'effective_to' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    /**
     * Calculate nominal minimum markup over base unit cost
     */
    public function calculateMarkupAmount(float $baseCost): float
    {
        if ($this->min_markup_type === 'PERCENTAGE') {
            return round($baseCost * ((float) $this->min_markup_value / 100), 2);
        }

        return (float) $this->min_markup_value;
    }
}
