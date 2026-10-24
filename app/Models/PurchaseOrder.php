<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'po_number',
        'supplier_id',
        'destination_location_id',
        'status',
        'ordered_at',
        'expected_delivery_at',
        'subtotal',
        'tax_amount',
        'grand_total',
        'notes',
        'created_by',
        'approved_by',
    ];

    protected function casts(): array
    {
        return [
            'ordered_at' => 'datetime',
            'expected_delivery_at' => 'date',
            'subtotal' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'grand_total' => 'decimal:2',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function destinationLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'destination_location_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class, 'purchase_order_id');
    }

    public function goodsReceipts(): HasMany
    {
        return $this->hasMany(GoodsReceipt::class, 'purchase_order_id');
    }

    public function isDraft(): bool
    {
        return $this->status === 'DRAFT';
    }

    public function isIssued(): bool
    {
        return $this->status === 'ISSUED';
    }

    public function isPartiallyReceived(): bool
    {
        return $this->status === 'PARTIALLY_RECEIVED';
    }

    public function isReceived(): bool
    {
        return $this->status === 'RECEIVED';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'DRAFT' => 'Draft',
            'ISSUED' => 'Diterbitkan / On Order',
            'PARTIALLY_RECEIVED' => 'Diterima Sebagian',
            'RECEIVED' => 'Lengkap Diterima',
            'CLOSED' => 'Selesai',
            'CANCELLED' => 'Dibatalkan',
            default => $this->status,
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'DRAFT' => 'bg-gray-100 text-gray-700 border-gray-200',
            'ISSUED' => 'bg-blue-50 text-blue-700 border-blue-200',
            'PARTIALLY_RECEIVED' => 'bg-amber-50 text-amber-700 border-amber-200',
            'RECEIVED' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'CLOSED' => 'bg-purple-50 text-purple-700 border-purple-200',
            'CANCELLED' => 'bg-rose-50 text-rose-700 border-rose-200',
            default => 'bg-gray-100 text-gray-700 border-gray-200',
        };
    }
}
