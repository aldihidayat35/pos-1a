<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'name',
        'normalized_name',
        'category_id',
        'base_uom_id',
        'description',
        'image_path',
        'track_stock',
        'is_active',
    ];

    protected $appends = [
        'price',
        'image_url',
    ];

    protected function casts(): array
    {
        return [
            'track_stock' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function getPriceAttribute(): float
    {
        $rule = PriceRule::where('product_id', $this->id)
            ->where('is_active', true)
            ->first();

        if ($rule && $rule->list_price > 0) {
            return (float) $rule->list_price;
        }

        return (float) ($this->inventoryBalances()->value('avg_unit_cost') ?? 0.0);
    }

    public function getImageUrlAttribute(): ?string
    {
        if ($this->image_path) {
            return asset('storage/' . $this->image_path);
        }
        return null;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function baseUom(): BelongsTo
    {
        return $this->belongsTo(Uom::class, 'base_uom_id');
    }

    public function productUoms(): HasMany
    {
        return $this->hasMany(ProductUom::class, 'product_id');
    }

    public function baseProductUom(): HasOne
    {
        return $this->hasOne(ProductUom::class, 'product_id')->where('is_base', true);
    }

    public function uoms(): BelongsToMany
    {
        return $this->belongsToMany(Uom::class, 'product_uoms', 'product_id', 'uom_id')
            ->withPivot(['id', 'factor_to_base', 'barcode', 'is_purchase', 'is_sale', 'is_base'])
            ->withTimestamps();
    }

    public function inventoryBalances(): HasMany
    {
        return $this->hasMany(InventoryBalance::class, 'product_id');
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'product_id');
    }

    public function stockBatches(): HasMany
    {
        return $this->hasMany(StockBatch::class, 'product_id');
    }

    public function getBalanceAt(int $locationId): ?InventoryBalance
    {
        return $this->inventoryBalances()->where('location_id', $locationId)->first();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Normalize product name for fuzzy duplicate matching (PRD Section 4.2)
     */
    public static function normalizeName(string $name): string
    {
        $normalized = mb_strtolower(trim($name), 'UTF-8');
        // Replace common unit variations
        $replacements = [
            '/\bkilogram\b/' => 'kg',
            '/\bgram\b/' => 'g',
            '/\bbuah\b/' => 'pcs',
            '/\bpotong\b/' => 'pcs',
            '/\bliter\b/' => 'l',
            '/\bmililiter\b/' => 'ml',
            '/[^\p{L}\p{N}\s]/u' => '', // remove punctuation
            '/\s+/' => ' ', // collapse spaces
        ];
        return trim(preg_replace(array_keys($replacements), array_values($replacements), $normalized));
    }
}
