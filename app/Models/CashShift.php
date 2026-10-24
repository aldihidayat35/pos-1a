<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashShift extends Model
{
    use HasFactory;

    public const STATUS_OPEN = 'OPEN';
    public const STATUS_CLOSED = 'CLOSED';
    public const STATUS_VALIDATED = 'VALIDATED';

    protected $fillable = [
        'shift_number',
        'location_id',
        'cashier_user_id',
        'status',
        'opened_at',
        'closed_at',
        'starting_cash',
        'expected_cash',
        'actual_cash',
        'difference',
        'notes',
        'validated_by',
        'validated_at',
    ];

    protected function casts(): array
    {
        return [
            'opened_at' => 'datetime',
            'closed_at' => 'datetime',
            'validated_at' => 'datetime',
            'starting_cash' => 'decimal:2',
            'expected_cash' => 'decimal:2',
            'actual_cash' => 'decimal:2',
            'difference' => 'decimal:2',
        ];
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_user_id');
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class, 'cash_shift_id');
    }

    public function isOpen(): bool
    {
        return $this->status === 'OPEN';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'OPEN' => 'Shift Aktif',
            'CLOSED' => 'Tutup (Menunggu Validasi)',
            'VALIDATED' => 'Tervalidasi',
            default => $this->status,
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'OPEN' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'CLOSED' => 'bg-amber-50 text-amber-700 border-amber-200',
            'VALIDATED' => 'bg-blue-50 text-blue-700 border-blue-200',
            default => 'bg-gray-50 text-gray-700 border-gray-200',
        };
    }
}
