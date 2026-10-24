<?php

namespace Database\Seeders;

use App\Models\AnomalyEvent;
use App\Models\EmergencyPurchase;
use App\Models\Location;
use App\Models\PriceRule;
use App\Models\PricingPolicy;
use App\Models\Product;
use App\Models\ProductUom;
use App\Models\User;
use App\Services\EmergencyPurchaseService;
use App\Services\InventoryService;
use App\Services\TransferService;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class TransferEmergencyPricingSeeder extends Seeder
{
    /**
     * Run the database seeds for Transfer, Emergency, and Pricing.
     */
    public function run(): void
    {
        $warehouse = Location::where('code', 'WH-CENTRAL')->first();
        $store = Location::where('code', 'STR-PASAR-1')->first();
        $transit = Location::where('code', 'LOC-TRANSIT')->first();

        $superAdmin = User::where('username', 'superadmin')->first() ?? User::first();
        $kepalaGudang = User::where('username', 'kepalagudang')->first() ?? $superAdmin;
        $kepalaToko = User::where('username', 'kepalatoko')->first() ?? $superAdmin;
        $stafToko = User::where('username', 'staftoko')->first() ?? $superAdmin;

        $transferService = app(TransferService::class);
        $emergencyService = app(EmergencyPurchaseService::class);
        $inventoryService = app(InventoryService::class);

        // 1. Seed Pricing Policies (Floor Markup)
        PricingPolicy::firstOrCreate(
            ['scope' => 'GLOBAL', 'channel' => 'RETAIL'],
            [
                'min_markup_type' => 'PERCENTAGE',
                'min_markup_value' => 8.00, // 8% markup over HPP
                'effective_from' => now()->startOfYear(),
                'is_active' => true,
            ]
        );

        PricingPolicy::firstOrCreate(
            ['scope' => 'GLOBAL', 'channel' => 'B2B'],
            [
                'min_markup_type' => 'PERCENTAGE',
                'min_markup_value' => 4.00, // 4% markup for wholesale
                'effective_from' => now()->startOfYear(),
                'is_active' => true,
            ]
        );

        // 2. Seed Price Rules (Market Price Guidelines)
        $beras = Product::where('sku', 'SKU-BRS-001')->first();
        $minyak = Product::where('sku', 'SKU-MYK-001')->first();
        $gula = Product::where('sku', 'SKU-GLA-001')->first();
        $bawang = Product::where('sku', 'SKU-SYR-001')->first();

        if ($beras) {
            PriceRule::firstOrCreate(
                ['product_id' => $beras->id, 'channel' => 'RETAIL'],
                [
                    'list_price' => 15000.00,
                    'market_min_price' => 13500.00,
                    'market_max_price' => 17000.00,
                    'effective_from' => now()->startOfYear(),
                    'is_active' => true,
                ]
            );
        }

        if ($minyak) {
            PriceRule::firstOrCreate(
                ['product_id' => $minyak->id, 'channel' => 'RETAIL'],
                [
                    'list_price' => 18000.00,
                    'market_min_price' => 16000.00,
                    'market_max_price' => 21000.00,
                    'effective_from' => now()->startOfYear(),
                    'is_active' => true,
                ]
            );
        }

        if ($gula) {
            PriceRule::firstOrCreate(
                ['product_id' => $gula->id, 'channel' => 'RETAIL'],
                [
                    'list_price' => 17500.00,
                    'market_min_price' => 16500.00,
                    'market_max_price' => 19500.00,
                    'effective_from' => now()->startOfYear(),
                    'is_active' => true,
                ]
            );
        }

        if ($bawang) {
            PriceRule::firstOrCreate(
                ['product_id' => $bawang->id, 'channel' => 'RETAIL'],
                [
                    'list_price' => 38000.00,
                    'market_min_price' => 32000.00,
                    'market_max_price' => 45000.00,
                    'effective_from' => now()->startOfYear(),
                    'is_active' => true,
                ]
            );
        }

        // 3. Demo Active Stock Transfer: Warehouse -> Store (Status: DISPATCHED / In Transit)
        if ($warehouse && $store && $beras && $minyak) {
            $berasBaseUom = ProductUom::where('product_id', $beras->id)->where('is_base', true)->first();
            $minyakBaseUom = ProductUom::where('product_id', $minyak->id)->where('is_base', true)->first();

            if ($berasBaseUom && $minyakBaseUom) {
                $transfer = $transferService->createTransfer([
                    'from_location_id' => $warehouse->id,
                    'to_location_id' => $store->id,
                    'notes' => 'Pasokan rutin pagi untuk display rak Toko Pasar Raya Blok A via Armada Mobil Pick-up B-8821.',
                    'items' => [
                        [
                            'product_id' => $beras->id,
                            'product_uom_id' => $berasBaseUom->id,
                            'quantity' => 100.000, // 100 kg
                        ],
                        [
                            'product_id' => $minyak->id,
                            'product_uom_id' => $minyakBaseUom->id,
                            'quantity' => 50.000, // 50 L
                        ],
                    ],
                ], $kepalaGudang);

                // Dispatch to transit
                $transferService->dispatchTransfer($transfer, $kepalaGudang);
            }
        }

        // 4. Demo Emergency Purchase at Store (Status: ORANGE Anomaly)
        if ($store && $bawang && $stafToko) {
            $bawangUom = ProductUom::where('product_id', $bawang->id)->first();
            if ($bawangUom) {
                // Ensure sample photo exists
                Storage::disk('public')->makeDirectory('emergency_receipts');
                $samplePath = 'emergency_receipts/nota_bawang_sample.jpg';
                if (!Storage::disk('public')->exists($samplePath)) {
                    Storage::disk('public')->put($samplePath, 'FAKEDATA_RECEIPT_IMAGE');
                }

                $emergencyPurchase = EmergencyPurchase::create([
                    'purchase_number' => 'EMG-' . date('Ymd') . '-0001',
                    'location_id' => $store->id,
                    'product_id' => $bawang->id,
                    'product_uom_id' => $bawangUom->id,
                    'qty_input' => 20.000,
                    'factor_snapshot' => 1.000000,
                    'qty_base' => 20.000,
                    'unit_cost_input' => 31000.00,
                    'unit_cost_base' => 31000.000000,
                    'total_amount' => 620000.00,
                    'merchant_name' => 'Kios Sayur Berkah (H. Joko Blok B Pasar)',
                    'receipt_photo_path' => $samplePath,
                    'notes' => 'Stok bawang habis saat jam ramai pasar, beli darurat 20 kg untuk display POS.',
                    'created_by' => $stafToko->id,
                    'posted_at' => now()->subHours(2),
                ]);

                // Create stock movement (ORANGE) & Anomaly Event
                $inventoryService->recordOpeningBalance(
                    locationId: $store->id,
                    productId: $bawang->id,
                    qtyBase: 20.000,
                    unitCostBase: 31000.000000,
                    actor: $stafToko,
                    notes: 'Penerimaan Belanja Emergency Toko (H. Joko Blok B)'
                );

                AnomalyEvent::create([
                    'severity' => 'ORANGE',
                    'kind' => 'EMERGENCY_PURCHASE',
                    'location_id' => $store->id,
                    'actor_user_id' => $stafToko->id,
                    'reference_type' => EmergencyPurchase::class,
                    'reference_id' => $emergencyPurchase->id,
                    'reference_number' => $emergencyPurchase->purchase_number,
                    'amount' => 620000.00,
                    'title' => 'Pembelian Emergency Toko: Bawang Merah Brebes Super',
                    'description' => "Staf Toko membeli 20 kg dari 'Kios Sayur Berkah' seharga Rp 620.000 tanpa approval Owner. Nota fisik telah diverifikasi.",
                    'occurred_at' => now()->subHours(2),
                ]);
            }
        }
    }
}
