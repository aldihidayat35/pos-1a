<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Location extends Model
{
    use HasFactory;

    public const TYPE_WAREHOUSE = 'WAREHOUSE';
    public const TYPE_STORE = 'STORE';
    public const TYPE_TRANSIT = 'TRANSIT';

    protected $fillable = [
        'code',
        'name',
        'type',
        'phone',
        'address',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_locations')
            ->withPivot('is_default')
            ->withTimestamps();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeWarehouse(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_WAREHOUSE);
    }

    public function scopeStore(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_STORE);
    }

    public function scopeTransit(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_TRANSIT);
    }

    public function isWarehouse(): bool
    {
        return $this->type === self::TYPE_WAREHOUSE;
    }

    public function isStore(): bool
    {
        return $this->type === self::TYPE_STORE;
    }

    public function isTransit(): bool
    {
        return $this->type === self::TYPE_TRANSIT;
    }
}
