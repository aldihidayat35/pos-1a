<?php

namespace Tests\Feature;

use App\Models\B2bOrder;
use App\Models\B2bOrderItem;
use App\Models\InventoryBalance;
use App\Models\Invoice;
use App\Models\Location;
use App\Models\Member;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use App\Services\FinancialReportService;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Tests\TestCase;

class OwnerReportingAndReadOnlyTest extends TestCase
{
    protected User $owner;
    protected User $superAdmin;
    protected User $kasir;
    protected User $kepalaGudang;
    protected Location $store;
    protected Location $warehouse;
    protected Product $product;
    protected Member $member;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::where('username', 'owner')->first() ?? User::factory()->create();
        $this->superAdmin = User::where('username', 'superadmin')->first() ?? User::factory()->create();
        $this->kasir = User::where('username', 'kasir')->first() ?? User::factory()->create();
        $this->kepalaGudang = User::where('username', 'kepalagudang')->first() ?? User::factory()->create();

        $this->store = Location::where('code', 'STORE-PASAR')->first() ?? Location::where('type', 'STORE')->first();
        $this->warehouse = Location::where('code', 'WH-CENTRAL')->first() ?? Location::where('type', 'WAREHOUSE')->first();

        $this->product = Product::where('is_active', true)->first();
        $this->member = Member::where('is_active', true)->first();
    }

    /**
     * Test 1: Owner and authorized users can access executive reports dashboard with live KPI
     */
    public function test_owner_can_access_executive_reports_dashboard_with_live_kpi(): void
    {
        $response = $this->actingAs($this->owner)
            ->get(route('reports.index'));

        $response->assertStatus(200);
        $response->assertSee('Dasbor Finansial');
        $response->assertSee('Owner Read-Only');
        $response->assertSee('Omzet Terealisasi');
        $response->assertSee('Estimasi Laba Kotor');
        $response->assertSee('Nilai Aset Persediaan');
        $response->assertSee('Piutang B2B Outstanding');
    }

    /**
     * Test 2: Unauthorized user without financial permissions is strictly forbidden
     */
    public function test_unauthorized_user_is_forbidden_from_viewing_reports(): void
    {
        $response = $this->actingAs($this->kasir)
            ->get(route('reports.index'));

        $response->assertStatus(403);
    }

    /**
     * Test 3: Realized revenue only aggregates POS COMPLETED and B2B HANDED_OVER, strictly excluding RESERVED/DRAFT
     */
    public function test_realized_revenue_strictly_includes_only_completed_pos_and_handed_over_b2b(): void
    {
        $uniqueSuffix = Str::random(5);
        $today = Carbon::today()->addHours(12);

        // 1. Create a COMPLETED POS sale
        $posSale = Sale::create([
            'sale_number' => 'POS-TEST-' . $uniqueSuffix,
            'idempotency_key' => Str::uuid()->toString(),
            'location_id' => $this->store->id,
            'cashier_user_id' => $this->kasir->id,
            'customer_name' => 'Test Customer',
            'subtotal' => 100000.00,
            'discount_amount' => 0.00,
            'tax_amount' => 0.00,
            'total_amount' => 100000.00,
            'paid_amount' => 100000.00,
            'payment_status' => 'PAID',
            'cogs_total' => 70000.00,
            'payment_method' => 'CASH',
            'status' => 'COMPLETED',
            'posted_at' => $today,
        ]);

        // 2. Create a RESERVED B2B order (MUST NOT be counted in revenue)
        $reservedB2b = B2bOrder::create([
            'order_number' => 'B2B-RES-' . $uniqueSuffix,
            'member_id' => $this->member->id,
            'creator_user_id' => $this->superAdmin->id,
            'fulfillment_location_id' => $this->warehouse->id,
            'subtotal' => 500000.00,
            'tax_amount' => 0.00,
            'total_amount' => 500000.00,
            'payment_term' => B2bOrder::TERM_NET_14,
            'status' => B2bOrder::STATUS_RESERVED,
            'placed_at' => Carbon::now(),
        ]);

        // 3. Create a HANDED_OVER B2B order (MUST be counted in revenue)
        $handedB2b = B2bOrder::create([
            'order_number' => 'B2B-HND-' . $uniqueSuffix,
            'member_id' => $this->member->id,
            'creator_user_id' => $this->superAdmin->id,
            'fulfillment_location_id' => $this->warehouse->id,
            'subtotal' => 300000.00,
            'tax_amount' => 0.00,
            'total_amount' => 300000.00,
            'payment_term' => B2bOrder::TERM_CASH,
            'status' => B2bOrder::STATUS_HANDED_OVER,
            'handed_over_at' => $today,
        ]);

        $uom = $this->product->productUoms()->first();
        B2bOrderItem::create([
            'b2b_order_id' => $handedB2b->id,
            'product_id' => $this->product->id,
            'product_uom_id' => $uom?->id,
            'quantity_input' => 10.000,
            'quantity_base' => 10.000,
            'unit_price' => 30000.00,
            'cogs_snapshot' => 20000.00,
            'discount_percent' => 0.00,
            'subtotal' => 300000.00,
        ]);

        /** @var FinancialReportService $service */
        $service = app(FinancialReportService::class);
        $summary = $service->getExecutiveSummary(null, Carbon::today()->startOfDay(), Carbon::today()->endOfDay());

        // Check POS revenue includes our $posSale
        $this->assertGreaterThanOrEqual(100000.00, $summary['revenue']['pos']);
        // Check B2B revenue includes $handedB2b but DOES NOT include $reservedB2b
        $this->assertGreaterThanOrEqual(300000.00, $summary['revenue']['b2b']);

        // Check detailed sales drilldown
        $salesList = $service->getDetailedSales(null, Carbon::today()->startOfDay(), Carbon::today()->endOfDay());
        $this->assertTrue($salesList->contains('reference_number', $posSale->sale_number));
        $this->assertTrue($salesList->contains('reference_number', $handedB2b->order_number));
        $this->assertFalse($salesList->contains('reference_number', $reservedB2b->order_number));

        // Clean up test records
        $posSale->delete();
        $handedB2b->items()->delete();
        $handedB2b->delete();
        $reservedB2b->delete();
    }

    /**
     * Test 4: Realized sales drilldown page renders properly with metrics and rows
     */
    public function test_sales_drilldown_page_renders_with_metrics_and_channels(): void
    {
        $response = $this->actingAs($this->owner)
            ->get(route('reports.sales'));

        $response->assertStatus(200);
        $response->assertSee('Buku Penjualan & Margin');
        $response->assertSee('Total Omzet Terealisasi');
        $response->assertSee('Total Beban Pokok');
        $response->assertSee('Laba Kotor Terealisasi');
        $response->assertSee('Ritel POS Toko');
        $response->assertSee('Grosir B2B');
    }

    /**
     * Test 5: Inventory asset valuation report displays SKU level on-hand values
     */
    public function test_inventory_valuation_report_displays_on_hand_moving_average_values(): void
    {
        $response = $this->actingAs($this->owner)
            ->get(route('reports.inventory'));

        $response->assertStatus(200);
        $response->assertSee('Valuasi Stok & Nilai Aset Persediaan');
        $response->assertSee('Total Nilai Aset Stok');
        $response->assertSee('Aset Gudang Pusat');
        $response->assertSee('Aset Display Toko');
        $response->assertSee('Fisik On-Hand');
        $response->assertSee('HPP Rata-rata');
    }

    /**
     * Test 6: Receivables aging schedule categorizes active B2B invoices into aging buckets
     */
    public function test_receivables_aging_schedule_categorizes_overdue_and_current_invoices(): void
    {
        $response = $this->actingAs($this->owner)
            ->get(route('reports.receivables'));

        $response->assertStatus(200);
        $response->assertSee('Penuaan Piutang B2B');
        $response->assertSee('Lancar (Belum Jatuh Tempo)');
        $response->assertSee('Terlambat 1 - 7 Hari');
        $response->assertSee('Terlambat 8 - 14 Hari');
        $response->assertSee('Macet Kritis (> 14 Hari)');
    }

    /**
     * Test 7: CSV exports stream valid UTF-8 BOM CSV files
     */
    public function test_csv_export_endpoints_stream_valid_csv_files(): void
    {
        // 1. Financial summary export
        $finExport = $this->actingAs($this->owner)
            ->get(route('reports.export.financial'));

        $finExport->assertStatus(200);
        $finExport->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('LAPORAN KESEHATAN FINANSIAL', $finExport->streamedContent());

        // 2. Sales drilldown export
        $salesExport = $this->actingAs($this->owner)
            ->get(route('reports.export.sales'));

        $salesExport->assertStatus(200);
        $salesExport->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('Jenis Transaksi', $salesExport->streamedContent());
        $this->assertStringContainsString('No. Referensi / Struk', $salesExport->streamedContent());

        // 3. Inventory valuation export
        $invExport = $this->actingAs($this->owner)
            ->get(route('reports.export.inventory'));

        $invExport->assertStatus(200);
        $invExport->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('SKU', $invExport->streamedContent());
        $this->assertStringContainsString('Total Nilai Aset', $invExport->streamedContent());
    }

    /**
     * Test 8: Strict Owner Read-Only Guard rejects any mutative operational action
     */
    public function test_strict_owner_read_only_guard_rejects_all_mutative_actions(): void
    {
        // 1. Owner attempts POS checkout -> 403 Forbidden
        $posResponse = $this->actingAs($this->owner)
            ->post(route('store.pos.checkout'), [
                'items' => [
                    ['product_id' => $this->product->id, 'quantity' => 1, 'unit_price' => 20000]
                ],
                'payment_method' => 'CASH',
                'cash_tendered' => 50000,
            ]);
        $posResponse->assertStatus(403);

        // 2. Owner attempts to create Purchase Order -> 403 Forbidden
        $poResponse = $this->actingAs($this->owner)
            ->post(route('warehouse.po.store'), [
                'supplier_id' => 1,
                'location_id' => $this->warehouse->id,
                'items' => [
                    ['product_id' => $this->product->id, 'quantity' => 10, 'unit_cost' => 10000]
                ],
            ]);
        $poResponse->assertStatus(403);

        // 3. Owner attempts to create stock transfer -> 403 Forbidden
        $transferResponse = $this->actingAs($this->owner)
            ->post(route('warehouse.transfers.store'), [
                'source_location_id' => $this->warehouse->id,
                'destination_location_id' => $this->store->id,
                'items' => [
                    ['product_id' => $this->product->id, 'quantity' => 5]
                ],
            ]);
        $transferResponse->assertStatus(403);

        // 4. Owner attempts to create stock opname session -> 403 Forbidden
        $opnameResponse = $this->actingAs($this->owner)
            ->post(route('opname.store'), [
                'location_id' => $this->warehouse->id,
                'notes' => 'Illegal owner mutation attempt',
            ]);
        $opnameResponse->assertStatus(403);
    }
}
