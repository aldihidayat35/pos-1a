<?php

namespace Tests\Feature;

use App\Models\B2bOrder;
use App\Models\B2bOrderItem;
use App\Models\CashShift;
use App\Models\InventoryBalance;
use App\Models\Location;
use App\Models\Member;
use App\Models\PickupTicket;
use App\Models\Product;
use App\Models\Sale;
use App\Models\StockMovement;
use App\Models\StockOpname;
use App\Models\StockOpnameItem;
use App\Models\User;
use App\Services\PosService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Tests\TestCase;

class StabilizationAndUatTest extends TestCase
{
    protected User $kasir;
    protected User $kepalaGudang;
    protected User $superAdmin;
    protected Location $store;
    protected Location $warehouse;
    protected Product $product;
    protected Member $member;

    protected function setUp(): void
    {
        parent::setUp();

        $this->kasir = User::where('username', 'staftoko')->first()
            ?? User::where('username', 'kasir')->first();
        $this->kepalaGudang = User::where('username', 'kepalagudang')->first();
        $this->superAdmin = User::where('username', 'superadmin')->first();

        $this->store = Location::where('code', 'STR-PASAR-1')->first()
            ?? Location::where('type', 'STORE')->first();
        $this->warehouse = Location::where('code', 'WH-CENTRAL')->first()
            ?? Location::where('type', 'WAREHOUSE')->first();

        $this->product = Product::with(['productUoms', 'baseUom'])->where('sku', 'SKU-BRS-001')->first()
            ?? Product::where('is_active', true)->where('track_stock', true)->first();
        $this->member = Member::where('is_active', true)->first();
    }

    /**
     * Test AT-02: POS checkout concurrency and strict zero-negative stock guard
     */
    public function test_at02_pos_checkout_strictly_blocks_negative_stock(): void
    {
        /** @var PosService $posService */
        $posService = app(PosService::class);

        $shift = CashShift::where('cashier_user_id', $this->kasir->id)
            ->where('location_id', $this->store->id)
            ->where('status', 'OPEN')
            ->first();

        if (!$shift) {
            $shift = $posService->openShift($this->store, $this->kasir, 200000.00);
        }

        \App\Models\PriceRule::updateOrCreate(
            ['product_id' => $this->product->id, 'channel' => 'RETAIL'],
            [
                'list_price' => 20000.0,
                'market_min_price' => 15000.0,
                'market_max_price' => 25000.0,
                'effective_from' => now()->subDay()->toDateString(),
                'is_active' => true,
            ]
        );

        // Set exact stock to 3
        $balance = InventoryBalance::firstOrCreate(
            ['location_id' => $this->store->id, 'product_id' => $this->product->id],
            ['on_hand_qty' => 3.000, 'reserved_qty' => 0.000, 'avg_unit_cost' => 10000.00]
        );
        $balance->update(['on_hand_qty' => 3.000]);

        $uom = $this->product->productUoms()->first();

        // 1. First checkout buys 3 -> succeeds
        $res1 = $this->actingAs($this->kasir)
            ->postJson(route('store.pos.checkout'), [
                'location_id' => $this->store->id,
                'idempotency_key' => Str::uuid()->toString(),
                'items' => [
                    [
                        'product_id' => $this->product->id,
                        'product_uom_id' => $uom->id,
                        'quantity' => 3,
                        'unit_price' => 20000,
                    ]
                ],
                'payment_method' => 'CASH',
                'cash_tendered' => 60000,
                'paid_amount' => 60000,
            ]);

        $res1->assertStatus(200);
        $res1->assertJson(['success' => true]);

        // Stock now 0
        $balance->refresh();
        $this->assertEquals(0.000, (float) $balance->on_hand_qty);

        // 2. Second checkout attempts to buy 1 -> MUST FAIL with 422 Insufficient Stock
        $res2 = $this->actingAs($this->kasir)
            ->postJson(route('store.pos.checkout'), [
                'location_id' => $this->store->id,
                'idempotency_key' => Str::uuid()->toString(),
                'items' => [
                    [
                        'product_id' => $this->product->id,
                        'product_uom_id' => $uom->id,
                        'quantity' => 1,
                        'unit_price' => 20000,
                    ]
                ],
                'payment_method' => 'CASH',
                'cash_tendered' => 20000,
                'paid_amount' => 20000,
            ]);

        $res2->assertStatus(422);
        $balance->refresh();
        $this->assertGreaterThanOrEqual(0.000, (float) $balance->on_hand_qty);

        // Restore stock
        $balance->update(['on_hand_qty' => 50.000]);
    }

