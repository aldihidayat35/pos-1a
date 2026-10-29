<?php

namespace Tests\Feature;

use App\Models\AnomalyEvent;
use App\Models\CashShift;
use App\Models\InventoryBalance;
use App\Models\Location;
use App\Models\PriceRule;
use App\Models\PricingPolicy;
use App\Models\Product;
use App\Models\ProductUom;
use App\Models\Sale;
use App\Models\StockMovement;
use App\Models\User;
use App\Services\PosService;
use App\Services\PricingService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PosAndCashShiftTest extends TestCase
{
    use DatabaseTransactions;

    protected User $stafToko;
    protected User $kepalaToko;
    protected User $owner;
    protected Location $store;
    protected Product $product;
    protected ProductUom $baseUom;
    protected PosService $posService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stafToko = User::where('username', 'staftoko')->first()
            ?? User::where('email', 'staftoko@pasar.local')->first();
        $this->kepalaToko = User::where('username', 'kepalatoko')->first()
            ?? User::where('email', 'kepalatoko@pasar.local')->first();
        $this->owner = User::where('username', 'owner')->first()
            ?? User::where('email', 'owner@pasar.local')->first();

        $this->store = Location::where('code', 'STR-PASAR-1')->firstOrFail();

        $this->product = Product::with(['productUoms', 'baseUom'])->where('sku', 'SKU-BRS-001')->firstOrFail();
        $this->baseUom = $this->product->productUoms->where('is_base', true)->first()
            ?? $this->product->productUoms->first();

        $this->posService = app(PosService::class);
    }

    public function test_cashier_can_access_pos_terminal_and_shifts_page(): void
    {
        $response = $this->actingAs($this->stafToko)->get(route('store.pos.terminal'));
        $response->assertStatus(200);
        $response->assertSee('Terminal Kasir POS');
        $response->assertSee('Keranjang Belanja');

        $shiftsResponse = $this->actingAs($this->stafToko)->get(route('store.shifts.index'));
        $shiftsResponse->assertStatus(200);
        $shiftsResponse->assertSee('Pengendalian Shift');
    }

    public function test_cashier_can_open_shift_with_starting_cash(): void
    {
        // Close any lingering open shifts
        CashShift::where('cashier_user_id', $this->stafToko->id)->where('status', 'OPEN')->update(['status' => 'CLOSED']);

        $response = $this->actingAs($this->stafToko)->post(route('store.shifts.open'), [
            'starting_cash' => 200000.0,
            'notes' => 'Shift Pagi Kasir Utama',
        ]);

        $response->assertRedirect();

        $activeShift = CashShift::where('cashier_user_id', $this->stafToko->id)
            ->where('status', 'OPEN')
            ->first();

        $this->assertNotNull($activeShift);
        $this->assertEquals(200000.0, (float) $activeShift->starting_cash);
    }

    public function test_checkout_deducts_inventory_and_logs_purple_movement_with_idempotency(): void
    {
        // 1. Ensure active shift
        $shift = $this->posService->openShift($this->store, $this->stafToko, 200000.0);

        // 2. Ensure initial stock
        $bal = InventoryBalance::updateOrCreate(
            ['location_id' => $this->store->id, 'product_id' => $this->product->id],
            ['on_hand_qty' => 50.0, 'avg_unit_cost' => 10000.0]
        );

        PriceRule::updateOrCreate(
            ['product_id' => $this->product->id, 'channel' => 'RETAIL'],
            [
                'list_price' => 15000.0,
                'market_min_price' => 11000.0,
                'market_max_price' => 18000.0,
                'effective_from' => now()->subDay()->toDateString(),
                'is_active' => true,
            ]
        );

        $idempotencyKey = 'IDEM-' . uniqid();

        $payload = [
            'location_id' => $this->store->id,
            'payment_method' => 'CASH',
            'paid_amount' => 50000.0,
            'idempotency_key' => $idempotencyKey,
            'customer_name' => 'Bapak Budi Santoso',
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'product_uom_id' => $this->baseUom->id,
                    'quantity' => 2.0,
                    'unit_price' => 15000.0,
                ],
            ],
        ];

        // 3. First checkout request
        $response = $this->actingAs($this->stafToko)->postJson(route('store.pos.checkout'), $payload);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Stock decreased by 2 (50 -> 48)
        $balAfter = (float) InventoryBalance::where('location_id', $this->store->id)
            ->where('product_id', $this->product->id)
            ->value('on_hand_qty');
        $this->assertEquals(48.0, $balAfter);

        // Movement PURPLE recorded
        $movement = StockMovement::where('product_id', $this->product->id)
            ->where('location_id', $this->store->id)
            ->where('movement_type', 'SALE_POS')
            ->latest('id')
            ->first();

        $this->assertNotNull($movement);
        $this->assertEquals('PURPLE', $movement->semantic_status);
        $this->assertEquals('#8B5CF6', $movement->semantic_color);

        // 4. Repeated checkout with SAME idempotency key (must NOT deduct stock again!)
        $repeatResponse = $this->actingAs($this->stafToko)->postJson(route('store.pos.checkout'), $payload);
        $repeatResponse->assertStatus(200);

        $balAfterRepeat = (float) InventoryBalance::where('location_id', $this->store->id)
            ->where('product_id', $this->product->id)
            ->value('on_hand_qty');
        $this->assertEquals(48.0, $balAfterRepeat); // Still exactly 48.0!
    }

    public function test_checkout_fails_if_insufficient_stock(): void
    {
        $this->posService->openShift($this->store, $this->stafToko, 200000.0);

        InventoryBalance::updateOrCreate(
            ['location_id' => $this->store->id, 'product_id' => $this->product->id],
            ['on_hand_qty' => 5.0, 'avg_unit_cost' => 10000.0]
        );

        $payload = [
            'location_id' => $this->store->id,
            'payment_method' => 'CASH',
            'paid_amount' => 500000.0,
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'product_uom_id' => $this->baseUom->id,
                    'quantity' => 100.0, // Exceeds 5.0
                    'unit_price' => 15000.0,
                ],
            ],
        ];

        $response = $this->actingAs($this->stafToko)->postJson(route('store.pos.checkout'), $payload);
        $response->assertStatus(422);
        $response->assertJsonFragment(['success' => false]);
    }

    public function test_checkout_fails_if_price_below_floor_price(): void
    {
        $this->posService->openShift($this->store, $this->stafToko, 200000.0);

        InventoryBalance::updateOrCreate(
            ['location_id' => $this->store->id, 'product_id' => $this->product->id],
            ['on_hand_qty' => 50.0, 'avg_unit_cost' => 10000.0]
        );

        // Set markup policy 10% -> Floor Price = 11.000
        PricingPolicy::updateOrCreate(
            ['channel' => 'RETAIL'],
            ['name' => 'Retail', 'min_markup_type' => 'PERCENTAGE', 'min_markup_value' => 10.0, 'is_active' => true]
        );

        $payload = [
            'location_id' => $this->store->id,
            'payment_method' => 'CASH',
            'paid_amount' => 50000.0,
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'product_uom_id' => $this->baseUom->id,
                    'quantity' => 1.0,
                    'unit_price' => 9000.0, // Below Floor Price (11.000)
                ],
            ],
        ];

        $response = $this->actingAs($this->stafToko)->postJson(route('store.pos.checkout'), $payload);
        $response->assertStatus(422);
        $this->assertStringContainsString('berada di bawah batas minimum', $response->json('message'));
    }

    public function test_cashier_can_close_shift_with_blind_cash_count_and_logs_discrepancy_anomaly(): void
    {
        CashShift::where('cashier_user_id', $this->stafToko->id)->where('status', 'OPEN')->update(['status' => 'CLOSED']);
        $shift = $this->posService->openShift($this->store, $this->stafToko, 200000.0);

        // Expected is 200.000. Cashier enters actual cash 180.000 (Deficit Rp 20.000)
        $response = $this->actingAs($this->stafToko)->post(route('store.shifts.close', $shift->id), [
            'actual_cash' => 180000.0,
            'notes' => 'Uang fisik dihitung saat tutup shift.',
        ]);

        $response->assertRedirect();
        $shift->refresh();

        $this->assertEquals('CLOSED', $shift->status);
        $this->assertEquals(-20000.0, (float) $shift->difference);

        // RED Anomaly Event published
        $anomaly = AnomalyEvent::where('reference_id', $shift->id)
            ->where('kind', 'CASH_DRAWER_DISCREPANCY')
            ->first();

        $this->assertNotNull($anomaly);
        $this->assertEquals('RED', $anomaly->severity);
        $this->assertEquals(20000.0, (float) $anomaly->amount);
    }

    public function test_authorized_user_can_void_sale_with_reversal_and_red_movement(): void
    {
        $this->posService->openShift($this->store, $this->stafToko, 200000.0);

        InventoryBalance::updateOrCreate(
            ['location_id' => $this->store->id, 'product_id' => $this->product->id],
            ['on_hand_qty' => 50.0, 'avg_unit_cost' => 10000.0]
        );

        $sale = $this->posService->checkout([
            'location_id' => $this->store->id,
            'payment_method' => 'CASH',
            'paid_amount' => 30000.0,
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'product_uom_id' => $this->baseUom->id,
                    'quantity' => 2.0,
                    'unit_price' => 15000.0,
                ],
            ],
        ], $this->stafToko);

        $this->assertEquals('POSTED', $sale->status);
        $this->assertEquals(48.0, (float) InventoryBalance::where('location_id', $this->store->id)->where('product_id', $this->product->id)->value('on_hand_qty'));

        // Kepala toko voids the sale
        $response = $this->actingAs($this->kepalaToko)->post(route('store.pos.void', $sale->id), [
            'void_reason' => 'Pembeli membatalkan pembelian karena barang salah varian.',
        ]);

        $response->assertRedirect();
        $sale->refresh();

        $this->assertEquals('VOIDED', $sale->status);
        $this->assertEquals('REFUNDED', $sale->payment_status);

        // Inventory restored back to 50.0
        $restoredQty = (float) InventoryBalance::where('location_id', $this->store->id)->where('product_id', $this->product->id)->value('on_hand_qty');
        $this->assertEquals(50.0, $restoredQty);

        // RED Movement logged
        $reversalMove = StockMovement::where('product_id', $this->product->id)
            ->where('movement_type', 'SALE_VOID')
            ->latest('id')
            ->first();

        $this->assertNotNull($reversalMove);
        $this->assertEquals('RED', $reversalMove->semantic_status);
        $this->assertEquals('#EF4444', $reversalMove->semantic_color);

        // RED Anomaly event published
        $anomaly = AnomalyEvent::where('reference_id', $sale->id)
            ->where('kind', 'SALE_VOID')
            ->first();

        $this->assertNotNull($anomaly);
        $this->assertEquals('RED', $anomaly->severity);
    }

    public function test_receipt_page_renders_thermal_receipt_without_right_panel_and_with_print_styles(): void
    {
        $sale = Sale::first();
        if (!$sale) {
            $shift = CashShift::create([
                'user_id' => $this->stafToko->id,
                'location_id' => $this->store->id,
                'shift_number' => 'SFT-TEST-001',
                'opened_at' => now(),
                'opening_cash' => 500000.0,
                'status' => 'OPEN',
            ]);
            $sale = Sale::create([
                'sale_number' => 'POS-TEST-001',
                'cash_shift_id' => $shift->id,
                'location_id' => $this->store->id,
                'cashier_id' => $this->stafToko->id,
                'customer_name' => 'Pelanggan Uji',
                'subtotal' => 50000.0,
                'total_amount' => 50000.0,
                'paid_amount' => 50000.0,
                'change_amount' => 0.0,
                'payment_method' => 'CASH',
                'status' => 'POSTED',
                'posted_at' => now(),
            ]);
        }

        $response = $this->actingAs($this->stafToko)->get(route('store.pos.receipt', $sale->id));

        $response->assertStatus(200);
        $response->assertSee('Struk Pembayaran Transaksi');
        $response->assertSee('id="thermalReceipt"', false);
        $response->assertSee('@media print', false);
        $response->assertSee('size: 80mm auto;', false);
        // Ensure right panel widgets are NOT present on the receipt page
        $response->assertDontSee('Fungsi Otomasi');
        $response->assertDontSee('Optimasi Rak');
        $response->assertDontSee('Statistik Throughput');
    }
}
