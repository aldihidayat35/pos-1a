<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class B2bOrder extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'DRAFT';
    public const STATUS_PLACED = 'PLACED';
    public const STATUS_RESERVED = 'RESERVED';
    public const STATUS_READY_FOR_PICKUP = 'READY_FOR_PICKUP';
    public const STATUS_HANDED_OVER = 'HANDED_OVER';
    public const STATUS_CANCELLED = 'CANCELLED';

    public const TERM_CASH = 'CASH';
    public const TERM_NET_7 = 'NET_7';
    public const TERM_NET_14 = 'NET_14';
    public const TERM_NET_30 = 'NET_30';

    protected $fillable = [
        'order_number',
        'member_id',
        'creator_user_id',
        'fulfillment_location_id',
        'payment_term',
        'due_date',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'total_amount',
        'status',
        'notes',
        'placed_at',
        'ready_at',
        'handed_over_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'due_date' => 'date',
            'placed_at' => 'datetime',
            'ready_at' => 'datetime',
            'handed_over_at' => 'datetime',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_user_id');
    }

    public function fulfillmentLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'fulfillment_location_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(B2bOrderItem::class, 'b2b_order_id');
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(StockReservation::class, 'b2b_order_id');
    }

    public function pickupTicket(): HasOne
    {
        return $this->hasOne(PickupTicket::class, 'b2b_order_id');
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class, 'b2b_order_id');
    }

    public function isCreditTerm(): bool
    {
        return in_array($this->payment_term, [self::TERM_NET_7, self::TERM_NET_14, self::TERM_NET_30], true);
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_HANDED_OVER;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_PLACED, self::STATUS_RESERVED, self::STATUS_READY_FOR_PICKUP], true);
    }
}
