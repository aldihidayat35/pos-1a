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

            // Sync Base UOM and Conversion UOMs cleanly without unique constraint violation
            $submittedConversions = (isset($data['conversions']) && is_array($data['conversions']))
                ? $data['conversions']
                : [];

            $validConversions = [];
            $targetConvUomIds = [];

            foreach ($submittedConversions as $conv) {
                if (empty($conv['uom_id']) || (int)$conv['uom_id'] === (int)$baseUom->id) {
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

                $uomId = (int)$convUom->id;
                $targetConvUomIds[] = $uomId;
                $validConversions[$uomId] = [
                    'factor' => $factor,
                    'barcode' => !empty($conv['barcode']) ? trim($conv['barcode']) : null,
                    'is_purchase' => !empty($conv['is_purchase']),
                    'is_sale' => !empty($conv['is_sale']),
                ];
            }

            // Allowed UOM IDs for this product (new Base UOM + valid conversion UOMs)
            $allowedUomIds = array_unique(array_merge([(int)$baseUom->id], $targetConvUomIds));

            // 1. Delete all product_uoms that were removed from the form (neither base nor in target conversions)
            $product->productUoms()
                ->whereNotIn('uom_id', $allowedUomIds)
                ->delete();

            // 2. Demote any former base UOM that is now a conversion (if retained in allowedUomIds)
            $product->productUoms()
                ->where('uom_id', '!=', $baseUom->id)
                ->where('is_base', true)
                ->update(['is_base' => false]);

            // 3. Promote or create the new Base UOM row
            $baseBarcode = array_key_exists('base_barcode', $data)
                ? (!empty($data['base_barcode']) ? trim($data['base_barcode']) : null)
                : null;
            $baseProductUom = $product->productUoms()->where('uom_id', $baseUom->id)->first();

            if ($baseProductUom) {
                $baseProductUom->update([
                    'factor_to_base' => 1.000000,
                    'barcode' => array_key_exists('base_barcode', $data) ? $baseBarcode : $baseProductUom->barcode,
                    'is_purchase' => true,
                    'is_sale' => true,
                    'is_base' => true,
                ]);
            } else {
                ProductUom::create([
                    'product_id' => $product->id,
                    'uom_id' => $baseUom->id,
                    'factor_to_base' => 1.000000,
                    'barcode' => $baseBarcode,
                    'is_purchase' => true,
                    'is_sale' => true,
                    'is_base' => true,
                ]);
            }

            // 4. Upsert submitted conversion rows
            foreach ($validConversions as $uomId => $convData) {
                ProductUom::updateOrCreate(
                    ['product_id' => $product->id, 'uom_id' => $uomId],
                    [
                        'factor_to_base' => $convData['factor'],
                        'barcode' => $convData['barcode'],
                        'is_purchase' => $convData['is_purchase'],
                        'is_sale' => $convData['is_sale'],
                        'is_base' => false,
                    ]
                );
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
