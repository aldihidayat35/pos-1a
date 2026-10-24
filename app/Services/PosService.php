<?php

namespace App\Services;

use App\Models\AnomalyEvent;
use App\Models\CashShift;
use App\Models\InventoryBalance;
use App\Models\Location;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductUom;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use App\Models\StockOperation;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PosService
{
    public function __construct(
        protected PricingService $pricingService,
        protected InventoryService $inventoryService
    ) {}

    /**
     * Retrieve active open shift for cashier at specific location
     */
    public function getActiveShift(User $user, Location $location): ?CashShift
    {
        return CashShift::where('cashier_user_id', $user->id)
            ->where('location_id', $location->id)
            ->where('status', 'OPEN')
            ->latest('id')
            ->first();
    }

    /**
     * Open a new cash drawer shift
     */
    public function openShift(Location $location, User $cashier, float $startingCash, ?string $notes = null): CashShift
    {
        $existing = $this->getActiveShift($cashier, $location);
        if ($existing) {
            return $existing;
        }

        $prefix = 'SFT-' . date('Ymd') . '-';
        $lastShift = CashShift::where('shift_number', 'LIKE', $prefix . '%')
            ->orderByDesc('id')
            ->first();
        $nextSeq = $lastShift ? ((int) substr($lastShift->shift_number, -4)) + 1 : 1;
        $shiftNumber = $prefix . str_pad((string) $nextSeq, 4, '0', STR_PAD_LEFT);

        return CashShift::create([
            'shift_number' => $shiftNumber,
            'location_id' => $location->id,
            'cashier_user_id' => $cashier->id,
            'status' => 'OPEN',
            'opened_at' => now(),
            'starting_cash' => round($startingCash, 2),
            'notes' => $notes,
        ]);
    }

    /**
     * Close cashier shift with blind physical cash count and discrepancy audit
     */
    public function closeShift(CashShift $shift, float $actualCash, ?string $notes = null): CashShift
    {
        return DB::transaction(function () use ($shift, $actualCash, $notes) {
            $shift = CashShift::where('id', $shift->id)->lockForUpdate()->firstOrFail();

            if ($shift->status !== 'OPEN') {
                throw new \DomainException("Shift {$shift->shift_number} sudah dalam status tertutup.");
            }

            // Calculate expected cash from sales and voids
            $cashSales = (float) Sale::where('cash_shift_id', $shift->id)
                ->where('status', 'POSTED')
                ->where('payment_method', 'CASH')
                ->sum('total_amount');

            $cashRefunds = (float) Sale::where('cash_shift_id', $shift->id)
                ->where('status', 'VOIDED')
                ->where('payment_method', 'CASH')
                ->sum('total_amount');

            $expectedCash = round((float) $shift->starting_cash + $cashSales - $cashRefunds, 2);
            $difference = round($actualCash - $expectedCash, 2);

            $shift->update([
                'status' => 'CLOSED',
                'closed_at' => now(),
                'expected_cash' => $expectedCash,
                'actual_cash' => round($actualCash, 2),
                'difference' => $difference,
                'notes' => $notes ?? $shift->notes,
            ]);

            // If cash discrepancy detected, log anomaly event
            if (abs($difference) > 0.01) {
                $isDeficit = $difference < 0;
                AnomalyEvent::create([
                    'severity' => $isDeficit ? 'RED' : 'ORANGE',
                    'kind' => 'CASH_DRAWER_DISCREPANCY',
                    'location_id' => $shift->location_id,
                    'actor_user_id' => $shift->cashier_user_id,
                    'reference_type' => CashShift::class,
                    'reference_id' => $shift->id,
                    'reference_number' => $shift->shift_number,
                    'amount' => abs($difference),
                    'title' => "Selisih Kas Laci: Shift {$shift->shift_number} (" . ($isDeficit ? 'Kurang Kas' : 'Lebih Kas') . ")",
                    'description' => "Kasir {$shift->cashier->name} menutup shift. Diharapkan: Rp " . number_format($expectedCash, 0, ',', '.') . ", Fisik aktual: Rp " . number_format($actualCash, 0, ',', '.') . ", Selisih: Rp " . number_format($difference, 0, ',', '.') . ".",
                    'occurred_at' => now(),
                ]);
            }

            return $shift;
        });
    }

    /**
     * Checkout POS sale atomically with idempotency protection, floor-price verification,
     * stock on-hand deduction, and PURPLE stock movement logging.
     */
    public function checkout(array $data, User $cashier): Sale
    {
        // 1. Idempotency Check
        $idempotencyKey = $data['idempotency_key'] ?? null;
        if ($idempotencyKey) {
            $existing = Sale::with(['items.product.baseUom', 'items.productUom.uom', 'payments', 'location', 'cashier'])
                ->where('idempotency_key', $idempotencyKey)
                ->first();
            if ($existing) {
                return $existing;
            }
        }

        $locationId = (int) $data['location_id'];
        $location = Location::findOrFail($locationId);

        // 2. Ensure active shift
        $activeShift = $this->getActiveShift($cashier, $location);
        if (!$activeShift && !($cashier->isSuperAdmin() || $cashier->isOwner())) {
            throw new \DomainException("Anda belum membuka shift kasir di toko {$location->name}. Harap buka shift terlebih dahulu sebelum melayani penjualan.");
        }

        return DB::transaction(function () use ($data, $cashier, $location, $activeShift, $idempotencyKey) {
            $itemsData = collect($data['items'] ?? []);
            if ($itemsData->isEmpty()) {
                throw new \DomainException("Keranjang belanja kasir masih kosong.");
            }

            // Sort by product_id to prevent database deadlocks on concurrent checkouts
            $sortedItems = $itemsData->sortBy('product_id')->values();

            $totalSubtotal = 0.0;
            $totalCogs = 0.0;
            $preparedLines = [];

            // 3. Pre-validate stock and floor prices with lockForUpdate
            foreach ($sortedItems as $row) {
                $productId = (int) $row['product_id'];
                $productUomId = (int) $row['product_uom_id'];
                $qtyInput = (float) $row['quantity'];
                $unitPrice = (float) $row['unit_price'];

                if ($qtyInput <= 0) {
                    throw new \DomainException("Jumlah barang yang dijual harus lebih besar dari 0.");
                }

                $product = Product::with('baseUom')->findOrFail($productId);
                $productUom = ProductUom::with('uom')->where('product_id', $productId)->findOrFail($productUomId);

                $factor = (float) $productUom->factor_to_base ?: 1.0;
                $qtyBase = round($qtyInput * $factor, 3);

                // Lock InventoryBalance
                $balance = InventoryBalance::where('location_id', $location->id)
                    ->where('product_id', $productId)
                    ->lockForUpdate()
                    ->first();

                $available = $balance ? (float) $balance->available_qty : 0.0;
                if ($available < $qtyBase) {
                    throw new \DomainException("Stok di toko tidak mencukupi untuk '{$product->name}'. Tersedia: " . number_format($available, 2) . " {$product->baseUom->symbol}, Dibutuhkan: " . number_format($qtyBase, 2) . " {$product->baseUom->symbol}.");
                }

                // Floor Price Validation
                $pricingLimits = $this->pricingService->validateSellingPrice($product, $location, $unitPrice, 'RETAIL');

                $lineSubtotal = round($qtyInput * $unitPrice, 2);
                $unitCost = $balance ? (float) $balance->avg_unit_cost : (float) ($product->last_purchase_price ?? 0.0);
                $lineCogs = round($qtyBase * $unitCost, 2);

                $totalSubtotal += $lineSubtotal;
                $totalCogs += $lineCogs;

                $preparedLines[] = [
                    'product' => $product,
                    'product_uom' => $productUom,
                    'qty_input' => $qtyInput,
                    'factor' => $factor,
                    'qty_base' => $qtyBase,
                    'unit_price' => $unitPrice,
                    'floor_price' => $pricingLimits['effective_min_price'],
                    'unit_cost' => $unitCost,
                    'subtotal' => $lineSubtotal,
                    'cogs' => $lineCogs,
                    'balance' => $balance,
                ];
            }

            $discountAmount = round((float) ($data['discount_amount'] ?? 0.0), 2);
            $taxAmount = round((float) ($data['tax_amount'] ?? 0.0), 2);
            $grandTotal = round($totalSubtotal - $discountAmount + $taxAmount, 2);

            $paymentMethod = $data['payment_method'] ?? 'CASH';
            $paidAmount = round((float) ($data['paid_amount'] ?? $grandTotal), 2);

            if ($paidAmount < $grandTotal) {
                throw new \DomainException("Jumlah uang pembayaran kurang! Total belanja: Rp " . number_format($grandTotal, 0, ',', '.') . ", Uang diterima: Rp " . number_format($paidAmount, 0, ',', '.'));
            }

            $changeAmount = round(max(0, $paidAmount - $grandTotal), 2);

            // Generate POS sale number
            $prefix = 'POS-' . date('Ymd') . '-';
            $lastSale = Sale::where('sale_number', 'LIKE', $prefix . '%')
                ->orderByDesc('id')
                ->first();
            $nextSeq = $lastSale ? ((int) substr($lastSale->sale_number, -4)) + 1 : 1;
            $saleNumber = $prefix . str_pad((string) $nextSeq, 4, '0', STR_PAD_LEFT);

            // Create Sale Header
            $sale = Sale::create([
                'sale_number' => $saleNumber,
                'idempotency_key' => $idempotencyKey ?: (string) Str::uuid(),
                'location_id' => $location->id,
                'cash_shift_id' => $activeShift?->id,
                'cashier_user_id' => $cashier->id,
                'member_id' => $data['member_id'] ?? null,
                'customer_name' => $data['customer_name'] ?? 'Pelanggan Umum',
                'status' => 'POSTED',
                'subtotal' => $totalSubtotal,
                'discount_amount' => $discountAmount,
                'tax_amount' => $taxAmount,
                'total_amount' => $grandTotal,
                'cogs_total' => $totalCogs,
                'payment_method' => $paymentMethod,
                'payment_status' => 'PAID',
                'paid_amount' => $paidAmount,
                'change_amount' => $changeAmount,
                'posted_at' => now(),
            ]);

            // Create Stock Operation Header for POS Outbound
            $operation = StockOperation::create([
                'operation_uuid' => (string) Str::uuid(),
                'operation_type' => 'SALE_POS',
                'document_type' => 'SALE',
                'document_id' => $sale->id,
                'document_number' => $sale->sale_number,
                'location_id' => $location->id,
                'actor_user_id' => $cashier->id,
                'posted_at' => now(),
                'notes' => "Penjualan Kasir POS Struk {$sale->sale_number}",
            ]);

            // Create Line Items, Deduct Balances, and Record PURPLE Movements
            foreach ($preparedLines as $line) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $line['product']->id,
                    'product_uom_id' => $line['product_uom']->id,
                    'quantity_input' => $line['qty_input'],
                    'factor_snapshot' => $line['factor'],
                    'quantity_base' => $line['qty_base'],
                    'unit_price' => $line['unit_price'],
                    'floor_price_snapshot' => $line['floor_price'],
                    'unit_cost_snapshot' => $line['unit_cost'],
                    'subtotal' => $line['subtotal'],
                    'cogs_amount' => $line['cogs'],
                ]);

                // Deduct Balance
                $bal = $line['balance'];
                $bal->on_hand_qty = round((float) $bal->on_hand_qty - $line['qty_base'], 3);
                $bal->version += 1;
                $bal->save();

                // Record PURPLE stock movement (#8B5CF6)
                StockMovement::create([
                    'operation_id' => $operation->id,
                    'location_id' => $location->id,
                    'product_id' => $line['product']->id,
                    'delta_qty_base' => -$line['qty_base'],
                    'unit_cost_snapshot' => $line['unit_cost'],
                    'amount_value' => $line['cogs'],
                    'balance_after_qty' => $bal->on_hand_qty,
                    'movement_type' => 'SALE_POS',
                    'semantic_status' => 'PURPLE',
                    'occurred_at' => now(),
                ]);
            }

            // Record Payment
            Payment::create([
                'payment_number' => 'PAY-' . date('Ymd') . '-' . strtoupper(Str::random(5)),
                'sale_id' => $sale->id,
                'payment_method' => $paymentMethod,
                'amount' => $grandTotal,
                'reference_code' => $data['reference_code'] ?? null,
                'received_by' => $cashier->id,
                'paid_at' => now(),
            ]);

            return $sale->load(['items.product.baseUom', 'items.productUom.uom', 'payments', 'location', 'cashier']);
        });
    }

    /**
     * Void / Reverse a posted sale atomically: restores inventory balance,
     * logs RED stock movement reversal, and publishes RED AnomalyEvent.
     */
    public function voidSale(Sale $sale, User $actor, string $reason): Sale
    {
        return DB::transaction(function () use ($sale, $actor, $reason) {
            $sale = Sale::with(['items.product'])->where('id', $sale->id)->lockForUpdate()->firstOrFail();

            if ($sale->status === 'VOIDED') {
                throw new \DomainException("Transaksi {$sale->sale_number} sudah dibatalkan sebelumnya.");
            }

            $sale->update([
                'status' => 'VOIDED',
                'payment_status' => 'REFUNDED',
                'voided_at' => now(),
                'voided_by' => $actor->id,
                'void_reason' => $reason,
            ]);

            // Create Reversal Stock Operation Header
            $operation = StockOperation::create([
                'operation_uuid' => (string) Str::uuid(),
                'operation_type' => 'SALE_VOID',
                'document_type' => 'SALE',
                'document_id' => $sale->id,
                'document_number' => $sale->sale_number,
                'location_id' => $sale->location_id,
                'actor_user_id' => $actor->id,
                'posted_at' => now(),
                'notes' => "Reversal / Pembatalan Struk {$sale->sale_number}. Alasan: {$reason}",
            ]);

            // Restore on-hand stock and log RED movement
            foreach ($sale->items as $item) {
                $balance = InventoryBalance::where('location_id', $sale->location_id)
                    ->where('product_id', $item->product_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $balance->on_hand_qty = round((float) $balance->on_hand_qty + (float) $item->quantity_base, 3);
                $balance->version += 1;
                $balance->save();

                // Record RED movement (#EF4444)
                StockMovement::create([
                    'operation_id' => $operation->id,
                    'location_id' => $sale->location_id,
                    'product_id' => $item->product_id,
                    'delta_qty_base' => +(float) $item->quantity_base,
                    'unit_cost_snapshot' => (float) $item->unit_cost_snapshot,
                    'amount_value' => (float) $item->cogs_amount,
                    'balance_after_qty' => $balance->on_hand_qty,
                    'movement_type' => 'SALE_VOID',
                    'semantic_status' => 'RED',
                    'occurred_at' => now(),
                ]);
            }

            // Publish RED Anomaly Event for Owner visibility
            AnomalyEvent::create([
                'severity' => 'RED',
                'kind' => 'SALE_VOID',
                'location_id' => $sale->location_id,
                'actor_user_id' => $actor->id,
                'reference_type' => Sale::class,
                'reference_id' => $sale->id,
                'reference_number' => $sale->sale_number,
                'amount' => $sale->total_amount,
                'title' => "Pembatalan Struk POS (Void): {$sale->sale_number}",
                'description' => "Petugas {$actor->name} membatalkan struk penjualan senilai Rp " . number_format($sale->total_amount, 0, ',', '.') . ". Alasan: '{$reason}'. Stok telah dikembalikan ke rak.",
                'occurred_at' => now(),
            ]);

            return $sale;
        });
    }
}
