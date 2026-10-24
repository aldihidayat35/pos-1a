<?php

namespace App\Services;

use App\Models\AnomalyEvent;
use App\Models\InventoryBalance;
use App\Models\Location;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\StockOperation;
use App\Models\StockOpname;
use App\Models\StockOpnameItem;
use App\Models\User;
use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StockOpnameService
{
    /**
     * Create a new Stock Opname Session with frozen system snapshots
     */
    public function createSession(Location $location, User $creator, array $params = []): StockOpname
    {
        $scopeType = $params['scope_type'] ?? StockOpname::SCOPE_ALL;
        $categoryId = !empty($params['category_id']) ? (int) $params['category_id'] : null;
        $productIds = $params['product_ids'] ?? [];

        return DB::transaction(function () use ($location, $creator, $scopeType, $categoryId, $productIds, $params) {
            // 1. Generate unique collision-proof Opname Number
            $prefix = 'OPN-' . date('Ymd') . '-';
            $lastOpname = StockOpname::where('opname_number', 'LIKE', $prefix . '%')
                ->orderByDesc('opname_number')
                ->first();
            $nextSeq = $lastOpname ? ((int) substr($lastOpname->opname_number, -4)) + 1 : 1;
            do {
                $opnameNumber = $prefix . str_pad((string) $nextSeq, 4, '0', STR_PAD_LEFT);
                $nextSeq++;
            } while (StockOpname::where('opname_number', $opnameNumber)->exists());

            // 2. Query Products in scope
            $productsQuery = Product::where('is_active', true)->where('track_stock', true);

            if ($scopeType === StockOpname::SCOPE_CATEGORY && $categoryId) {
                $productsQuery->where('category_id', $categoryId);
            } elseif ($scopeType === StockOpname::SCOPE_CUSTOM && !empty($productIds)) {
                $productsQuery->whereIn('id', $productIds);
            }

            $products = $productsQuery->orderBy('name', 'asc')->get();

            if ($products->isEmpty()) {
                throw new DomainException('Tidak ada produk yang dapat dimasukkan ke dalam sesi opname ini.');
            }

            // 3. Create StockOpname Header
            $opname = StockOpname::create([
                'opname_number' => $opnameNumber,
                'location_id' => $location->id,
                'scope_type' => $scopeType,
                'category_id' => $categoryId,
                'status' => StockOpname::STATUS_OPEN,
                'opened_by_user_id' => $creator->id,
                'notes' => $params['notes'] ?? null,
                'started_at' => now(),
            ]);

            // 4. Freeze system quantity and unit cost snapshots
            foreach ($products as $product) {
                $balance = InventoryBalance::firstOrCreate(
                    [
                        'location_id' => $location->id,
                        'product_id' => $product->id,
                    ],
                    [
                        'on_hand_qty' => 0.000,
                        'reserved_qty' => 0.000,
                        'avg_unit_cost' => (float) ($product->cost_price ?? 0.00),
                    ]
                );

                StockOpnameItem::create([
                    'stock_opname_id' => $opname->id,
                    'product_id' => $product->id,
                    'product_uom_id' => $product->base_uom_id,
                    'system_qty_snapshot' => (float) $balance->on_hand_qty,
                    'physical_qty_counted' => null,
                    'variance_qty' => 0.000,
                    'unit_cost_snapshot' => (float) $balance->avg_unit_cost,
                    'variance_value' => 0.00,
                    'reason_code' => StockOpnameItem::REASON_NONE,
                ]);
            }

            return $opname;
        });
    }

    /**
     * Record physical count from staff (Blind count - only inputting physical count)
     */
    public function recordCount(StockOpname $opname, User $counter, array $counts): StockOpname
    {
        if ($opname->isFinalized() || $opname->isCancelled()) {
            throw new DomainException("Sesi opname #{$opname->opname_number} tidak dapat dihitung lagi karena berstatus {$opname->status}.");
        }

        return DB::transaction(function () use ($opname, $counter, $counts) {
            $countedItemsCount = 0;
            $discrepancyCount = 0;
            $totalVarianceVal = 0.00;
            $totalLossVal = 0.00;

            foreach ($counts as $itemId => $countedQty) {
                if ($countedQty === '' || $countedQty === null) {
                    continue;
                }

                $qty = (float) $countedQty;
                if ($qty < 0) {
                    throw new DomainException('Kuantitas fisik tidak boleh bernilai negatif.');
                }

                $item = StockOpnameItem::where('id', $itemId)
                    ->where('stock_opname_id', $opname->id)
                    ->firstOrFail();

                $systemQty = (float) $item->system_qty_snapshot;
                $diffQty = round($qty - $systemQty, 3);
                $diffVal = round($diffQty * (float) $item->unit_cost_snapshot, 2);

                $item->update([
                    'physical_qty_counted' => $qty,
                    'variance_qty' => $diffQty,
                    'variance_value' => $diffVal,
                    'counter_user_id' => $counter->id,
                    'counted_at' => now(),
                ]);

                $countedItemsCount++;
                if (abs($diffQty) > 0.0001) {
                    $discrepancyCount++;
                    $totalVarianceVal += $diffVal;
                    if ($diffVal < 0) {
                        $totalLossVal += abs($diffVal);
                    }
                }
            }

            // Update Header status to COUNTING
            $opname->update([
                'status' => StockOpname::STATUS_COUNTING,
                'total_items_counted' => $countedItemsCount,
                'total_items_discrepancy' => $discrepancyCount,
                'total_variance_value' => $totalVarianceVal,
                'total_loss_value' => $totalLossVal,
            ]);

            return $opname->fresh();
        });
    }

    /**
     * Move session to Reconciliation stage (Reviewing discrepancies & assigning mandatory reasons)
     */
    public function moveToReconciliation(StockOpname $opname, User $supervisor): StockOpname
    {
        if ($opname->isFinalized() || $opname->isCancelled()) {
            throw new DomainException("Sesi opname #{$opname->opname_number} sudah selesai atau dibatalkan.");
        }

        // Recalculate totals
        $items = $opname->items()->get();
        $countedCount = $items->whereNotNull('physical_qty_counted')->count();
        $discrepancyCount = 0;
        $totalVarianceVal = 0.00;
        $totalLossVal = 0.00;

        foreach ($items as $item) {
            if ($item->physical_qty_counted !== null) {
                $diffQty = (float) $item->variance_qty;
                $diffVal = (float) $item->variance_value;
                if (abs($diffQty) > 0.0001) {
                    $discrepancyCount++;
                    $totalVarianceVal += $diffVal;
                    if ($diffVal < 0) {
                        $totalLossVal += abs($diffVal);
                    }
                }
            }
        }

        $opname->update([
            'status' => StockOpname::STATUS_RECONCILIATION,
            'reconciled_at' => now(),
            'total_items_counted' => $countedCount,
            'total_items_discrepancy' => $discrepancyCount,
            'total_variance_value' => $totalVarianceVal,
            'total_loss_value' => $totalLossVal,
        ]);

        return $opname->fresh();
    }

    /**
     * Save reconciliation reasons for discrepancy items
     */
    public function saveReconcileReasons(StockOpname $opname, array $reasons): StockOpname
    {
        if ($opname->isFinalized() || $opname->isCancelled()) {
            throw new DomainException("Tidak dapat mengubah alasan karena sesi opname sudah selesai.");
        }

        DB::transaction(function () use ($opname, $reasons) {
            foreach ($reasons as $itemId => $data) {
                $reasonCode = $data['reason_code'] ?? StockOpnameItem::REASON_NONE;
                $notes = $data['notes'] ?? null;

                StockOpnameItem::where('id', $itemId)
                    ->where('stock_opname_id', $opname->id)
                    ->update([
                        'reason_code' => $reasonCode,
                        'notes' => $notes,
                    ]);
            }
        });

        return $opname->fresh();
    }

    /**
     * Finalize Stock Opname atomically:
     * - Adjust inventory balances to verified physical count
     * - Write append-only stock movement ledger (Red if negative variance)
     * - Emit AnomalyEvent (RED) if negative discrepancies exist
     * - Lock session permanently
     */
    public function finalize(StockOpname $opname, User $finalizer, ?string $notes = null): StockOpname
    {
        // 1. Guard against owner mutation (Owner is read-only)
        if ($finalizer->isOwner()) {
            throw new DomainException('Akses Ditolak: Role Owner hanya memiliki akses baca (read-only) dan tidak diizinkan memfinalisasi opname.');
        }

        if ($opname->status === StockOpname::STATUS_FINALIZED) {
            throw new DomainException("Sesi opname #{$opname->opname_number} sudah pernah difinalisasi sebelumnya (Double finalization blocked).");
        }

        if ($opname->status === StockOpname::STATUS_CANCELLED) {
            throw new DomainException("Sesi opname #{$opname->opname_number} telah dibatalkan.");
        }

        return DB::transaction(function () use ($opname, $finalizer, $notes) {
            // Lock opname row
            $lockedOpname = StockOpname::where('id', $opname->id)->lockForUpdate()->firstOrFail();

            if ($lockedOpname->status === StockOpname::STATUS_FINALIZED) {
                throw new DomainException("Sesi opname #{$lockedOpname->opname_number} sudah pernah difinalisasi sebelumnya.");
            }

            // Fetch items
            $items = $lockedOpname->items()->with('product')->orderBy('product_id', 'asc')->get();

            // Check if any items with discrepancy lack a valid reason_code
            foreach ($items as $item) {
                if ($item->physical_qty_counted !== null && abs((float) $item->variance_qty) > 0.0001) {
                    if (empty($item->reason_code) || $item->reason_code === StockOpnameItem::REASON_NONE) {
                        throw new DomainException(
                            "Selisih barang '{$item->product->name}' (" . number_format($item->variance_qty, 2) . ") wajib memiliki alasan selisih sebelum finalisasi."
                        );
                    }
                }
            }

            // Create StockOperation Header for audit
            $operation = StockOperation::create([
                'operation_uuid' => (string) Str::uuid(),
                'operation_type' => 'OPNAME_ADJUSTMENT',
                'document_type' => 'STOCK_OPNAME',
                'document_id' => $lockedOpname->id,
                'document_number' => $lockedOpname->opname_number,
                'location_id' => $lockedOpname->location_id,
                'actor_user_id' => $finalizer->id,
                'posted_at' => now(),
                'notes' => $notes ?? "Finalisasi penyesuaian stok opname #{$lockedOpname->opname_number}",
            ]);

            $totalVarianceVal = 0.00;
            $totalLossVal = 0.00;
            $discrepancyCount = 0;
            $countedCount = 0;

            foreach ($items as $item) {
                if ($item->physical_qty_counted === null) {
                    continue;
                }

                $countedCount++;
                $physicalQty = (float) $item->physical_qty_counted;

                // Lock inventory balance row
                $balance = InventoryBalance::where('location_id', $lockedOpname->location_id)
                    ->where('product_id', $item->product_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $currentOnHand = (float) $balance->on_hand_qty;
                $deltaQty = round($physicalQty - $currentOnHand, 3);
                $unitCost = (float) $balance->avg_unit_cost;
                $diffValue = round($deltaQty * $unitCost, 2);

                if (abs($deltaQty) > 0.0001) {
                    $discrepancyCount++;
                    $totalVarianceVal += $diffValue;

                    // Update on_hand_qty to physical count
                    $balance->on_hand_qty = max(0.000, $physicalQty);
                    $balance->save();

                    // Determine semantic status and color code
                    // Merah (#EF4444) for negative variance (loss/shrinkage)
                    // Biru (#3B82F6) for positive adjustment (found extra)
                    $isNegative = $deltaQty < 0;
                    $semanticStatus = $isNegative ? 'RED' : 'BLUE';

                    if ($isNegative) {
                        $totalLossVal += abs($diffValue);
                    }

                    // Record ledger movement
                    StockMovement::create([
                        'operation_id' => $operation->id,
                        'location_id' => $lockedOpname->location_id,
                        'product_id' => $item->product_id,
                        'delta_qty_base' => $deltaQty,
                        'unit_cost_snapshot' => $unitCost,
                        'amount_value' => abs($diffValue),
                        'balance_after_qty' => $balance->on_hand_qty,
                        'movement_type' => 'OPNAME_ADJ',
                        'semantic_status' => $semanticStatus,
                        'occurred_at' => now(),
                    ]);

                    // If negative variance, emit AnomalyEvent (RED)
                    if ($isNegative) {
                        AnomalyEvent::create([
                            'severity' => 'RED',
                            'kind' => 'OPNAME_DISCREPANCY',
                            'location_id' => $lockedOpname->location_id,
                            'actor_user_id' => $finalizer->id,
                            'reference_type' => 'STOCK_OPNAME',
                            'reference_id' => $lockedOpname->id,
                            'reference_number' => $lockedOpname->opname_number,
                            'amount' => abs($diffValue),
                            'title' => "Selisih Kurang Opname: {$item->product->name}",
                            'description' => "Selisih fisik " . number_format($deltaQty, 2) . " {$item->product->baseUom?->code}. " .
                                "Alasan: {$item->reason_label}. Catatan: " . ($item->notes ?? 'Tidak ada catatan tambahan.'),
                            'occurred_at' => now(),
                        ]);
                    }
                }
            }

            // Update StockOpname Header
            $lockedOpname->update([
                'status' => StockOpname::STATUS_FINALIZED,
                'finalized_by_user_id' => $finalizer->id,
                'finalized_at' => now(),
                'total_items_counted' => $countedCount,
                'total_items_discrepancy' => $discrepancyCount,
                'total_variance_value' => $totalVarianceVal,
                'total_loss_value' => $totalLossVal,
                'notes' => $notes ?? $lockedOpname->notes,
            ]);

            return $lockedOpname->fresh();
        });
    }

    /**
     * Cancel an open session
     */
    public function cancelSession(StockOpname $opname, User $user, ?string $reason = null): StockOpname
    {
        if ($opname->isFinalized()) {
            throw new DomainException('Sesi opname yang sudah difinalisasi tidak dapat dibatalkan.');
        }

        $opname->update([
            'status' => StockOpname::STATUS_CANCELLED,
            'notes' => trim(($opname->notes ? $opname->notes . " | " : "") . "Dibatalkan oleh {$user->name}: " . ($reason ?? 'Tanpa alasan.')),
        ]);

        return $opname->fresh();
    }
}
