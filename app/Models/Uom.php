<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Uom extends Model
{
    use HasFactory;

    public const DIMENSION_COUNT = 'COUNT';
    public const DIMENSION_WEIGHT = 'WEIGHT';
    public const DIMENSION_VOLUME = 'VOLUME';

    protected $fillable = [
        'code',
        'name',
        'symbol',
        'dimension',
        'precision',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'precision' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function productUoms(): HasMany
    {
        return $this->hasMany(ProductUom::class, 'uom_id');
    }

    public function baseProducts(): HasMany
    {
        return $this->hasMany(Product::class, 'base_uom_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
