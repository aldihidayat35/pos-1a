<?php

namespace Tests\Feature;

use App\Models\AnomalyEvent;
use App\Models\InventoryBalance;
use App\Models\Location;
use App\Models\PriceRule;
use App\Models\PricingPolicy;
use App\Models\Product;
use App\Models\ProductUom;
use App\Models\StockMovement;
use App\Models\StockTransfer;
use App\Models\User;
use App\Services\PricingService;
use App\Services\TransferService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TransferEmergencyPricingTest extends TestCase
{
    use DatabaseTransactions;

    protected User $kepalaGudang;
    protected User $kepalaToko;
    protected User $stafToko;
    protected User $owner;
    protected Location $warehouse;
    protected Location $transitLocation;
    protected Location $store;
    protected Product $product;
    protected ProductUom $baseUom;

    protected function setUp(): void
    {
        parent::setUp();

        $this->kepalaGudang = User::where('username', 'kepalagudang')->first()
            ?? User::where('email', 'kepalagudang@pasar.local')->first();
        $this->kepalaToko = User::where('username', 'kepalatoko')->first()
            ?? User::where('email', 'kepalatoko@pasar.local')->first();
        $this->stafToko = User::where('username', 'staftoko')->first()
            ?? User::where('email', 'staftoko@pasar.local')->first();
        $this->owner = User::where('username', 'owner')->first()
            ?? User::where('email', 'owner@pasar.local')->first();

        $this->warehouse = Location::where('code', 'WH-CENTRAL')->firstOrFail();
        $this->transitLocation = Location::where('code', 'LOC-TRANSIT')->firstOrFail();
        $this->store = Location::where('code', 'STR-PASAR-1')->firstOrFail();

        $this->product = Product::with(['productUoms', 'baseUom'])->where('sku', 'SKU-BRS-001')->firstOrFail();
        $this->baseUom = $this->product->productUoms->where('is_base', true)->first()
            ?? $this->product->productUoms->first();

        Storage::fake('public');
    }

    public function test_user_can_view_transfers_list_and_create_page(): void
    {
        $response = $this->actingAs($this->kepalaGudang)->get(route('warehouse.transfers.index'));
        $response->assertStatus(200);
        $response->assertSee('Pengiriman Transfer Antar Lokasi');

        $createResponse = $this->actingAs($this->kepalaGudang)->get(route('warehouse.transfers.create'));
        $createResponse->assertStatus(200);
        $createResponse->assertSee('Kirim Transfer Barang Antar Lokasi');
    }

    public function test_transfer_dispatch_cuts_origin_and_increments_transit_stock_no_loss_or_double_count(): void
    {
        // 1. Ensure initial stock in warehouse
        $whBalance = InventoryBalance::firstOrCreate(
            ['location_id' => $this->warehouse->id, 'product_id' => $this->product->id],
            ['on_hand_qty' => 50.0, 'avg_unit_cost' => 12500.0]
        );
        $whBalance->update(['on_hand_qty' => 50.0, 'avg_unit_cost' => 12500.0]);

        $transitBalanceBefore = (float) (InventoryBalance::where('location_id', $this->transitLocation->id)
            ->where('product_id', $this->product->id)
            ->value('on_hand_qty') ?? 0.0);

        $totalStockBefore = (float) InventoryBalance::where('product_id', $this->product->id)->sum('on_hand_qty');

        // 2. Dispatch transfer of 10 units
        $payload = [
            'from_location_id' => $this->warehouse->id,
            'to_location_id' => $this->store->id,
            'notes' => 'Pengiriman beras operasional toko pasar.',
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'product_uom_id' => $this->baseUom->id,
                    'quantity' => 10.0,
                    'notes' => '10 kg beras',
                ],
            ],
        ];

        $response = $this->actingAs($this->kepalaGudang)->post(route('warehouse.transfers.store'), $payload);
        $response->assertRedirect();

        // 3. Verify balances
        $whBalanceAfter = (float) InventoryBalance::where('location_id', $this->warehouse->id)
            ->where('product_id', $this->product->id)
            ->value('on_hand_qty');
        $this->assertEquals(40.0, $whBalanceAfter);

        $transitBalanceAfter = (float) InventoryBalance::where('location_id', $this->transitLocation->id)
            ->where('product_id', $this->product->id)
            ->value('on_hand_qty');
        $this->assertEquals($transitBalanceBefore + 10.0, $transitBalanceAfter);

        // Conservation of mass: Total stock across entire system is identical
        $totalStockAfter = (float) InventoryBalance::where('product_id', $this->product->id)->sum('on_hand_qty');
        $this->assertEquals($totalStockBefore, $totalStockAfter);

        // Verify Blue stock movement at transit
        $movement = StockMovement::where('product_id', $this->product->id)
            ->where('location_id', $this->transitLocation->id)
            ->where('movement_type', 'TRANSFER_TRANSIT_IN')
            ->latest('id')
            ->first();

        $this->assertNotNull($movement);
        $this->assertEquals('BLUE', $movement->semantic_status);
        $this->assertEquals('#3B82F6', $movement->semantic_color);
    }

    public function test_cannot_dispatch_transfer_exceeding_on_hand_balance(): void
    {
        $whBalance = InventoryBalance::firstOrCreate(
            ['location_id' => $this->warehouse->id, 'product_id' => $this->product->id],
            ['on_hand_qty' => 5.0, 'avg_unit_cost' => 12500.0]
        );
        $whBalance->update(['on_hand_qty' => 5.0]);

        $payload = [
            'from_location_id' => $this->warehouse->id,
            'to_location_id' => $this->store->id,
            'notes' => 'Transfer melebihi saldo.',
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'product_uom_id' => $this->baseUom->id,
                    'quantity' => 1000.0,
                ],
            ],
        ];

        $response = $this->actingAs($this->kepalaGudang)->post(route('warehouse.transfers.store'), $payload);
        $response->assertSessionHas('error');

        // Balance remains unchanged
        $this->assertEquals(5.0, (float) InventoryBalance::where('location_id', $this->warehouse->id)->where('product_id', $this->product->id)->value('on_hand_qty'));
    }

    public function test_store_staff_can_receive_transfer_and_updates_moving_average_hpp(): void
    {
        // 1. Prepare dispatched transfer
        /** @var TransferService $transferService */
        $transferService = app(TransferService::class);

        InventoryBalance::updateOrCreate(
            ['location_id' => $this->warehouse->id, 'product_id' => $this->product->id],
            ['on_hand_qty' => 100.0, 'avg_unit_cost' => 12000.0]
        );

        // Existing store balance: 10 kg @ Rp 10.000 (total value = Rp 100.000)
        InventoryBalance::updateOrCreate(
            ['location_id' => $this->store->id, 'product_id' => $this->product->id],
            ['on_hand_qty' => 10.0, 'avg_unit_cost' => 10000.0]
        );

        $transfer = $transferService->createTransfer([
            'from_location_id' => $this->warehouse->id,
            'to_location_id' => $this->store->id,
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'product_uom_id' => $this->baseUom->id,
                    'quantity' => 10.0,
                ],
            ],
        ], $this->kepalaGudang);

        $transferService->dispatchTransfer($transfer, $this->kepalaGudang);
        $transfer->refresh();
        $this->assertEquals('DISPATCHED', $transfer->status);

        // 2. Kepala toko receives 10 kg from transit
        $receivePayload = [
            'notes' => 'Diterima utuh tanpa selisih di toko.',
            'items' => [
                [
                    'item_id' => $transfer->items->first()->id,
                    'qty_received' => 10.0,
                ],
            ],
        ];

        $response = $this->actingAs($this->kepalaToko)->post(
            route('store.transfers.receive', $transfer->id),
            $receivePayload
        );

        $response->assertRedirect();
        $transfer->refresh();
        $this->assertEquals('RECEIVED', $transfer->status);

        // Store balance should be 10 (existing) + 10 (new) = 20 kg
        $storeBal = InventoryBalance::where('location_id', $this->store->id)
            ->where('product_id', $this->product->id)
            ->first();

        $this->assertEquals(20.0, (float) $storeBal->on_hand_qty);

        // Moving Average Calculation:
        // (10 kg * 10.000 + 10 kg * 12.000) / 20 kg = 220.000 / 20 = 11.000
        $this->assertEquals(11000.0, (float) $storeBal->avg_unit_cost);
    }

    public function test_emergency_purchase_requires_mandatory_photo_proof(): void
    {
        $payload = [
            'location_id' => $this->store->id,
            'product_id' => $this->product->id,
            'product_uom_id' => $this->baseUom->id,
            'qty_input' => 5.0,
            'unit_cost_input' => 13000.0,
            'merchant_name' => 'Kios Berkah Blok B-01',
            // No receipt_photo provided
        ];

        $response = $this->actingAs($this->stafToko)->post(route('store.emergency.store'), $payload);
        $response->assertSessionHasErrors(['receipt_photo']);
    }

    public function test_emergency_purchase_directly_increases_stock_and_creates_orange_anomaly(): void
    {
        $initialStoreQty = (float) (InventoryBalance::where('location_id', $this->store->id)
            ->where('product_id', $this->product->id)
            ->value('on_hand_qty') ?? 0.0);

        $file = UploadedFile::fake()->image('nota_struk_pasar.jpg', 600, 800);

        $payload = [
            'location_id' => $this->store->id,
            'product_id' => $this->product->id,
            'product_uom_id' => $this->baseUom->id,
            'qty_input' => 6.0,
            'unit_cost_input' => 14000.0,
            'merchant_name' => 'Kios Berkah Sayur',
            'receipt_photo' => $file,
            'notes' => 'Beli darurat karena stok di rak habis diserbu pembeli.',
        ];

        $response = $this->actingAs($this->stafToko)->post(route('store.emergency.store'), $payload);
        $response->assertRedirect();

        // 1. Store stock increased directly
        $finalStoreQty = (float) InventoryBalance::where('location_id', $this->store->id)
            ->where('product_id', $this->product->id)
            ->value('on_hand_qty');
        $this->assertEquals($initialStoreQty + 6.0, $finalStoreQty);

        // 2. Orange stock movement logged (#F59E0B)
        $movement = StockMovement::where('product_id', $this->product->id)
            ->where('location_id', $this->store->id)
            ->where('movement_type', 'EMERGENCY_PURCHASE')
            ->latest('id')
            ->first();

        $this->assertNotNull($movement);
        $this->assertEquals('ORANGE', $movement->semantic_status);
        $this->assertEquals('#F59E0B', $movement->semantic_color);

        // 3. Orange Anomaly Event created automatically
        $anomaly = AnomalyEvent::where('location_id', $this->store->id)
            ->where('kind', 'EMERGENCY_PURCHASE')
            ->latest('id')
            ->first();

        $this->assertNotNull($anomaly);
        $this->assertEquals('ORANGE', $anomaly->severity);
        $this->assertStringContainsString('Pembelian Emergency Toko', $anomaly->title);
    }

    public function test_pricing_service_calculates_floor_price_and_blocks_underpriced_sales(): void
    {
        /** @var PricingService $pricingService */
        $pricingService = app(PricingService::class);

        // Set up active policy: Retail 10% markup
        PricingPolicy::updateOrCreate(
            ['channel' => 'RETAIL'],
            [
                'name' => 'Retail Toko',
                'min_markup_type' => 'PERCENTAGE',
                'min_markup_value' => 10.0,
                'is_active' => true,
            ]
        );

        // Set cost to Rp 10.000 in InventoryBalance at store
        InventoryBalance::updateOrCreate(
            ['location_id' => $this->store->id, 'product_id' => $this->product->id],
            ['on_hand_qty' => 50.0, 'avg_unit_cost' => 10000.0]
        );

        // Set market min to Rp 10.500 in PriceRule
        PriceRule::updateOrCreate(
            ['product_id' => $this->product->id, 'channel' => 'RETAIL'],
            [
                'list_price' => 13500.0,
                'market_min_price' => 10500.0,
                'market_max_price' => 16000.0,
                'effective_from' => now()->subDay()->toDateString(),
                'is_active' => true,
            ]
        );

        $limits = $pricingService->calculatePricingLimits($this->product, $this->store, 'RETAIL');

        // Current Unit Cost = 10.000
        // Markup 10% = 1.000
        // Floor Price = 10.000 + 1.000 = 11.000
        // Effective Min = max(10.500, 11.000) = 11.000
        $this->assertEquals(10000.0, $limits['current_unit_cost']);
        $this->assertEquals(1000.0, $limits['markup_amount']);
        $this->assertEquals(11000.0, $limits['floor_price']);
        $this->assertEquals(11000.0, $limits['effective_min_price']);

        // 1. Underpriced sale (e.g. Rp 10.800 < 11.000) must throw DomainException
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('berada di bawah batas minimum');
        $pricingService->validateSellingPrice($this->product, $this->store, 10800.0, 'RETAIL');
    }

    public function test_pricing_endpoint_returns_json_validation_status(): void
    {
        // Set unit cost at location
        InventoryBalance::updateOrCreate(
            ['location_id' => $this->store->id, 'product_id' => $this->product->id],
            ['on_hand_qty' => 50.0, 'avg_unit_cost' => 10000.0]
        );

        PriceRule::updateOrCreate(
            ['product_id' => $this->product->id, 'channel' => 'RETAIL'],
            [
                'list_price' => 13500.0,
                'market_min_price' => 10000.0,
                'market_max_price' => 16000.0,
                'effective_from' => now()->subDay()->toDateString(),
                'is_active' => true,
            ]
        );

        // Attempt below floor price: should return 422 JSON
        $rejectResponse = $this->actingAs($this->stafToko)->postJson(route('master.pricing.check'), [
            'product_id' => $this->product->id,
            'channel' => 'RETAIL',
            'offered_price' => 9500,
        ]);

        $rejectResponse->assertStatus(422);
        $rejectResponse->assertJson([
            'valid' => false,
        ]);

        // Attempt above floor price: should return 200 JSON
        $acceptResponse = $this->actingAs($this->stafToko)->postJson(route('master.pricing.check'), [
            'product_id' => $this->product->id,
            'channel' => 'RETAIL',
            'offered_price' => 15000,
        ]);

        $acceptResponse->assertStatus(200);
        $acceptResponse->assertJson([
            'valid' => true,
        ]);
    }
}
