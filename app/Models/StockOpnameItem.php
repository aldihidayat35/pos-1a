<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockOpnameItem extends Model
{
    use HasFactory;

    public const REASON_NONE = 'NONE';
    public const REASON_NATURAL_SHRINK = 'NATURAL_SHRINK';
    public const REASON_DAMAGED_EXPIRED = 'DAMAGED_EXPIRED';
    public const REASON_LOST_THEFT = 'LOST_THEFT';
    public const REASON_INPUT_ERROR = 'INPUT_ERROR';
    public const REASON_FOUND_EXTRA = 'FOUND_EXTRA';

    protected $fillable = [
        'stock_opname_id',
        'product_id',
        'product_uom_id',
        'system_qty_snapshot',
        'physical_qty_counted',
        'variance_qty',
        'unit_cost_snapshot',
        'variance_value',
        'reason_code',
        'notes',
        'counter_user_id',
        'counted_at',
    ];

    protected function casts(): array
    {
        return [
            'system_qty_snapshot' => 'decimal:3',
            'physical_qty_counted' => 'decimal:3',
            'variance_qty' => 'decimal:3',
            'unit_cost_snapshot' => 'decimal:6',
            'variance_value' => 'decimal:2',
            'counted_at' => 'datetime',
        ];
    }

    public function stockOpname(): BelongsTo
    {
        return $this->belongsTo(StockOpname::class, 'stock_opname_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function uom(): BelongsTo
    {
        return $this->belongsTo(ProductUom::class, 'product_uom_id');
    }

    public function counter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'counter_user_id');
    }

    public static function getReasonLabels(): array
    {
        return [
            self::REASON_NONE => 'Pilih Alasan Selisih...',
            self::REASON_NATURAL_SHRINK => 'Susut Alami / Menguap / Timbangan (Natural Shrink)',
            self::REASON_DAMAGED_EXPIRED => 'Barang Rusak / Busuk / Kedaluwarsa (Damaged / Expired)',
            self::REASON_LOST_THEFT => 'Hilang / Selisih Fisik Tak Ditemukan (Lost / Theft)',
            self::REASON_INPUT_ERROR => 'Kesalahan Pencatatan / Salah Masuk Lokasi (Data Error)',
            self::REASON_FOUND_EXTRA => 'Lebih Fisik / Ditemukan Kembali (Found Extra)',
        ];
    }

    public function getReasonLabelAttribute(): string
    {
        return self::getReasonLabels()[$this->reason_code] ?? $this->reason_code;
    }
}
