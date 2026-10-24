<?php

namespace App\Services;

use App\Models\AnomalyEvent;
use App\Models\EmergencyPurchase;
use App\Models\ProductUom;
use App\Models\StockOperation;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EmergencyPurchaseService
{
    public function __construct(
        protected InventoryService $inventoryService
    ) {}

    /**
     * Record an emergency store purchase with mandatory photo proof,
     * immediate store stock increase, ORANGE ledger movement, and Anomaly publication.
     */
    public function recordEmergencyPurchase(array $data, UploadedFile $photo, User $creator): EmergencyPurchase
    {
        $productUom = ProductUom::with('product')->findOrFail($data['product_uom_id']);
        $qtyInput = (float) $data['qty_input'];
        $unitCostInput = (float) $data['unit_cost_input'];

        $factor = (float) $productUom->factor_to_base ?: 1.0;
        $qtyBase = round($qtyInput * $factor, 3);
        $unitCostBase = round($unitCostInput / $factor, 6);
        $totalAmount = round($qtyInput * $unitCostInput, 2);

        // Store photo proof on public disk for display in audit board
        $photoPath = $photo->store('emergency_receipts', 'public');

        return DB::transaction(function () use ($data, $productUom, $qtyInput, $factor, $qtyBase, $unitCostInput, $unitCostBase, $totalAmount, $photoPath, $creator) {
            $prefix = 'EMG-' . date('Ymd') . '-';
            $lastEmg = EmergencyPurchase::where('purchase_number', 'like', $prefix . '%')
                ->orderByDesc('id')
                ->lockForUpdate()
                ->first();

            $nextSequence = 1;
            if ($lastEmg) {
                $lastNum = (int) substr($lastEmg->purchase_number, -4);
                $nextSequence = $lastNum + 1;
            }
            $emgNumber = $prefix . str_pad((string) $nextSequence, 4, '0', STR_PAD_LEFT);

            // 1. Create Emergency Purchase record
            $emergencyPurchase = EmergencyPurchase::create([
                'purchase_number' => $emgNumber,
                'location_id' => $data['location_id'],
                'product_id' => $productUom->product_id,
                'product_uom_id' => $productUom->id,
                'qty_input' => $qtyInput,
                'factor_snapshot' => $factor,
                'qty_base' => $qtyBase,
                'unit_cost_input' => $unitCostInput,
                'unit_cost_base' => $unitCostBase,
                'total_amount' => $totalAmount,
                'merchant_name' => $data['merchant_name'],
                'receipt_photo_path' => $photoPath,
                'notes' => $data['notes'] ?? null,
                'created_by' => $creator->id,
                'posted_at' => now(),
            ]);

            // 2. Create Stock Operation header
            $operation = StockOperation::create([
                'operation_uuid' => (string) Str::uuid(),
                'operation_type' => 'EMERGENCY_PURCHASE',
                'document_type' => 'EMERGENCY_PURCHASE',
                'document_id' => $emergencyPurchase->id,
                'document_number' => $emergencyPurchase->purchase_number,
                'location_id' => $data['location_id'],
                'actor_user_id' => $creator->id,
                'posted_at' => now(),
                'notes' => "Belanja Emergency Toko dari {$data['merchant_name']}",
            ]);

            // 3. Atomically increase store stock & update Moving Average HPP with ORANGE badge
            $this->inventoryService->recordInbound(
                locationId: $data['location_id'],
                productId: $productUom->product_id,
                deltaQtyBase: $qtyBase,
                unitCostBase: $unitCostBase,
                movementType: 'EMERGENCY_PURCHASE',
                semanticStatus: 'ORANGE',
                operation: $operation,
                batchNumber: 'EMG-' . date('Ymd') . '-' . $emergencyPurchase->id
            );

            // 4. Publish ORANGE Anomaly Event for Owner visibility without holding up operations
            AnomalyEvent::create([
                'severity' => 'ORANGE',
                'kind' => 'EMERGENCY_PURCHASE',
                'location_id' => $data['location_id'],
                'actor_user_id' => $creator->id,
                'reference_type' => EmergencyPurchase::class,
                'reference_id' => $emergencyPurchase->id,
                'reference_number' => $emergencyPurchase->purchase_number,
                'amount' => $totalAmount,
                'title' => "Pembelian Emergency Toko: {$productUom->product->name}",
                'description' => "Staf {$creator->name} membeli {$qtyInput} {$productUom->uom->symbol} dari pedagang '{$data['merchant_name']}' seharga Rp " . number_format($totalAmount, 0, ',', '.') . ". Nota fisik terlampir.",
                'occurred_at' => now(),
            ]);

            return $emergencyPurchase->load(['location', 'product.baseUom', 'productUom.uom', 'creator']);
        });
    }
}
