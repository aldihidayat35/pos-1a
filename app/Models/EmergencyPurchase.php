<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmergencyPurchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_number',
        'location_id',
        'product_id',
        'product_uom_id',
        'qty_input',
        'factor_snapshot',
        'qty_base',
        'unit_cost_input',
        'unit_cost_base',
        'total_amount',
        'merchant_name',
        'receipt_photo_path',
        'notes',
        'created_by',
        'posted_at',
    ];

    protected function casts(): array
    {
        return [
            'qty_input' => 'decimal:3',
            'factor_snapshot' => 'decimal:6',
            'qty_base' => 'decimal:3',
            'unit_cost_input' => 'decimal:2',
            'unit_cost_base' => 'decimal:6',
            'total_amount' => 'decimal:2',
            'posted_at' => 'datetime',
        ];
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function productUom(): BelongsTo
    {
        return $this->belongsTo(ProductUom::class, 'product_uom_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
