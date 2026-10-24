<?php

namespace App\Services;

use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use App\Models\ProductUom;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\StockOperation;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PurchasingService
{
    public function __construct(
        protected InventoryService $inventoryService
    ) {}

    /**
     * Create a new Purchase Order
     */
    public function createPurchaseOrder(array $data, User $creator): PurchaseOrder
    {
        return DB::transaction(function () use ($data, $creator) {
            $prefix = 'PO-' . date('Ymd') . '-';
            $lastPo = PurchaseOrder::where('po_number', 'like', $prefix . '%')
                ->orderByDesc('id')
                ->lockForUpdate()
                ->first();

            $nextSequence = 1;
            if ($lastPo) {
                $lastNum = (int) substr($lastPo->po_number, -4);
                $nextSequence = $lastNum + 1;
            }
            $poNumber = $prefix . str_pad((string) $nextSequence, 4, '0', STR_PAD_LEFT);

            $po = PurchaseOrder::create([
                'po_number' => $poNumber,
                'supplier_id' => $data['supplier_id'],
                'destination_location_id' => $data['destination_location_id'],
                'status' => 'ISSUED',
                'ordered_at' => $data['ordered_at'] ?? now(),
                'expected_delivery_at' => $data['expected_delivery_at'] ?? null,
                'notes' => $data['notes'] ?? null,
                'created_by' => $creator->id,
            ]);

            $subtotal = 0.00;
            foreach ($data['items'] as $itemData) {
                $productUom = ProductUom::findOrFail($itemData['product_uom_id']);
                $qtyOrdered = (float) $itemData['quantity_ordered'];
                $unitPrice = (float) $itemData['unit_purchase_price'];
                $lineTotal = round($qtyOrdered * $unitPrice, 2);
                $subtotal += $lineTotal;

                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'product_id' => $itemData['product_id'],
                    'product_uom_id' => $productUom->id,
                    'quantity_ordered' => $qtyOrdered,
                    'factor_to_base_snapshot' => $productUom->factor_to_base,
                    'unit_purchase_price' => $unitPrice,
                    'line_total' => $lineTotal,
                    'quantity_received_base' => 0.000,
                ]);
            }

            $taxAmount = 0.00; // Tax configuration hook
            $grandTotal = $subtotal + $taxAmount;

            $po->update([
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'grand_total' => $grandTotal,
            ]);

            return $po->load(['items.product', 'items.productUom.uom', 'supplier', 'destinationLocation']);
        });
    }

    /**
     * Process physical Goods Receipt (partial or full) against a Purchase Order
     */
    public function receiveGoodsReceipt(PurchaseOrder $po, array $data, User $receiver): GoodsReceipt
    {
        return DB::transaction(function () use ($po, $data, $receiver) {
            // Lock PO
            $po = PurchaseOrder::where('id', $po->id)->lockForUpdate()->firstOrFail();

            if (in_array($po->status, ['CANCELLED', 'CLOSED'])) {
                throw new \DomainException("Purchase Order {$po->po_number} berstatus {$po->status} dan tidak dapat menerima barang.");
            }

            // Generate GRN number
            $prefix = 'GRN-' . date('Ymd') . '-';
            $lastGrn = GoodsReceipt::where('receipt_number', 'like', $prefix . '%')
                ->orderByDesc('id')
                ->lockForUpdate()
                ->first();

            $nextSequence = 1;
            if ($lastGrn) {
                $lastNum = (int) substr($lastGrn->receipt_number, -4);
                $nextSequence = $lastNum + 1;
            }
            $grnNumber = $prefix . str_pad((string) $nextSequence, 4, '0', STR_PAD_LEFT);

            // Create Goods Receipt header
            $goodsReceipt = GoodsReceipt::create([
                'receipt_number' => $grnNumber,
                'receipt_type' => 'PO',
                'purchase_order_id' => $po->id,
                'supplier_id' => $po->supplier_id,
                'supplier_name_snapshot' => $po->supplier?->name,
                'supplier_do_number' => $data['supplier_do_number'] ?? null,
                'location_id' => $po->destination_location_id,
                'status' => 'POSTED',
                'notes' => $data['notes'] ?? null,
                'posted_at' => now(),
                'created_by' => $receiver->id,
            ]);

            // Create Stock Operation header
            $operation = StockOperation::create([
                'operation_uuid' => (string) Str::uuid(),
                'operation_type' => 'INBOUND_PO',
                'document_type' => 'GOODS_RECEIPT',
                'document_id' => $goodsReceipt->id,
                'document_number' => $goodsReceipt->receipt_number,
                'location_id' => $po->destination_location_id,
                'actor_user_id' => $receiver->id,
                'posted_at' => now(),
                'notes' => 'Penerimaan PO ' . $po->po_number . ' (DO: ' . ($data['supplier_do_number'] ?? '-') . ')',
            ]);

            $hasReceivedAny = false;

            foreach ($data['items'] as $itemData) {
                $qtyInput = (float) ($itemData['qty_input'] ?? 0);
                if ($qtyInput <= 0) {
                    continue;
                }

                $hasReceivedAny = true;
                $poItem = PurchaseOrderItem::where('id', $itemData['purchase_order_item_id'])
                    ->where('purchase_order_id', $po->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $factor = (float) $poItem->factor_to_base_snapshot ?: 1.0;
                $qtyBase = round($qtyInput * $factor, 3);

                $unitCostInput = isset($itemData['unit_cost_input']) && $itemData['unit_cost_input'] !== ''
                    ? (float) $itemData['unit_cost_input']
                    : (float) $poItem->unit_purchase_price;

                $unitCostBase = round($unitCostInput / $factor, 6);
                $lineTotal = round($qtyInput * $unitCostInput, 2);

                $batchNumber = !empty($itemData['batch_number'])
                    ? trim($itemData['batch_number'])
                    : 'LOT-' . date('Ymd') . '-' . $poItem->product_id;

                $expiryDate = !empty($itemData['expiry_date']) ? $itemData['expiry_date'] : null;

                // 1. Create Goods Receipt Item
                GoodsReceiptItem::create([
                    'goods_receipt_id' => $goodsReceipt->id,
                    'purchase_order_item_id' => $poItem->id,
                    'product_id' => $poItem->product_id,
                    'product_uom_id' => $poItem->product_uom_id,
                    'qty_input' => $qtyInput,
                    'factor_snapshot' => $factor,
                    'qty_base' => $qtyBase,
                    'unit_cost_input' => $unitCostInput,
                    'unit_cost_base' => $unitCostBase,
                    'line_total' => $lineTotal,
                    'batch_number' => $batchNumber,
                    'expiry_date' => $expiryDate,
                    'notes' => $itemData['notes'] ?? null,
                ]);

                // 2. Update stock balance & ledger via InventoryService
                $this->inventoryService->recordInbound(
                    locationId: $po->destination_location_id,
                    productId: $poItem->product_id,
                    deltaQtyBase: $qtyBase,
                    unitCostBase: $unitCostBase,
                    movementType: 'INBOUND_PO',
                    semanticStatus: 'GREEN',
                    operation: $operation,
                    batchNumber: $batchNumber,
                    expiryDate: $expiryDate
                );

                // 3. Update PO Item received progress
                $poItem->quantity_received_base = round(((float) $poItem->quantity_received_base) + $qtyBase, 3);
                $poItem->save();
            }

            if (!$hasReceivedAny) {
                throw new \DomainException("Setidaknya satu barang harus memiliki jumlah penerimaan lebih dari 0.");
            }

            // 4. Update overall PO status (PARTIALLY_RECEIVED vs RECEIVED)
            $po->refresh();
            $allReceived = true;
            foreach ($po->items as $item) {
                if (!$item->isFullyReceived()) {
                    $allReceived = false;
                    break;
                }
            }

            $po->status = $allReceived ? 'RECEIVED' : 'PARTIALLY_RECEIVED';
            $po->save();

            return $goodsReceipt->load(['items.product', 'items.productUom.uom', 'supplier', 'location']);
        });
    }
}
