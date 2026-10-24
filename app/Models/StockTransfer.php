<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'transfer_number',
        'from_location_id',
        'to_location_id',
        'transit_location_id',
        'status',
        'notes',
        'dispatched_at',
        'received_at',
        'created_by',
        'received_by',
    ];

    protected function casts(): array
    {
        return [
            'dispatched_at' => 'datetime',
            'received_at' => 'datetime',
        ];
    }

    public function fromLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'from_location_id');
    }

    public function toLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'to_location_id');
    }

    public function transitLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'transit_location_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(StockTransferItem::class, 'stock_transfer_id');
    }

    public function isDraft(): bool
    {
        return $this->status === 'DRAFT';
    }

    public function isDispatched(): bool
    {
        return $this->status === 'DISPATCHED';
    }

    public function isReceived(): bool
    {
        return $this->status === 'RECEIVED';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'DRAFT' => 'Draft',
            'DISPATCHED' => 'Dalam Perjalanan (Transit)',
            'PARTIALLY_RECEIVED' => 'Diterima Sebagian',
            'RECEIVED' => 'Lengkap Diterima',
            'CANCELLED' => 'Dibatalkan',
            default => $this->status,
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'DRAFT' => 'bg-gray-100 text-gray-700 border-gray-200',
            'DISPATCHED' => 'bg-blue-50 text-blue-700 border-blue-200',
            'PARTIALLY_RECEIVED' => 'bg-amber-50 text-amber-700 border-amber-200',
            'RECEIVED' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'CANCELLED' => 'bg-rose-50 text-rose-700 border-rose-200',
            default => 'bg-gray-100 text-gray-700 border-gray-200',
        };
    }
}
