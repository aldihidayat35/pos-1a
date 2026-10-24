<?php

namespace App\Services;

use App\Models\InventoryBalance;
use App\Models\Product;
use App\Models\StockBatch;
use App\Models\StockMovement;
use App\Models\StockOperation;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InventoryService
{
    /**
     * Record an atomic inbound stock movement with Moving Average HPP calculation
     * and pessimistic row-locking on the destination balance.
     */
    public function recordInbound(
        int $locationId,
        int $productId,
        float $deltaQtyBase,
        float $unitCostBase,
        string $movementType,
        string $semanticStatus,
        StockOperation $operation,
        ?string $batchNumber = null,
        ?string $expiryDate = null
    ): StockMovement {
        if ($deltaQtyBase <= 0) {
            throw new \InvalidArgumentException("Inbound delta quantity must be strictly positive.");
        }

        // 1. Lock or initialize inventory balance row
        $balance = InventoryBalance::where('location_id', $locationId)
            ->where('product_id', $productId)
            ->lockForUpdate()
            ->first();

        if (!$balance) {
            $balance = new InventoryBalance([
                'location_id' => $locationId,
                'product_id' => $productId,
                'on_hand_qty' => 0.000,
                'reserved_qty' => 0.000,
                'avg_unit_cost' => 0.000000,
                'version' => 1,
            ]);
        }

        $currentQty = (float) $balance->on_hand_qty;
        $currentCost = (float) $balance->avg_unit_cost;
        $inboundQty = $deltaQtyBase;
        $inboundCost = $unitCostBase;

        // 2. Weighted Moving Average formula (PRD v2 Section 4.3):
        // New HPP = ((Current Qty * Current HPP) + (Inbound Qty * Inbound Cost)) / (Current Qty + Inbound Qty)
        $newTotalQty = $currentQty + $inboundQty;
        if ($newTotalQty > 0) {
            $newAvgCost = (($currentQty * $currentCost) + ($inboundQty * $inboundCost)) / $newTotalQty;
        } else {
            $newAvgCost = $inboundCost;
        }

        // 3. Update Balance
        $balance->on_hand_qty = $newTotalQty;
        $balance->avg_unit_cost = round($newAvgCost, 6);
        $balance->version = ((int) $balance->version) + 1;
        $balance->save();

        // 4. Record/Update Lot Batch if provided
        if (!empty($batchNumber)) {
            $batch = StockBatch::firstOrNew([
                'location_id' => $locationId,
                'product_id' => $productId,
                'batch_number' => $batchNumber,
            ]);

            $batch->initial_qty = ((float) $batch->initial_qty) + $deltaQtyBase;
            $batch->current_qty = ((float) $batch->current_qty) + $deltaQtyBase;
            $batch->unit_cost = $unitCostBase;
            $batch->received_date = now()->toDateString();
            if ($expiryDate) {
                $batch->expiry_date = $expiryDate;
            }
            $batch->status = 'ACTIVE';
            $batch->save();
        }

        // 5. Append-only Immutable Stock Movement Record
        return StockMovement::create([
            'operation_id' => $operation->id,
            'location_id' => $locationId,
            'product_id' => $productId,
            'delta_qty_base' => $deltaQtyBase,
            'unit_cost_snapshot' => $unitCostBase,
            'amount_value' => round($deltaQtyBase * $unitCostBase, 2),
            'balance_after_qty' => $balance->on_hand_qty,
            'movement_type' => $movementType,
            'semantic_status' => $semanticStatus,
            'occurred_at' => now(),
        ]);
    }

    /**
     * Record Opening Balance for initial inventory setup
     */
    public function recordOpeningBalance(
        int $locationId,
        int $productId,
        float $qtyBase,
        float $unitCostBase,
        User $actor,
        ?string $notes = 'Saldo Awal Sistem'
    ): StockMovement {
        return DB::transaction(function () use ($locationId, $productId, $qtyBase, $unitCostBase, $actor, $notes) {
            $operation = StockOperation::create([
                'operation_uuid' => (string) Str::uuid(),
                'operation_type' => 'OPENING_BALANCE',
                'document_type' => 'OPENING_BALANCE',
                'document_number' => 'OPB-' . date('Ymd') . '-' . str_pad((string) rand(1, 9999), 4, '0', STR_PAD_LEFT),
                'location_id' => $locationId,
                'actor_user_id' => $actor->id,
                'posted_at' => now(),
                'notes' => $notes,
            ]);

            return $this->recordInbound(
                locationId: $locationId,
                productId: $productId,
                deltaQtyBase: $qtyBase,
                unitCostBase: $unitCostBase,
                movementType: 'OPENING_BALANCE',
                semanticStatus: 'GREEN',
                operation: $operation,
                batchNumber: 'INIT-' . date('Ymd')
            );
        });
    }
}
