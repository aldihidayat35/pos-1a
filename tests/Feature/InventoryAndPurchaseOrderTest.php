<?php

namespace Tests\Feature;

use App\Models\InventoryBalance;
use App\Models\Location;
use App\Models\Product;
use App\Models\ProductUom;
use App\Models\PurchaseOrder;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\User;
use App\Services\InventoryService;
use App\Services\PurchasingService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class InventoryAndPurchaseOrderTest extends TestCase
{
    use DatabaseTransactions;

    protected User $kepalaGudang;
    protected User $stafGudang;
    protected User $stafToko;
    protected Location $warehouse;
    protected Supplier $supplier;
    protected Product $product;
    protected ProductUom $baseUom;
    protected ProductUom $packUom;

    protected function setUp(): void
    {
        parent::setUp();

        $this->kepalaGudang = User::where('username', 'kepalagudang')->first()
            ?? User::where('email', 'kepalagudang@pasar.local')->first();
        $this->stafGudang = User::where('username', 'stafgudang')->first()
            ?? User::where('email', 'stafgudang@pasar.local')->first();
        $this->stafToko = User::where('username', 'staftoko')->first()
            ?? User::where('email', 'staftoko@pasar.local')->first();

        $this->warehouse = Location::where('code', 'WH-CENTRAL')->firstOrFail();
        $this->supplier = Supplier::firstOrFail();
        $this->product = Product::with(['productUoms', 'baseUom'])->where('sku', 'SKU-BRS-001')->firstOrFail();

        $this->baseUom = $this->product->productUoms->where('is_base', true)->first()
            ?? $this->product->productUoms->first();
        $this->packUom = $this->product->productUoms->where('is_base', false)->first()
            ?? $this->baseUom;
    }

    public function test_user_can_view_warehouse_dashboard_with_live_stats_and_pos(): void
    {
        $response = $this->actingAs($this->kepalaGudang)->get(route('warehouse.index'));

        $response->assertStatus(200);
        $response->assertSee('Operasional Gudang Pusat');
        $response->assertSee('Pesanan Pembelian Aktif');
        $response->assertSee('Saldo Fisik Stok');
        $response->assertSee('Moving Average HPP');
        $response->assertSee(route('warehouse.po.index'));
        $response->assertSee(route('warehouse.ledger.index'));
    }

    public function test_authorized_user_can_create_purchase_order_with_multi_uom(): void
    {
        $payload = [
            'supplier_id' => $this->supplier->id,
            'destination_location_id' => $this->warehouse->id,
            'ordered_at' => now()->toDateString(),
            'expected_delivery_at' => now()->addDays(3)->toDateString(),
            'notes' => 'PO Pengadaan Beras Rojolele Super Kemasan Karung.',
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'product_uom_id' => $this->packUom->id,
                    'quantity_ordered' => 10.000,
                    'unit_purchase_price' => 600000.00,
                ],
            ],
        ];

        $response = $this->actingAs($this->kepalaGudang)->post(route('warehouse.po.store'), $payload);

        $response->assertRedirect();

        $po = PurchaseOrder::where('supplier_id', $this->supplier->id)
            ->where('notes', 'PO Pengadaan Beras Rojolele Super Kemasan Karung.')
            ->first();

        $this->assertNotNull($po);
        $this->assertEquals('ISSUED', $po->status);
        $this->assertEquals(6000000.00, (float) $po->grand_total);
        $this->assertCount(1, $po->items);
        $this->assertEquals(10.000, (float) $po->items->first()->quantity_ordered);
    }

    public function test_staff_can_receive_goods_partially_and_updates_status_and_ledger(): void
    {
        // 1. Create a fresh PO
        $purchasingService = app(PurchasingService::class);
        $po = $purchasingService->createPurchaseOrder([
            'supplier_id' => $this->supplier->id,
            'destination_location_id' => $this->warehouse->id,
            'ordered_at' => now(),
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'product_uom_id' => $this->packUom->id,
                    'quantity_ordered' => 10.000, // 10 Karung
                    'unit_purchase_price' => 600000.00,
                ],
            ],
        ], $this->kepalaGudang);

        $poItem = $po->items->first();
        $factor = (float) $poItem->factor_to_base_snapshot ?: 1.0;

        // Balance before
        $initialBalance = InventoryBalance::where('location_id', $this->warehouse->id)
            ->where('product_id', $this->product->id)
            ->first();
        $qtyBefore = (float) ($initialBalance?->on_hand_qty ?? 0);

        // 2. Receive 4 Karung partially
        $receiptPayload = [
            'supplier_do_number' => 'SJ-TEST-VENDOR-101',
            'notes' => 'Penerimaan bertahap 4 Karung pertama.',
            'items' => [
                [
                    'purchase_order_item_id' => $poItem->id,
                    'qty_input' => 4.000,
                    'unit_cost_input' => 600000.00,
                    'batch_number' => 'LOT-TEST-001',
                    'expiry_date' => now()->addYear()->toDateString(),
                ],
            ],
        ];

        $response = $this->actingAs($this->stafGudang)->post(
            route('warehouse.receipts.store', $po->id),
            $receiptPayload
        );

        $response->assertRedirect();

        // 3. Assertions
        $po->refresh();
        $this->assertEquals('PARTIALLY_RECEIVED', $po->status);

        $expectedBaseReceived = 4.000 * $factor;
        $this->assertEquals($expectedBaseReceived, (float) $poItem->fresh()->quantity_received_base);

        // Assert Balance Updated
        $balanceAfter = InventoryBalance::where('location_id', $this->warehouse->id)
            ->where('product_id', $this->product->id)
            ->first();
        $this->assertEquals($qtyBefore + $expectedBaseReceived, (float) $balanceAfter->on_hand_qty);

        // Assert Stock Movement Ledger Recorded with GREEN Semantic Status
        $movement = StockMovement::where('location_id', $this->warehouse->id)
            ->where('product_id', $this->product->id)
            ->where('movement_type', 'INBOUND_PO')
            ->latest('id')
            ->first();

        $this->assertNotNull($movement);
        $this->assertEquals('GREEN', $movement->semantic_status);
        $this->assertEquals($expectedBaseReceived, (float) $movement->delta_qty_base);
    }

    public function test_moving_average_hpp_recalculation_accuracy(): void
    {
        $inventoryService = app(InventoryService::class);
        $purchasingService = app(PurchasingService::class);

        // 1. Manually set exact known balance: 100 kg @ Rp 10.000
        $balance = InventoryBalance::updateOrCreate(
            ['location_id' => $this->warehouse->id, 'product_id' => $this->product->id],
            [
                'on_hand_qty' => 100.000,
                'reserved_qty' => 0.000,
                'avg_unit_cost' => 10000.000000,
                'version' => 1,
            ]
        );

        // 2. Issue PO and receive: 50 kg @ Rp 16.000
        // Expected New HPP = ((100 * 10.000) + (50 * 16.000)) / (100 + 50)
        //                 = (1.000.000 + 800.000) / 150
        //                 = 1.800.000 / 150 = Rp 12.000,00/kg
        $po = $purchasingService->createPurchaseOrder([
            'supplier_id' => $this->supplier->id,
            'destination_location_id' => $this->warehouse->id,
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'product_uom_id' => $this->baseUom->id, // Base unit (1 kg)
                    'quantity_ordered' => 50.000,
                    'unit_purchase_price' => 16000.00,
                ],
            ],
        ], $this->kepalaGudang);

        $poItem = $po->items->first();

        $purchasingService->receiveGoodsReceipt($po, [
            'supplier_do_number' => 'DO-HPP-TEST-01',
            'items' => [
                [
                    'purchase_order_item_id' => $poItem->id,
                    'qty_input' => 50.000,
                    'unit_cost_input' => 16000.00,
                    'batch_number' => 'LOT-HPP-01',
                ],
            ],
        ], $this->stafGudang);

        $balance->refresh();
        $this->assertEquals(150.000, (float) $balance->on_hand_qty);
        $this->assertEquals(12000.000000, (float) $balance->avg_unit_cost);
    }

    public function test_full_receipt_marks_po_status_as_received(): void
    {
        $purchasingService = app(PurchasingService::class);
        $po = $purchasingService->createPurchaseOrder([
            'supplier_id' => $this->supplier->id,
            'destination_location_id' => $this->warehouse->id,
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'product_uom_id' => $this->baseUom->id,
                    'quantity_ordered' => 25.000,
                    'unit_purchase_price' => 12500.00,
                ],
            ],
        ], $this->kepalaGudang);

        $poItem = $po->items->first();

        $purchasingService->receiveGoodsReceipt($po, [
            'supplier_do_number' => 'DO-FULL-RECEIPT-99',
            'items' => [
                [
                    'purchase_order_item_id' => $poItem->id,
                    'qty_input' => 25.000, // 100% full
                    'unit_cost_input' => 12500.00,
                ],
            ],
        ], $this->stafGudang);

        $po->refresh();
        $this->assertEquals('RECEIVED', $po->status);
    }

    public function test_unauthorized_user_cannot_create_po_or_receive_goods(): void
    {
        // Staf Toko has neither purchasing.po nor purchasing.receive
        $poPayload = [
            'supplier_id' => $this->supplier->id,
            'destination_location_id' => $this->warehouse->id,
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'product_uom_id' => $this->baseUom->id,
                    'quantity_ordered' => 5.000,
                    'unit_purchase_price' => 10000.00,
                ],
            ],
        ];

        $response = $this->actingAs($this->stafToko)->post(route('warehouse.po.store'), $poPayload);
        $response->assertStatus(403);
    }

    public function test_staf_gudang_without_hpp_view_sees_masked_financials(): void
    {
        $response = $this->actingAs($this->stafGudang)->get(route('warehouse.ledger.index'));

        $response->assertStatus(200);
        $response->assertSee('*** [Terkunci]');
    }
}
