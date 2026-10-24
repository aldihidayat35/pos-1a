<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    use HasFactory;

    public const STATUS_COMPLETED = 'COMPLETED';
    public const STATUS_VOIDED = 'VOIDED';

    protected $fillable = [
        'sale_number',
        'idempotency_key',
        'location_id',
        'cash_shift_id',
        'cashier_user_id',
        'member_id',
        'customer_name',
        'status',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'total_amount',
        'cogs_total',
        'payment_method',
        'payment_status',
        'paid_amount',
        'change_amount',
        'posted_at',
        'voided_at',
        'voided_by',
        'void_reason',
    ];

    protected function casts(): array
    {
        return [
            'posted_at' => 'datetime',
            'voided_at' => 'datetime',
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'cogs_total' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'change_amount' => 'decimal:2',
        ];
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function cashShift(): BelongsTo
    {
        return $this->belongsTo(CashShift::class, 'cash_shift_id');
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_user_id');
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function voidedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'voided_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class, 'sale_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'sale_id');
    }

    public function isPosted(): bool
    {
        return $this->status === 'POSTED';
    }

    public function isVoided(): bool
    {
        return $this->status === 'VOIDED';
    }

    public function getGrossProfitAttribute(): float
    {
        return (float) $this->total_amount - (float) $this->cogs_total;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'POSTED' => 'Berhasil Dibukukan',
            'VOIDED' => 'Dibatalkan (Void)',
            default => $this->status,
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'POSTED' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'VOIDED' => 'bg-rose-50 text-rose-700 border-rose-200',
            default => 'bg-gray-50 text-gray-700 border-gray-200',
        };
    }
}
