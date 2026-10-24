<?php

namespace App\Services;

use App\Models\InventoryBalance;
use App\Models\Location;
use App\Models\ProductUom;
use App\Models\StockMovement;
use App\Models\StockOperation;
use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransferService
{
    /**
     * Create a transfer order in DRAFT status
     */
    public function createTransfer(array $data, User $creator): StockTransfer
    {
        if ($data['from_location_id'] == $data['to_location_id']) {
            throw new \DomainException("Lokasi asal dan tujuan transfer tidak boleh sama.");
        }

        $transitLocation = Location::where('type', 'TRANSIT')->firstOrFail();

        return DB::transaction(function () use ($data, $creator, $transitLocation) {
            $prefix = 'TRF-' . date('Ymd') . '-';
            $lastTrf = StockTransfer::where('transfer_number', 'like', $prefix . '%')
                ->orderByDesc('id')
                ->lockForUpdate()
                ->first();

            $nextSequence = 1;
            if ($lastTrf) {
                $lastNum = (int) substr($lastTrf->transfer_number, -4);
                $nextSequence = $lastNum + 1;
            }
            $trfNumber = $prefix . str_pad((string) $nextSequence, 4, '0', STR_PAD_LEFT);

            $transfer = StockTransfer::create([
                'transfer_number' => $trfNumber,
                'from_location_id' => $data['from_location_id'],
                'to_location_id' => $data['to_location_id'],
                'transit_location_id' => $transitLocation->id,
                'status' => 'DRAFT',
                'notes' => $data['notes'] ?? null,
                'created_by' => $creator->id,
            ]);

            foreach ($data['items'] as $itemData) {
                $productUom = ProductUom::findOrFail($itemData['product_uom_id']);
                $qtyInput = (float) $itemData['quantity'];
                $factor = (float) $productUom->factor_to_base ?: 1.0;
                $qtyBase = round($qtyInput * $factor, 3);

                StockTransferItem::create([
                    'stock_transfer_id' => $transfer->id,
                    'product_id' => $itemData['product_id'],
                    'product_uom_id' => $productUom->id,
                    'requested_qty_input' => $qtyInput,
                    'factor_snapshot' => $factor,
                    'requested_qty_base' => $qtyBase,
                    'dispatched_qty_base' => 0.000,
                    'received_qty_base' => 0.000,
                    'cost_snapshot' => 0.000000,
                    'notes' => $itemData['notes'] ?? null,
                ]);
            }

            return $transfer->load(['fromLocation', 'toLocation', 'transitLocation', 'items.product', 'items.productUom.uom']);
        });
    }

    /**
     * Dispatch transfer: cuts stock from origin and moves to transit location
     */
    public function dispatchTransfer(StockTransfer $transfer, User $dispatcher): StockTransfer
    {
        return DB::transaction(function () use ($transfer, $dispatcher) {
            $transfer = StockTransfer::where('id', $transfer->id)->lockForUpdate()->firstOrFail();

            if (!in_array($transfer->status, ['DRAFT'])) {
                throw new \DomainException("Transfer {$transfer->transfer_number} tidak dapat di-dispatch karena berstatus {$transfer->status}.");
            }

            $operation = StockOperation::create([
                'operation_uuid' => (string) Str::uuid(),
                'operation_type' => 'TRANSFER_DISPATCH',
                'document_type' => 'STOCK_TRANSFER',
                'document_id' => $transfer->id,
                'document_number' => $transfer->transfer_number,
                'location_id' => $transfer->from_location_id,
                'actor_user_id' => $dispatcher->id,
                'posted_at' => now(),
                'notes' => 'Dispatch kiriman ' . $transfer->transfer_number . ' ke ' . $transfer->toLocation->name,
            ]);

            foreach ($transfer->items as $item) {
                // 1. Lock origin balance & check availability
                $originBalance = InventoryBalance::where('location_id', $transfer->from_location_id)
                    ->where('product_id', $item->product_id)
                    ->lockForUpdate()
                    ->first();

                $available = $originBalance ? $originBalance->available_qty : 0.0;
                if ($available < (float) $item->requested_qty_base) {
                    throw new \DomainException("Stok di lokasi asal tidak mencukupi untuk {$item->product->name}. Tersedia: {$available}, diminta: {$item->requested_qty_base}.");
                }

                $costSnapshot = (float) $originBalance->avg_unit_cost;

                // Deduct origin balance
                $originBalance->on_hand_qty = round((float) $originBalance->on_hand_qty - (float) $item->requested_qty_base, 3);
                $originBalance->version += 1;
                $originBalance->save();

                // 2. Add to virtual transit balance
                $transitBalance = InventoryBalance::firstOrCreate(
                    ['location_id' => $transfer->transit_location_id, 'product_id' => $item->product_id],
                    ['on_hand_qty' => 0.000, 'reserved_qty' => 0.000, 'avg_unit_cost' => $costSnapshot, 'version' => 1]
                );
                $transitBalance = InventoryBalance::where('id', $transitBalance->id)->lockForUpdate()->first();
                $transitBalance->on_hand_qty = round((float) $transitBalance->on_hand_qty + (float) $item->requested_qty_base, 3);
                $transitBalance->avg_unit_cost = $costSnapshot;
                $transitBalance->version += 1;
                $transitBalance->save();

                // 3. Record 2 ledger rows: Origin OUT (BLUE) & Transit IN (BLUE)
                StockMovement::create([
                    'operation_id' => $operation->id,
                    'location_id' => $transfer->from_location_id,
                    'product_id' => $item->product_id,
                    'delta_qty_base' => -(float) $item->requested_qty_base,
                    'unit_cost_snapshot' => $costSnapshot,
                    'amount_value' => round((float) $item->requested_qty_base * $costSnapshot, 2),
                    'balance_after_qty' => $originBalance->on_hand_qty,
                    'movement_type' => 'TRANSFER_OUT',
                    'semantic_status' => 'BLUE',
                    'occurred_at' => now(),
                ]);

                StockMovement::create([
                    'operation_id' => $operation->id,
                    'location_id' => $transfer->transit_location_id,
                    'product_id' => $item->product_id,
                    'delta_qty_base' => +(float) $item->requested_qty_base,
                    'unit_cost_snapshot' => $costSnapshot,
                    'amount_value' => round((float) $item->requested_qty_base * $costSnapshot, 2),
                    'balance_after_qty' => $transitBalance->on_hand_qty,
                    'movement_type' => 'TRANSFER_TRANSIT_IN',
                    'semantic_status' => 'BLUE',
                    'occurred_at' => now(),
                ]);

                // Update item dispatched quantity
                $item->dispatched_qty_base = $item->requested_qty_base;
                $item->cost_snapshot = $costSnapshot;
                $item->save();
            }

            $transfer->status = 'DISPATCHED';
            $transfer->dispatched_at = now();
            $transfer->save();

            return $transfer;
        });
    }

    /**
     * Receive transfer at destination location: cuts from transit and adds to destination
     * Recalculates Moving Average HPP at destination!
     */
    public function receiveTransfer(StockTransfer $transfer, array $itemsData, User $receiver): StockTransfer
    {
        return DB::transaction(function () use ($transfer, $itemsData, $receiver) {
            $transfer = StockTransfer::where('id', $transfer->id)->lockForUpdate()->firstOrFail();

            if (!in_array($transfer->status, ['DISPATCHED', 'PARTIALLY_RECEIVED'])) {
                throw new \DomainException("Transfer {$transfer->transfer_number} tidak dalam status perjalanan.");
            }

            $operation = StockOperation::create([
                'operation_uuid' => (string) Str::uuid(),
                'operation_type' => 'TRANSFER_RECEIVE',
                'document_type' => 'STOCK_TRANSFER',
                'document_id' => $transfer->id,
                'document_number' => $transfer->transfer_number,
                'location_id' => $transfer->to_location_id,
                'actor_user_id' => $receiver->id,
                'posted_at' => now(),
                'notes' => 'Penerimaan kiriman ' . $transfer->transfer_number . ' di ' . $transfer->toLocation->name,
            ]);

            $allReceived = true;

            foreach ($itemsData as $row) {
                $item = StockTransferItem::where('id', $row['item_id'])
                    ->where('stock_transfer_id', $transfer->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $receivedInput = (float) ($row['qty_received'] ?? 0);
                if ($receivedInput <= 0) {
                    continue;
                }

                $factor = (float) $item->factor_snapshot ?: 1.0;
                $receivedBase = round($receivedInput * $factor, 3);

                // Lock transit balance
                $transitBalance = InventoryBalance::where('location_id', $transfer->transit_location_id)
                    ->where('product_id', $item->product_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $transitBalance->on_hand_qty = round(max(0, (float) $transitBalance->on_hand_qty - $receivedBase), 3);
                $transitBalance->save();

                // Lock destination balance & calculate New Moving Average HPP
                $destBalance = InventoryBalance::firstOrCreate(
                    ['location_id' => $transfer->to_location_id, 'product_id' => $item->product_id],
                    ['on_hand_qty' => 0.000, 'reserved_qty' => 0.000, 'avg_unit_cost' => (float) $item->cost_snapshot, 'version' => 1]
                );
                $destBalance = InventoryBalance::where('id', $destBalance->id)->lockForUpdate()->first();

                $destCurrentQty = (float) $destBalance->on_hand_qty;
                $destCurrentCost = (float) $destBalance->avg_unit_cost;
                $incomingCost = (float) $item->cost_snapshot;

                $newTotalQty = $destCurrentQty + $receivedBase;
                if ($newTotalQty > 0) {
                    $newDestCost = (($destCurrentQty * $destCurrentCost) + ($receivedBase * $incomingCost)) / $newTotalQty;
                } else {
                    $newDestCost = $incomingCost;
                }

                $destBalance->on_hand_qty = $newTotalQty;
                $destBalance->avg_unit_cost = round($newDestCost, 6);
                $destBalance->version += 1;
                $destBalance->save();

                // Record 2 ledger rows: Transit OUT (BLUE) & Destination IN (BLUE)
                StockMovement::create([
                    'operation_id' => $operation->id,
                    'location_id' => $transfer->transit_location_id,
                    'product_id' => $item->product_id,
                    'delta_qty_base' => -$receivedBase,
                    'unit_cost_snapshot' => $incomingCost,
                    'amount_value' => round($receivedBase * $incomingCost, 2),
                    'balance_after_qty' => $transitBalance->on_hand_qty,
                    'movement_type' => 'TRANSFER_TRANSIT_OUT',
                    'semantic_status' => 'BLUE',
                    'occurred_at' => now(),
                ]);

                StockMovement::create([
                    'operation_id' => $operation->id,
                    'location_id' => $transfer->to_location_id,
                    'product_id' => $item->product_id,
                    'delta_qty_base' => +$receivedBase,
                    'unit_cost_snapshot' => $incomingCost,
                    'amount_value' => round($receivedBase * $incomingCost, 2),
                    'balance_after_qty' => $destBalance->on_hand_qty,
                    'movement_type' => 'TRANSFER_IN',
                    'semantic_status' => 'BLUE',
                    'occurred_at' => now(),
                ]);

                $item->received_qty_base = round((float) $item->received_qty_base + $receivedBase, 3);
                $item->save();

                if ($item->remaining_to_receive_base > 0.0001) {
                    $allReceived = false;
                }
            }

            $transfer->status = $allReceived ? 'RECEIVED' : 'PARTIALLY_RECEIVED';
            $transfer->received_at = now();
            $transfer->received_by = $receiver->id;
            $transfer->save();

            return $transfer;
        });
    }
}
