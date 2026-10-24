<?php

namespace Database\Seeders;

use App\Models\InventoryBalance;
use App\Models\Location;
use App\Models\Product;
use App\Models\StockOpname;
use App\Models\User;
use App\Services\StockOpnameService;
use Illuminate\Database\Seeder;

class StockOpnameSeeder extends Seeder
{
    /**
     * Seed demonstration Stock Opname sessions
     */
    public function run(): void
    {
        $warehouse = Location::where('code', 'WH-CENTRAL')->first() ?? Location::where('type', 'WAREHOUSE')->first();
        $store = Location::where('code', 'STR-PASAR-1')->first() ?? Location::where('type', 'STORE')->first();
        $kepalaGudang = User::where('username', 'kepalagudang')->first() ?? User::first();
        $stafGudang = User::where('username', 'stafgudang')->first() ?? User::first();
        $kepalaToko = User::where('username', 'kepalatoko')->first() ?? User::first();

        if (!$warehouse || !$store || !$kepalaGudang) {
            return;
        }

        $opnameService = app(StockOpnameService::class);

        // 1. Session 1: Sesi Terbuka / Sedang Hitung di Gudang Sentral
        if (!StockOpname::where('opname_number', 'OPN-20261024-0001')->exists()) {
            $session1 = $opnameService->createSession($warehouse, $kepalaGudang, [
                'scope_type' => StockOpname::SCOPE_ALL,
                'notes' => 'Opname rutin berkala mingguan gudang sentral.',
            ]);

            // Staff inputs partial blind counts
            $items = $session1->items()->take(3)->get();
            $counts = [];
            foreach ($items as $item) {
                // Input count close to system
                $counts[$item->id] = (float) $item->system_qty_snapshot;
            }
            if (!empty($counts)) {
                $opnameService->recordCount($session1, $stafGudang, $counts);
            }
        }

        // 2. Session 2: Sesi Final dengan Selisih di Toko Pasar Sentral
        if (!StockOpname::where('opname_number', 'OPN-20261024-0002')->exists()) {
            $products = Product::where('is_active', true)->where('track_stock', true)->take(4)->get();
            if ($products->count() >= 2) {
                // Ensure inventory balance has stock for these products in store
                foreach ($products as $p) {
                    $bal = InventoryBalance::firstOrCreate(
                        ['location_id' => $store->id, 'product_id' => $p->id],
                        ['on_hand_qty' => 50.000, 'reserved_qty' => 0.000, 'avg_unit_cost' => 12000.00]
                    );
                    if ($bal->on_hand_qty <= 0) {
                        $bal->on_hand_qty = 50.000;
                        $bal->save();
                    }
                }

                $session2 = $opnameService->createSession($store, $kepalaToko, [
                    'scope_type' => StockOpname::SCOPE_CUSTOM,
                    'product_ids' => $products->pluck('id')->toArray(),
                    'notes' => 'Audit fisik stok beras dan minyak toko pasar.',
                ]);

                // Staff records count with 1 deficit and 1 surplus
                $items2 = $session2->items()->get();
                $counts2 = [];
                foreach ($items2 as $idx => $it) {
                    if ($idx === 0) {
                        // Deficit of 3 units (Susut Alami / Rusak)
                        $counts2[$it->id] = max(0, (float) $it->system_qty_snapshot - 3.000);
                    } elseif ($idx === 1) {
                        // Surplus of 1 unit (Lebih fisik)
                        $counts2[$it->id] = (float) $it->system_qty_snapshot + 1.000;
                    } else {
                        $counts2[$it->id] = (float) $it->system_qty_snapshot;
                    }
                }
                $opnameService->recordCount($session2, $kepalaToko, $counts2);

                // Move to reconciliation
                $opnameService->moveToReconciliation($session2, $kepalaToko);

                // Assign reasons
                $reasons = [];
                foreach ($session2->items as $idx => $it) {
                    if ($idx === 0) {
                        $reasons[$it->id] = [
                            'reason_code' => 'DAMAGED_EXPIRED',
                            'notes' => 'Kemasan sobek dan tercecer saat penataan rak.',
                        ];
                    } elseif ($idx === 1) {
                        $reasons[$it->id] = [
                            'reason_code' => 'FOUND_EXTRA',
                            'notes' => 'Ditemukan 1 kardus tertinggal di lorong belakang.',
                        ];
                    }
                }
                $opnameService->saveReconcileReasons($session2, $reasons);

                // Finalize session
                $opnameService->finalize($session2, $kepalaToko, 'Disahkan oleh Kepala Toko. Kerugian diakui.');
            }
        }
    }
}
