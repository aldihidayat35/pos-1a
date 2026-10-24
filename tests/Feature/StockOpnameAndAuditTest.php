<?php

namespace Tests\Feature;

use App\Models\AnomalyEvent;
use App\Models\InventoryBalance;
use App\Models\Location;
use App\Models\Product;
use App\Models\Role;
use App\Models\StockMovement;
use App\Models\StockOpname;
use App\Models\StockOpnameItem;
use App\Models\User;
use App\Services\StockOpnameService;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockOpnameAndAuditTest extends TestCase
{
    protected User $superAdmin;
    protected User $kepalaGudang;
    protected User $stafGudang;
    protected User $owner;
    protected Location $warehouse;
    protected Product $productA;
    protected Product $productB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::where('username', 'superadmin')->first() ?? User::factory()->create();
        $this->kepalaGudang = User::where('username', 'kepalagudang')->first() ?? User::factory()->create();
        $this->stafGudang = User::where('username', 'stafgudang')->first() ?? User::factory()->create();
        $this->owner = User::where('username', 'owner')->first() ?? User::factory()->create();

        $this->warehouse = Location::where('code', 'WH-CENTRAL')->first() ?? Location::where('type', 'WAREHOUSE')->first();

        // Create two test products
        $this->productA = Product::where('is_active', true)->where('track_stock', true)->first();
        $this->productB = Product::where('is_active', true)->where('track_stock', true)->skip(1)->first();

        // Initialize balances
        $balA = InventoryBalance::firstOrCreate(
            ['location_id' => $this->warehouse->id, 'product_id' => $this->productA->id],
            ['on_hand_qty' => 100.000, 'reserved_qty' => 0.000, 'avg_unit_cost' => 15000.00]
        );
        $balA->update(['on_hand_qty' => 100.000, 'avg_unit_cost' => 15000.00]);

        $balB = InventoryBalance::firstOrCreate(
            ['location_id' => $this->warehouse->id, 'product_id' => $this->productB->id],
            ['on_hand_qty' => 50.000, 'reserved_qty' => 0.000, 'avg_unit_cost' => 20000.00]
        );
        // Clean up prior test sessions
        StockOpname::where('notes', 'like', 'Uji buka sesi opname mandiri%')->delete();
    }

    public function test_user_with_opname_permission_can_view_opname_index_with_live_stats(): void
    {
        $response = $this->actingAs($this->kepalaGudang)->get(route('opname.index'));

        $response->assertStatus(200);
        $response->assertSee('Sesi Stok Opname &amp; Rekonsiliasi Audit', false);
        $response->assertSee('Total Sesi Opname');
        $response->assertSee('Total Susut / Selisih Kurang');
    }

    public function test_authorized_user_can_create_opname_session_with_frozen_snapshot(): void
    {
        $response = $this->actingAs($this->kepalaGudang)->post(route('opname.store'), [
            'location_id' => $this->warehouse->id,
            'scope_type' => StockOpname::SCOPE_CUSTOM,
            'product_ids' => [$this->productA->id, $this->productB->id],
            'notes' => 'Uji buka sesi opname mandiri',
        ]);

        $opname = StockOpname::where('notes', 'Uji buka sesi opname mandiri')->orderByDesc('id')->first();
        $this->assertNotNull($opname);
        $this->assertEquals(StockOpname::STATUS_OPEN, $opname->status);
        $this->assertStringStartsWith('OPN-', $opname->opname_number);

        $response->assertRedirect(route('opname.show', $opname->id));

        // Check frozen items
        $this->assertCount(2, $opname->items);
        $itemA = $opname->items()->where('product_id', $this->productA->id)->first();
        $this->assertEquals(100.000, (float) $itemA->system_qty_snapshot);
        $this->assertNull($itemA->physical_qty_counted);
    }

    public function test_staff_blind_count_screen_does_not_reveal_system_quantity(): void
    {
        $opnameService = app(StockOpnameService::class);
        $session = $opnameService->createSession($this->warehouse, $this->kepalaGudang, [
            'scope_type' => StockOpname::SCOPE_CUSTOM,
            'product_ids' => [$this->productA->id],
            'notes' => 'Blind count test session',
        ]);

        $response = $this->actingAs($this->stafGudang)->get(route('opname.count', $session->id));

        $response->assertStatus(200);
        $response->assertSee('Prinsip Blind Count Diaktifkan');
        $response->assertSee($this->productA->name);
        $response->assertSee('Kuantitas Fisik Riil (Blind Input)');
        // Assert that system quantity is NOT shown on the blind count screen
        $response->assertDontSee('Snapshot Sistem');
        $response->assertDontSee('100.000');
    }

    public function test_staff_can_record_physical_count_and_updates_status_to_counting(): void
    {
        $opnameService = app(StockOpnameService::class);
        $session = $opnameService->createSession($this->warehouse, $this->kepalaGudang, [
            'scope_type' => StockOpname::SCOPE_CUSTOM,
            'product_ids' => [$this->productA->id],
        ]);

        $itemA = $session->items()->first();

        $response = $this->actingAs($this->stafGudang)->post(route('opname.count.save', $session->id), [
            'counts' => [
                $itemA->id => 97.000, // Deficit of 3 units
            ],
        ]);

        $response->assertRedirect();
        $session->refresh();

        $this->assertEquals(StockOpname::STATUS_COUNTING, $session->status);
        $itemA->refresh();
        $this->assertEquals(97.000, (float) $itemA->physical_qty_counted);
        $this->assertEquals(-3.000, (float) $itemA->variance_qty);
        $this->assertEquals(-45000.00, (float) $itemA->variance_value); // -3 * 15000
    }

    public function test_moving_to_reconciliation_requires_reconcile_permission(): void
    {
        $opnameService = app(StockOpnameService::class);
        $session = $opnameService->createSession($this->warehouse, $this->kepalaGudang, [
            'scope_type' => StockOpname::SCOPE_CUSTOM,
            'product_ids' => [$this->productA->id],
        ]);

        // Staf Gudang does NOT have opname.reconcile -> 403 Forbidden
        $responseStaf = $this->actingAs($this->stafGudang)->get(route('opname.reconcile', $session->id));
        $responseStaf->assertStatus(403);

        // Kepala Gudang HAS opname.reconcile -> 200 OK
        $responseHead = $this->actingAs($this->kepalaGudang)->get(route('opname.reconcile', $session->id));
        $responseHead->assertStatus(200);
        $responseHead->assertSee('Rekonsiliasi &amp; Validasi Alasan Selisih Opname', false);
        $responseHead->assertSee('Snapshot Sistem');
    }

    public function test_finalization_fails_if_discrepancy_items_lack_reason_code(): void
    {
        $opnameService = app(StockOpnameService::class);
        $session = $opnameService->createSession($this->warehouse, $this->kepalaGudang, [
            'scope_type' => StockOpname::SCOPE_CUSTOM,
            'product_ids' => [$this->productA->id],
        ]);

        $itemA = $session->items()->first();
        $opnameService->recordCount($session, $this->stafGudang, [
            $itemA->id => 95.000, // Deficit of 5
        ]);

        $session->refresh();

        // Attempt to finalize without assigning reasons
        $response = $this->actingAs($this->kepalaGudang)->post(route('opname.finalize', $session->id), [
            'final_notes' => 'Finalize without reasons test',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $session->refresh();
        $this->assertNotEquals(StockOpname::STATUS_FINALIZED, $session->status);
    }

    public function test_successful_finalization_adjusts_inventory_and_logs_red_movement_and_anomaly(): void
    {
        $opnameService = app(StockOpnameService::class);
        $session = $opnameService->createSession($this->warehouse, $this->kepalaGudang, [
            'scope_type' => StockOpname::SCOPE_CUSTOM,
            'product_ids' => [$this->productA->id],
            'notes' => 'Session for full finalization test',
        ]);

        $itemA = $session->items()->first();
        $opnameService->recordCount($session, $this->stafGudang, [
            $itemA->id => 92.000, // Deficit of 8 units
        ]);

        $opnameService->saveReconcileReasons($session, [
            $itemA->id => [
                'reason_code' => StockOpnameItem::REASON_DAMAGED_EXPIRED,
                'notes' => 'Kemasan basah dan bocor',
            ],
        ]);

        $response = $this->actingAs($this->kepalaGudang)->post(route('opname.finalize', $session->id), [
            'final_notes' => 'Pengesahan final selesai',
        ]);

        $response->assertRedirect(route('opname.show', $session->id));
        $session->refresh();

        $this->assertEquals(StockOpname::STATUS_FINALIZED, $session->status);
        $this->assertEquals($this->kepalaGudang->id, $session->finalized_by_user_id);
        $this->assertNotNull($session->finalized_at);

        // Check InventoryBalance updated
        $balA = InventoryBalance::where('location_id', $this->warehouse->id)
            ->where('product_id', $this->productA->id)
            ->first();
        $this->assertEquals(92.000, (float) $balA->on_hand_qty);

        // Check StockMovement (Red movement)
        $movement = StockMovement::where('product_id', $this->productA->id)
            ->where('movement_type', 'OPNAME_ADJ')
            ->orderByDesc('id')
            ->first();
        $this->assertNotNull($movement);
        $this->assertEquals(-8.000, (float) $movement->delta_qty_base);
        $this->assertEquals('RED', $movement->semantic_status);

        // Check AnomalyEvent logged (Red)
        $anomaly = AnomalyEvent::where('reference_id', $session->id)
            ->where('reference_type', 'STOCK_OPNAME')
            ->first();
        $this->assertNotNull($anomaly);
        $this->assertEquals('RED', $anomaly->severity);
        $this->assertEquals(120000.00, (float) $anomaly->amount); // 8 * 15000
    }

    public function test_double_finalization_is_strictly_rejected(): void
    {
        $opnameService = app(StockOpnameService::class);
        $session = $opnameService->createSession($this->warehouse, $this->kepalaGudang, [
            'scope_type' => StockOpname::SCOPE_CUSTOM,
            'product_ids' => [$this->productA->id],
        ]);

        $itemA = $session->items()->first();
        $opnameService->recordCount($session, $this->stafGudang, [
            $itemA->id => 100.000,
        ]);
        $opnameService->finalize($session, $this->kepalaGudang);

        $session->refresh();
        $this->assertEquals(StockOpname::STATUS_FINALIZED, $session->status);

        // Second attempt must fail
        $response = $this->actingAs($this->kepalaGudang)->post(route('opname.finalize', $session->id));
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_owner_is_read_only_and_cannot_finalize_opname(): void
    {
        $opnameService = app(StockOpnameService::class);
        $session = $opnameService->createSession($this->warehouse, $this->kepalaGudang, [
            'scope_type' => StockOpname::SCOPE_CUSTOM,
            'product_ids' => [$this->productA->id],
        ]);

        $response = $this->actingAs($this->owner)->post(route('opname.finalize', $session->id));
        $response->assertStatus(403);
    }

    public function test_opname_cancellation_preserves_inventory_balance(): void
    {
        $initialOnHand = (float) InventoryBalance::where('location_id', $this->warehouse->id)
            ->where('product_id', $this->productA->id)
            ->value('on_hand_qty');

        $opnameService = app(StockOpnameService::class);
        $session = $opnameService->createSession($this->warehouse, $this->kepalaGudang, [
            'scope_type' => StockOpname::SCOPE_CUSTOM,
            'product_ids' => [$this->productA->id],
        ]);

        $response = $this->actingAs($this->kepalaGudang)->post(route('opname.cancel', $session->id), [
            'cancel_reason' => 'Salah lokasi hitung',
        ]);

        $response->assertRedirect(route('opname.index'));
        $session->refresh();

        $this->assertEquals(StockOpname::STATUS_CANCELLED, $session->status);

        // On-hand balance remains completely unchanged
        $finalOnHand = (float) InventoryBalance::where('location_id', $this->warehouse->id)
            ->where('product_id', $this->productA->id)
            ->value('on_hand_qty');
        $this->assertEquals($initialOnHand, $finalOnHand);
    }
}
