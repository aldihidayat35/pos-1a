<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MemberTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'priority',
        'discount_percent',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'priority' => 'integer',
            'discount_percent' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function members(): HasMany
    {
        return $this->hasMany(Member::class, 'tier_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
