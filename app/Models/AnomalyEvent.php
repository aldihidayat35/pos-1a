<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnomalyEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'severity',
        'kind',
        'location_id',
        'actor_user_id',
        'reference_type',
        'reference_id',
        'reference_number',
        'amount',
        'title',
        'description',
        'occurred_at',
        'acknowledged_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'occurred_at' => 'datetime',
            'acknowledged_at' => 'datetime',
        ];
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }

    public function getSeverityColorAttribute(): string
    {
        return match ($this->severity) {
            'RED' => '#EF4444',
            'ORANGE' => '#F59E0B',
            default => '#3B82F6',
        };
    }

    public function getSeverityBadgeClassAttribute(): string
    {
        return match ($this->severity) {
            'RED' => 'bg-rose-50 text-rose-700 border-rose-200',
            'ORANGE' => 'bg-amber-50 text-amber-700 border-amber-200',
            default => 'bg-blue-50 text-blue-700 border-blue-200',
        };
    }
}
