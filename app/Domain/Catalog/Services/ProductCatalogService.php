<?php

namespace App\Domain\Catalog\Services;

use App\Models\Product;
use App\Models\ProductUom;
use App\Models\Uom;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ProductCatalogService
{
    /**
     * Check for potential duplicate products using fuzzy matching (similarity >= 75%)
     * PRD Section 4.2
     */
    public function checkFuzzySimilarity(string $name, ?int $excludeProductId = null, float $threshold = 75.0): array
    {
        $normalizedInput = Product::normalizeName($name);
        if (empty($normalizedInput)) {
            return [];
        }

        $candidates = Product::query()
            ->where('is_active', true)
            ->when($excludeProductId, fn($q) => $q->where('id', '!=', $excludeProductId))
            ->get();

        $duplicates = [];

        foreach ($candidates as $candidate) {
            $candidateNorm = $candidate->normalized_name ?: Product::normalizeName($candidate->name);

            // Exact match
            if ($candidateNorm === $normalizedInput) {
                $duplicates[] = [
                    'id' => $candidate->id,
                    'sku' => $candidate->sku,
                    'name' => $candidate->name,
                    'similarity' => 100.0,
                    'match_type' => 'EXACT',
                ];
                continue;
            }

            // Calculate percentage similarity
            similar_text($normalizedInput, $candidateNorm, $percent);

            if ($percent >= $threshold) {
                $duplicates[] = [
                    'id' => $candidate->id,
                    'sku' => $candidate->sku,
                    'name' => $candidate->name,
                    'similarity' => round($percent, 1),
                    'match_type' => 'FUZZY',
                ];
            }
        }

        // Sort descending by highest similarity
        usort($duplicates, fn($a, $b) => $b['similarity'] <=> $a['similarity']);

        return $duplicates;
    }

    /**
     * Create product along with base UOM and optional conversion UOMs
     */
    public function createProduct(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            $baseUom = Uom::findOrFail($data['base_uom_id']);

            $product = Product::create([
                'sku' => strtoupper(trim($data['sku'])),
                'name' => trim($data['name']),
                'normalized_name' => Product::normalizeName($data['name']),
                'category_id' => $data['category_id'],
                'base_uom_id' => $baseUom->id,
                'description' => $data['description'] ?? null,
                'image_path' => $data['image_path'] ?? null,
                'track_stock' => $data['track_stock'] ?? true,
                'is_active' => $data['is_active'] ?? true,
            ]);

            // 1. Create Base Product UOM (always factor = 1, is_base = true)
            ProductUom::create([
                'product_id' => $product->id,
                'uom_id' => $baseUom->id,
                'factor_to_base' => 1.000000,
                'barcode' => !empty($data['base_barcode']) ? trim($data['base_barcode']) : null,
                'is_purchase' => true,
                'is_sale' => true,
                'is_base' => true,
            ]);

            // 2. Additional Conversion UOMs
            if (!empty($data['conversions']) && is_array($data['conversions'])) {
                foreach ($data['conversions'] as $conv) {
                    if (empty($conv['uom_id']) || $conv['uom_id'] == $baseUom->id) {
                        continue;
                    }

                    $convUom = Uom::findOrFail($conv['uom_id']);
                    // Prevent cross-dimension conversion (PRD 4.2)
                    if ($convUom->dimension !== $baseUom->dimension) {
                        throw new InvalidArgumentException(
                            "Satuan konversi [{$convUom->name}] ({$convUom->dimension}) memiliki dimensi berbeda dengan satuan dasar [{$baseUom->name}] ({$baseUom->dimension})."
                        );
                    }

                    $factor = (float) ($conv['factor_to_base'] ?? 0);
                    if ($factor <= 0) {
                        throw new InvalidArgumentException("Faktor konversi untuk [{$convUom->name}] harus lebih besar dari 0.");
                    }

                    ProductUom::create([
                        'product_id' => $product->id,
                        'uom_id' => $convUom->id,
                        'factor_to_base' => $factor,
                        'barcode' => !empty($conv['barcode']) ? trim($conv['barcode']) : null,
                        'is_purchase' => !empty($conv['is_purchase']),
                        'is_sale' => !empty($conv['is_sale']),
                        'is_base' => false,
                    ]);
                }
            }

            return $product->load(['category', 'baseUom', 'productUoms.uom']);
        });
    }

    /**
     * Update product and synchronise conversion UOMs
     */
    public function updateProduct(Product $product, array $data): Product
    {
        return DB::transaction(function () use ($product, $data) {
            $baseUom = Uom::findOrFail($data['base_uom_id'] ?? $product->base_uom_id);

            $updateData = [
                'sku' => strtoupper(trim($data['sku'] ?? $product->sku)),
                'name' => trim($data['name'] ?? $product->name),
                'normalized_name' => Product::normalizeName($data['name'] ?? $product->name),
                'category_id' => $data['category_id'] ?? $product->category_id,
                'base_uom_id' => $baseUom->id,
                'description' => $data['description'] ?? $product->description,
                'track_stock' => isset($data['track_stock']) ? (bool) $data['track_stock'] : $product->track_stock,
                'is_active' => isset($data['is_active']) ? (bool) $data['is_active'] : $product->is_active,
            ];

            if (array_key_exists('image_path', $data)) {
                $updateData['image_path'] = $data['image_path'];
            }

            $product->update($updateData);

            // Update base UOM barcode
            $baseProductUom = $product->productUoms()->where('is_base', true)->first();
            if ($baseProductUom) {
                $baseProductUom->update([
                    'uom_id' => $baseUom->id,
                    'barcode' => !empty($data['base_barcode']) ? trim($data['base_barcode']) : $baseProductUom->barcode,
                ]);
            } else {
                ProductUom::create([
                    'product_id' => $product->id,
                    'uom_id' => $baseUom->id,
                    'factor_to_base' => 1.000000,
                    'barcode' => !empty($data['base_barcode']) ? trim($data['base_barcode']) : null,
                    'is_purchase' => true,
                    'is_sale' => true,
                    'is_base' => true,
                ]);
            }

            // Sync additional conversions if provided
            if (isset($data['conversions']) && is_array($data['conversions'])) {
                $activeUomIds = [$baseUom->id];

                foreach ($data['conversions'] as $conv) {
                    if (empty($conv['uom_id']) || $conv['uom_id'] == $baseUom->id) {
                        continue;
                    }

                    $convUom = Uom::findOrFail($conv['uom_id']);
                    if ($convUom->dimension !== $baseUom->dimension) {
                        throw new InvalidArgumentException(
                            "Satuan konversi [{$convUom->name}] ({$convUom->dimension}) memiliki dimensi berbeda dengan satuan dasar [{$baseUom->name}] ({$baseUom->dimension})."
                        );
                    }

                    $factor = (float) ($conv['factor_to_base'] ?? 0);
                    if ($factor <= 0) {
                        throw new InvalidArgumentException("Faktor konversi untuk [{$convUom->name}] harus lebih besar dari 0.");
                    }

                    $activeUomIds[] = $convUom->id;

                    ProductUom::updateOrCreate(
                        ['product_id' => $product->id, 'uom_id' => $convUom->id],
                        [
                            'factor_to_base' => $factor,
                            'barcode' => !empty($conv['barcode']) ? trim($conv['barcode']) : null,
                            'is_purchase' => !empty($conv['is_purchase']),
                            'is_sale' => !empty($conv['is_sale']),
                            'is_base' => false,
                        ]
                    );
                }

                // Remove conversion UOMs that were deleted from the form
                $product->productUoms()
                    ->where('is_base', false)
                    ->whereNotIn('uom_id', $activeUomIds)
                    ->delete();
            }

            return $product->fresh(['category', 'baseUom', 'productUoms.uom']);
        });
    }

    /**
     * Soft deactivation for Product
     */
    public function deactivateProduct(Product $product): bool
    {
        return $product->update(['is_active' => false]);
    }
}