    /**
     * Test AT-03: Checkout idempotency replay protects against duplicate submissions
     */
    public function test_at03_checkout_idempotency_prevents_duplicate_sales_and_inventory_decrement(): void
    {
        $idempotencyKey = Str::uuid()->toString();
        $uom = $this->product->productUoms()->first();

        $balance = InventoryBalance::where('location_id', $this->store->id)
            ->where('product_id', $this->product->id)
            ->first();
        $initialStock = (float) $balance->on_hand_qty;

        $payload = [
            'location_id' => $this->store->id,
            'idempotency_key' => $idempotencyKey,
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'product_uom_id' => $uom->id,
                    'quantity' => 1,
                    'unit_price' => 20000,
                ]
            ],
            'payment_method' => 'CASH',
            'cash_tendered' => 50000,
            'paid_amount' => 50000,
        ];

        // 1st request
        $res1 = $this->actingAs($this->kasir)->postJson(route('store.pos.checkout'), $payload);
        $res1->assertStatus(200);
        $saleId1 = $res1->json('sale.id');

        // 2nd request with exact same idempotency_key
        $res2 = $this->actingAs($this->kasir)->postJson(route('store.pos.checkout'), $payload);
        $res2->assertStatus(200);
        $saleId2 = $res2->json('sale.id');
        $this->assertEquals($saleId1, $saleId2);

        // Stock was deducted only once
        $balance->refresh();
        $this->assertEquals($initialStock - 1.000, (float) $balance->on_hand_qty);

        // Revert test stock
        $balance->update(['on_hand_qty' => $initialStock]);
    }

    /**
     * Test AT-09: One-time QR token pickup redemption is strictly single-use
     */
    public function test_at09_one_time_qr_token_pickup_cannot_be_redeemed_twice(): void
    {
        $uom = $this->product->productUoms()->first();

        // Create a RESERVED B2B order
        $order = B2bOrder::create([
            'order_number' => 'B2B-UAT-' . Str::random(5),
            'member_id' => $this->member->id,
            'creator_user_id' => $this->superAdmin->id,
            'fulfillment_location_id' => $this->warehouse->id,
            'payment_term' => B2bOrder::TERM_CASH,
            'subtotal' => 100000.00,
            'tax_amount' => 0.00,
            'total_amount' => 100000.00,
            'status' => B2bOrder::STATUS_RESERVED,
            'ready_at' => Carbon::now(),
        ]);

        $orderItem = $order->items()->create([
            'product_id' => $this->product->id,
            'product_uom_id' => $uom->id,
            'quantity_input' => 2.000,
            'quantity_base' => 2.000,
            'unit_price' => 50000.00,
            'cogs_snapshot' => 30000.00,
            'subtotal' => 100000.00,
        ]);

        // Inventory balance and reservation
        $balance = InventoryBalance::firstOrCreate(
            ['location_id' => $this->warehouse->id, 'product_id' => $this->product->id],
            ['on_hand_qty' => 50.000, 'reserved_qty' => 0.000, 'avg_unit_cost' => 10000.00]
        );
        $balance->on_hand_qty = 50.000;
        $balance->reserved_qty = 2.000;
        $balance->save();

        \App\Models\StockReservation::create([
            'b2b_order_id' => $order->id,
            'b2b_order_item_id' => $orderItem->id,
            'location_id' => $this->warehouse->id,
            'product_id' => $this->product->id,
            'reserved_qty_base' => 2.000,
            'status' => \App\Models\StockReservation::STATUS_ACTIVE,
            'reserved_at' => now(),
        ]);

        // Generate one-time ticket
        $ticketToken = 'TK-' . strtoupper(Str::random(12));
        $ticket = PickupTicket::create([
            'b2b_order_id' => $order->id,
            'ticket_number' => 'TKT-TEST-' . uniqid(),
            'token_hash' => hash('sha256', $ticketToken),
            'raw_token_preview' => $ticketToken,
            'fulfillment_location_id' => $this->warehouse->id,
            'status' => PickupTicket::STATUS_ISSUED,
            'issued_at' => now(),
            'expires_at' => Carbon::now()->addHours(24),
        ]);

        // 1. First redemption -> MUST SUCCEED
        $res1 = $this->actingAs($this->kepalaGudang)->postJson(route('b2b.pickup.redeem'), [
            'token' => $ticketToken,
            'notes' => 'Serah terima pertama UAT',
        ]);
        $res1->assertStatus(200);
        $res1->assertJson(['success' => true]);

        $order->refresh();
        $this->assertEquals(B2bOrder::STATUS_HANDED_OVER, $order->status);

        // 2. Second redemption with the same token -> MUST BE REJECTED (422)
        $res2 = $this->actingAs($this->kepalaGudang)->postJson(route('b2b.pickup.redeem'), [
            'token' => $ticketToken,
            'notes' => 'Percobaan serah terima kedua',
        ]);
        $res2->assertStatus(422);

        // Clean up
        $ticket->delete();
        $order->items()->delete();
        $order->delete();
    }

    /**
     * Test AT-15: Bilingual language switcher persists in session, user model, and translates app
     */
    public function test_at15_bilingual_language_switcher_persists_and_translates_labels(): void
    {
        // 1. Switch to English
        $resEn = $this->actingAs($this->superAdmin)
            ->postJson(route('locale.switch'), ['locale' => 'en']);

        $resEn->assertStatus(200);
        $resEn->assertJson(['success' => true, 'locale' => 'en']);
        $this->assertEquals('en', session('locale'));

        $this->superAdmin->refresh();
        $this->assertEquals('en', $this->superAdmin->preferred_locale);

        // 2. Switch to Indonesian
        $resId = $this->actingAs($this->superAdmin)
            ->postJson(route('locale.switch'), ['locale' => 'id']);

        $resId->assertStatus(200);
        $resId->assertJson(['success' => true, 'locale' => 'id']);
        $this->assertEquals('id', session('locale'));

        // 3. Invalid locale is rejected
        $resInvalid = $this->actingAs($this->superAdmin)
            ->postJson(route('locale.switch'), ['locale' => 'de']);

        $resInvalid->assertStatus(422);
    }

    /**
     * Test AT-16: Historical transaction COGS snapshot remains completely immutable when product HPP changes
     */
    public function test_at16_historical_sale_cogs_snapshot_remains_immutable_when_hpp_changes(): void
    {
        $uom = $this->product->productUoms()->first();

        // 1. Create a sale with recorded cogs_total = 70000
        $sale = Sale::create([
            'sale_number' => 'POS-HIST-' . Str::random(5),
            'idempotency_key' => Str::uuid()->toString(),
            'location_id' => $this->store->id,
            'cashier_user_id' => $this->kasir->id,
            'customer_name' => 'Historical Customer',
            'subtotal' => 100000.00,
            'discount_amount' => 0.00,
            'tax_amount' => 0.00,
            'total_amount' => 100000.00,
            'paid_amount' => 100000.00,
            'payment_status' => 'PAID',
            'cogs_total' => 70000.00,
            'payment_method' => 'CASH',
            'status' => 'COMPLETED',
            'posted_at' => Carbon::now()->subDays(5),
        ]);

        // 2. Drastically change product's avg_unit_cost in balance
        $bal = InventoryBalance::where('location_id', $this->store->id)
            ->where('product_id', $this->product->id)
            ->first();
        $originalCost = (float) $bal->avg_unit_cost;

        $bal->update(['avg_unit_cost' => 999999.00]);

        // 3. Verify historical sale was NOT affected
        $sale->refresh();
        $this->assertEquals(70000.00, (float) $sale->cogs_total);

        // Restore balance cost
        $bal->update(['avg_unit_cost' => $originalCost]);
        $sale->delete();
    }

    /**
     * Test Database Backup Artisan Command
     */
    public function test_automated_database_backup_command_generates_valid_sql_snapshot(): void
    {
        $exitCode = Artisan::call('app:backup');
        $this->assertEquals(0, $exitCode);

        $output = Artisan::output();
        $this->assertStringContainsString('Pencadangan Sukses', $output);

        $backupDir = storage_path('app/backups');
        $files = File::files($backupDir);
        $this->assertNotEmpty($files);

        $latestFile = $files[count($files) - 1];
        $content = File::get($latestFile->getRealPath());

        $this->assertStringContainsString('PASARHUB SISTEM OPERASIONAL PASAR TERPADU', $content);
        $this->assertStringContainsString('CREATE TABLE `users`', $content);
        $this->assertStringContainsString('CREATE TABLE `sales`', $content);
    }
}
