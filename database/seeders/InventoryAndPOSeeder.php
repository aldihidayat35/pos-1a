<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\Product;
use App\Models\ProductUom;
use App\Models\Supplier;
use App\Models\User;
use App\Services\InventoryService;
use App\Services\PurchasingService;
use Illuminate\Database\Seeder;

class InventoryAndPOSeeder extends Seeder
{
    /**
     * Run the database seeds for Inventory & Purchasing.
     */
    public function run(): void
    {
        $warehouse = Location::where('code', 'WH-CENTRAL')->first();
        if (!$warehouse) {
            return;
        }

        $superAdmin = User::where('username', 'superadmin')->first()
            ?? User::where('email', 'superadmin@pasar.local')->first()
            ?? User::first();

        $inventoryService = app(InventoryService::class);
        $purchasingService = app(PurchasingService::class);

        // 1. Initial Opening Balance for Gudang Pusat
        $beras = Product::where('sku', 'SKU-BRS-001')->first();
        $minyak = Product::where('sku', 'SKU-MYK-001')->first();
        $gula = Product::where('sku', 'SKU-GLA-001')->first();

        if ($beras && $superAdmin) {
            $existing = $beras->getBalanceAt($warehouse->id);
            if (!$existing || $existing->on_hand_qty == 0) {
                $inventoryService->recordOpeningBalance(
                    locationId: $warehouse->id,
                    productId: $beras->id,
                    qtyBase: 500.000,
                    unitCostBase: 12000.000000,
                    actor: $superAdmin,
                    notes: 'Saldo Awal Migrasi Gudang Pusat (Beras Rojolele)'
                );
            }
        }

        if ($minyak && $superAdmin) {
            $existing = $minyak->getBalanceAt($warehouse->id);
            if (!$existing || $existing->on_hand_qty == 0) {
                $inventoryService->recordOpeningBalance(
                    locationId: $warehouse->id,
                    productId: $minyak->id,
                    qtyBase: 200.000,
                    unitCostBase: 14500.000000,
                    actor: $superAdmin,
                    notes: 'Saldo Awal Migrasi Gudang Pusat (Minyak Bimoli)'
                );
            }
        }

        if ($gula && $superAdmin) {
            $existing = $gula->getBalanceAt($warehouse->id);
            if (!$existing || $existing->on_hand_qty == 0) {
                $inventoryService->recordOpeningBalance(
                    locationId: $warehouse->id,
                    productId: $gula->id,
                    qtyBase: 300.000,
                    unitCostBase: 15000.000000,
                    actor: $superAdmin,
                    notes: 'Saldo Awal Migrasi Gudang Pusat (Gula Pasir)'
                );
            }
        }

        // 2. Demo Purchase Order 1: Ready to be received (Status: ISSUED)
        $sup1 = Supplier::where('supplier_code', 'SUP-001')->first();
        if ($sup1 && $beras && $minyak && $superAdmin) {
            $berasPackUom = ProductUom::where('product_id', $beras->id)->where('is_base', false)->first()
                ?? ProductUom::where('product_id', $beras->id)->first();
            $minyakPackUom = ProductUom::where('product_id', $minyak->id)->where('is_base', false)->first()
                ?? ProductUom::where('product_id', $minyak->id)->first();

            if ($berasPackUom && $minyakPackUom) {
                $purchasingService->createPurchaseOrder([
                    'supplier_id' => $sup1->id,
                    'destination_location_id' => $warehouse->id,
                    'ordered_at' => now()->subDay(),
                    'expected_delivery_at' => now()->addDays(2),
                    'notes' => 'Pasokan rutin beras premium dan minyak kemasan untuk stok buffer minggu ini.',
                    'items' => [
                        [
                            'product_id' => $beras->id,
                            'product_uom_id' => $berasPackUom->id,
                            'quantity_ordered' => 20.000, // e.g. 20 Karung @ 50kg = 1000kg
                            'unit_purchase_price' => 650000.00,
                        ],
                        [
                            'product_id' => $minyak->id,
                            'product_uom_id' => $minyakPackUom->id,
                            'quantity_ordered' => 15.000, // e.g. 15 Jerigen @ 18L = 270L
                            'unit_purchase_price' => 270000.00,
                        ],
                    ],
                ], $superAdmin);
            }
        }

        // 3. Demo Purchase Order 2: Partially Received to show Moving Average recalculation & Ledger
        $sup2 = Supplier::where('supplier_code', 'SUP-002')->first();
        if ($sup2 && $gula && $superAdmin) {
            $gulaPackUom = ProductUom::where('product_id', $gula->id)->where('is_base', false)->first()
                ?? ProductUom::where('product_id', $gula->id)->first();

            if ($gulaPackUom) {
                $po2 = $purchasingService->createPurchaseOrder([
                    'supplier_id' => $sup2->id,
                    'destination_location_id' => $warehouse->id,
                    'ordered_at' => now()->subDays(2),
                    'expected_delivery_at' => now()->addDay(),
                    'notes' => 'PO Gula Pasir Kristal Putih Pabrikasi.',
                    'items' => [
                        [
                            'product_id' => $gula->id,
                            'product_uom_id' => $gulaPackUom->id,
                            'quantity_ordered' => 20.000, // 20 Karung @ 50kg = 1.000 kg
                            'unit_purchase_price' => 780000.00, // Rp 15.600/kg
                        ],
                    ],
                ], $superAdmin);

                // Receive 10 karung partially
                $poItem = $po2->items->first();
                $purchasingService->receiveGoodsReceipt($po2, [
                    'supplier_do_number' => 'SJ-BMS-2026/10/881',
                    'notes' => 'Penerimaan tahap 1 via truk ekspedisi B-9122-XYZ (10 Karung).',
                    'items' => [
                        [
                            'purchase_order_item_id' => $poItem->id,
                            'qty_input' => 10.000,
                            'unit_cost_input' => 780000.00,
                            'batch_number' => 'LOT-GLA-' . date('Ymd') . '-01',
                            'expiry_date' => now()->addYear()->toDateString(),
                            'notes' => 'Kondisi karung utuh, segel pabrik aman, kadar air standar.',
                        ],
                    ],
                ], $superAdmin);
            }
        }
    }
}
