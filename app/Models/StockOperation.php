<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockOperation extends Model
{
    use HasFactory;

    protected $fillable = [
        'operation_uuid',
        'operation_type',
        'document_type',
        'document_id',
        'document_number',
        'location_id',
        'actor_user_id',
        'posted_at',
        'notes',
        'idempotency_key',
    ];

    protected function casts(): array
    {
        return [
            'posted_at' => 'datetime',
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

    public function movements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'operation_id');
    }
}
