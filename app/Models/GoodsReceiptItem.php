<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoodsReceiptItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'goods_receipt_id',
        'purchase_order_item_id',
        'product_id',
        'product_uom_id',
        'qty_input',
        'factor_snapshot',
        'qty_base',
        'unit_cost_input',
        'unit_cost_base',
        'line_total',
        'batch_number',
        'expiry_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'qty_input' => 'decimal:3',
            'factor_snapshot' => 'decimal:6',
            'qty_base' => 'decimal:3',
            'unit_cost_input' => 'decimal:2',
            'unit_cost_base' => 'decimal:6',
            'line_total' => 'decimal:2',
            'expiry_date' => 'date',
        ];
    }

    public function goodsReceipt(): BelongsTo
    {
        return $this->belongsTo(GoodsReceipt::class, 'goods_receipt_id');
    }

    public function purchaseOrderItem(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderItem::class, 'purchase_order_item_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function productUom(): BelongsTo
    {
        return $this->belongsTo(ProductUom::class, 'product_uom_id');
    }
}
