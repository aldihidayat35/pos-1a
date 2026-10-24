<?php

namespace Tests\Feature;

use App\Models\B2bOrder;
use App\Models\InventoryBalance;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Location;
use App\Models\Member;
use App\Models\PickupTicket;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\StockReservation;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Tests\TestCase;

class B2bCreditAndFulfillmentTest extends TestCase
{
    protected User $salesUser;
    protected User $stafGudang;
    protected User $stafToko;
    protected Location $gudang;
    protected Location $toko;
    protected Member $b2bMember;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->salesUser = User::where('username', 'sales')->first() ?? User::factory()->create();
        $this->stafGudang = User::where('username', 'stafgudang')->first() ?? User::factory()->create();
        $this->stafToko = User::where('username', 'staftoko')->first() ?? User::factory()->create();

        $this->gudang = Location::where('code', 'GUD-PUSAT')->first() ?? Location::first();
        $this->toko = Location::where('code', 'TOK-PASAR')->first() ?? Location::find(2);

        $this->b2bMember = Member::where('type', Member::TYPE_B2B)->first();
        $this->product = Product::where('sku', 'SKU-BRS-001')->first() ?? Product::first();

        // Clean up prior test orders and invoices
        InvoicePayment::whereHas('invoice', fn($q) => $q->where('invoice_number', 'like', 'INV-TEST%'))->delete();
        Invoice::where('invoice_number', 'like', 'INV-TEST%')->delete();
        PickupTicket::where('ticket_number', 'like', 'TKT-TEST%')->delete();
        StockReservation::whereHas('order', fn($q) => $q->where('order_number', 'like', 'B2B-TEST%'))->delete();
        B2bOrder::where('order_number', 'like', 'B2B-TEST%')->delete();
    }

    public function test_b2b_dashboard_displays_real_time_metrics(): void
    {
        $response = $this->actingAs($this->salesUser)->get(route('b2b.index'));

        $response->assertStatus(200);
        $response->assertSee('Kemitraan B2B &amp; Plafon Piutang', false);
        $response->assertSee('Buat Order Grosir B2B');
        $response->assertSee('Pemantauan Plafon Kredit', false);
    }

    public function test_b2b_order_placement_reserves_stock_and_issues_qr_ticket(): void
    {
        // Ensure starting stock balance
        $balance = InventoryBalance::firstOrCreate(
            ['location_id' => $this->gudang->id, 'product_id' => $this->product->id],
            ['on_hand_qty' => 500.000, 'reserved_qty' => 0.000, 'avg_unit_cost' => 12000.000000]
        );
        $balance->on_hand_qty = 500.000;
        $balance->save();
        $initialReserved = (float) $balance->reserved_qty;

        $uniqueNote = 'Uji reservasi stok B2B ' . uniqid();
        $response = $this->actingAs($this->salesUser)->post(route('b2b.orders.store'), [
            'member_id' => $this->b2bMember->id,
            'fulfillment_location_id' => $this->gudang->id,
            'payment_term' => 'CASH',
            'notes' => $uniqueNote,
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 50,
                    'unit_price' => 15000,
                ],
            ],
        ]);

        $response->assertSessionHasNoErrors();
        $order = B2bOrder::where('member_id', $this->b2bMember->id)
            ->where('notes', $uniqueNote)
            ->first();

        $this->assertNotNull($order);
        $this->assertEquals(B2bOrder::STATUS_RESERVED, $order->status);

        // Verify stock reservation created
        $this->assertDatabaseHas('stock_reservations', [
            'b2b_order_id' => $order->id,
            'product_id' => $this->product->id,
            'reserved_qty_base' => 50.000,
            'status' => StockReservation::STATUS_ACTIVE,
        ]);

        // Verify InventoryBalance reserved_qty incremented
        $balance->refresh();
        $this->assertEquals($initialReserved + 50.000, (float) $balance->reserved_qty);

        // Verify One-Time Pickup Ticket issued
        $this->assertDatabaseHas('pickup_tickets', [
            'b2b_order_id' => $order->id,
            'fulfillment_location_id' => $this->gudang->id,
            'status' => PickupTicket::STATUS_ISSUED,
        ]);
    }

    public function test_b2b_order_with_credit_term_exceeding_credit_limit_is_blocked(): void
    {
        // Set low credit limit
        $this->b2bMember->credit_limit = 1000000.00; // 1 Juta
        $this->b2bMember->save();

        // Attempt order of 5 Juta with tempo NET_14
        $response = $this->actingAs($this->salesUser)->post(route('b2b.orders.store'), [
            'member_id' => $this->b2bMember->id,
            'fulfillment_location_id' => $this->gudang->id,
            'payment_term' => 'NET_14',
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 100,
                    'unit_price' => 50000, // 5.000.000 > limit 1.000.000
                ],
            ],
        ]);

        $response->assertSessionHas('error');
        $this->assertStringContainsString('Plafon Kredit Terlampaui', session('error'));
    }

    public function test_b2b_order_with_overdue_invoice_is_blocked_from_credit_terms(): void
    {
        // Give plenty of credit limit so limit is not the issue
        $this->b2bMember->credit_limit = 50000000.00;
        $this->b2bMember->save();

        // Create an overdue invoice
        $invCode = 'INV-TEST-OVERDUE-' . uniqid();
        $overdueInv = Invoice::create([
            'invoice_number' => $invCode,
            'member_id' => $this->b2bMember->id,
            'invoice_date' => Carbon::today()->subDays(30),
            'due_date' => Carbon::today()->subDays(10), // Overdue 10 days
            'total_amount' => 500000.00,
            'paid_amount' => 0.00,
            'outstanding_amount' => 500000.00,
            'status' => Invoice::STATUS_OPEN,
        ]);

        // Attempt credit order NET_7
        $response = $this->actingAs($this->salesUser)->post(route('b2b.orders.store'), [
            'member_id' => $this->b2bMember->id,
            'fulfillment_location_id' => $this->gudang->id,
            'payment_term' => 'NET_7',
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 10,
                    'unit_price' => 15000,
                ],
            ],
        ]);

        $response->assertSessionHas('error');
        $this->assertStringContainsString('jatuh tempo', session('error'));

        // Clean up test invoice
        $overdueInv->delete();
    }

    public function test_b2b_order_with_cash_term_is_allowed_even_if_credit_limit_is_zero(): void
    {
        $this->b2bMember->credit_limit = 0.00;
        $this->b2bMember->save();

        $response = $this->actingAs($this->salesUser)->post(route('b2b.orders.store'), [
            'member_id' => $this->b2bMember->id,
            'fulfillment_location_id' => $this->gudang->id,
            'payment_term' => 'CASH', // Cash Before Delivery
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 10,
                    'unit_price' => 15000,
                ],
            ],
        ]);

        $response->assertSessionHasNoErrors();
    }

    public function test_one_time_qr_pickup_ticket_redemption_converts_reservation_to_stock_out_and_issues_invoice(): void
    {
        $uniqueOrder = 'B2B-TEST-PICKUP-' . uniqid();
        $order = B2bOrder::create([
            'order_number' => $uniqueOrder,
            'member_id' => $this->b2bMember->id,
            'creator_user_id' => $this->salesUser->id,
            'fulfillment_location_id' => $this->gudang->id,
            'payment_term' => B2bOrder::TERM_NET_14,
            'due_date' => Carbon::today()->addDays(14),
            'subtotal' => 300000.00,
            'total_amount' => 300000.00,
            'status' => B2bOrder::STATUS_RESERVED,
        ]);

        $orderItem = $order->items()->create([
            'product_id' => $this->product->id,
            'quantity_input' => 20.000,
            'quantity_base' => 20.000,
            'unit_price' => 15000.00,
            'subtotal' => 300000.00,
        ]);

        // Balance setup
        $balance = InventoryBalance::firstOrCreate(
            ['location_id' => $this->gudang->id, 'product_id' => $this->product->id],
            ['on_hand_qty' => 200.000, 'reserved_qty' => 0.000, 'avg_unit_cost' => 12000.000000]
        );
        $balance->on_hand_qty = 200.000;
        $balance->reserved_qty = 20.000;
        $balance->save();

        StockReservation::create([
            'b2b_order_id' => $order->id,
            'b2b_order_item_id' => $orderItem->id,
            'location_id' => $this->gudang->id,
            'product_id' => $this->product->id,
            'reserved_qty_base' => 20.000,
            'status' => StockReservation::STATUS_ACTIVE,
            'reserved_at' => now(),
        ]);

        $token = 'TK-' . strtoupper(Str::random(12));
        $ticket = PickupTicket::create([
            'b2b_order_id' => $order->id,
            'ticket_number' => 'TKT-TEST-' . uniqid(),
            'token_hash' => hash('sha256', $token),
            'raw_token_preview' => $token,
            'fulfillment_location_id' => $this->gudang->id,
            'status' => PickupTicket::STATUS_ISSUED,
            'issued_at' => now(),
        ]);

        // Staff Gudang redeems the ticket
        $this->actingAs($this->stafGudang);
        session(['active_location_id' => $this->gudang->id]);

        $response = $this->post(route('b2b.pickup.redeem'), [
            'token' => $token,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success');

        // Check ticket updated to REDEEMED
        $ticket->refresh();
        $this->assertEquals(PickupTicket::STATUS_REDEEMED, $ticket->status);
        $this->assertEquals($this->stafGudang->id, $ticket->redeemed_by_user_id);

        // Check order updated to HANDED_OVER
        $order->refresh();
        $this->assertEquals(B2bOrder::STATUS_HANDED_OVER, $order->status);

        // Check stock balance deducted (both on_hand and reserved)
        $balance->refresh();
        $this->assertEquals(180.000, (float) $balance->on_hand_qty);
        $this->assertEquals(0.000, (float) $balance->reserved_qty);

        // Check purple stock movement created
        $this->assertDatabaseHas('stock_movements', [
            'location_id' => $this->gudang->id,
            'product_id' => $this->product->id,
            'delta_qty_base' => -20.000,
            'movement_type' => 'B2B_PICKUP',
            'semantic_status' => 'outbound',
        ]);

        // Check Invoice issued
        $this->assertDatabaseHas('invoices', [
            'b2b_order_id' => $order->id,
            'member_id' => $this->b2bMember->id,
            'total_amount' => 300000.00,
            'status' => Invoice::STATUS_OPEN,
        ]);
    }

    public function test_double_redemption_of_pickup_ticket_is_strictly_rejected(): void
    {
        $token = 'TK-ALREADYUSED-' . uniqid();
        $order = B2bOrder::create([
            'order_number' => 'B2B-TEST-DOUBLE-' . uniqid(),
            'member_id' => $this->b2bMember->id,
            'creator_user_id' => $this->salesUser->id,
            'fulfillment_location_id' => $this->gudang->id,
            'payment_term' => B2bOrder::TERM_CASH,
            'total_amount' => 100000.00,
            'status' => B2bOrder::STATUS_HANDED_OVER,
        ]);

        PickupTicket::create([
            'b2b_order_id' => $order->id,
            'ticket_number' => 'TKT-TEST-USED-' . uniqid(),
            'token_hash' => hash('sha256', $token),
            'raw_token_preview' => $token,
            'fulfillment_location_id' => $this->gudang->id,
            'status' => PickupTicket::STATUS_REDEEMED,
            'issued_at' => now()->subDay(),
            'redeemed_at' => now()->subHours(2),
            'redeemed_by_user_id' => $this->stafGudang->id,
        ]);

        $this->actingAs($this->stafGudang);
        session(['active_location_id' => $this->gudang->id]);

        // Second attempt with same token
        $response = $this->post(route('b2b.pickup.redeem'), [
            'token' => $token,
        ]);

        $response->assertSessionHas('error');
        $this->assertStringContainsString('SUDAH DIGUNAKAN (Already Redeemed)', session('error'));
    }

    public function test_cancelling_b2b_order_releases_reserved_stock_and_revokes_ticket(): void
    {
        $order = B2bOrder::create([
            'order_number' => 'B2B-TEST-CANCEL-' . uniqid(),
            'member_id' => $this->b2bMember->id,
            'creator_user_id' => $this->salesUser->id,
            'fulfillment_location_id' => $this->gudang->id,
            'payment_term' => B2bOrder::TERM_NET_7,
            'total_amount' => 150000.00,
            'status' => B2bOrder::STATUS_RESERVED,
        ]);

        $orderItem = $order->items()->create([
            'product_id' => $this->product->id,
            'quantity_input' => 10.000,
            'quantity_base' => 10.000,
            'unit_price' => 15000.00,
            'subtotal' => 150000.00,
        ]);

        $balance = InventoryBalance::firstOrCreate(
            ['location_id' => $this->gudang->id, 'product_id' => $this->product->id],
            ['on_hand_qty' => 100.000, 'reserved_qty' => 10.000, 'avg_unit_cost' => 12000.000000]
        );
        $balance->reserved_qty = 10.000;
        $balance->save();

        StockReservation::create([
            'b2b_order_id' => $order->id,
            'b2b_order_item_id' => $orderItem->id,
            'location_id' => $this->gudang->id,
            'product_id' => $this->product->id,
            'reserved_qty_base' => 10.000,
            'status' => StockReservation::STATUS_ACTIVE,
            'reserved_at' => now(),
        ]);

        $ticket = PickupTicket::create([
            'b2b_order_id' => $order->id,
            'ticket_number' => 'TKT-TEST-CANCEL-' . uniqid(),
            'token_hash' => hash('sha256', 'TK-CANCEL' . uniqid()),
            'fulfillment_location_id' => $this->gudang->id,
            'status' => PickupTicket::STATUS_ISSUED,
            'issued_at' => now(),
        ]);

        $response = $this->actingAs($this->salesUser)->post(route('b2b.orders.cancel', $order), [
            'reason' => 'Mitra membatalkan pemesanan',
        ]);

        $response->assertSessionHas('success');

        $order->refresh();
        $this->assertEquals(B2bOrder::STATUS_CANCELLED, $order->status);

        $balance->refresh();
        $this->assertEquals(0.000, (float) $balance->reserved_qty);

        $ticket->refresh();
        $this->assertEquals(PickupTicket::STATUS_REVOKED, $ticket->status);
    }

    public function test_b2b_invoice_payment_settlement_updates_outstanding_balance(): void
    {
        $invoice = Invoice::create([
            'invoice_number' => 'INV-TEST-PAY-' . uniqid(),
            'member_id' => $this->b2bMember->id,
            'invoice_date' => Carbon::today(),
            'due_date' => Carbon::today()->addDays(7),
            'total_amount' => 1000000.00,
            'paid_amount' => 0.00,
            'outstanding_amount' => 1000000.00,
            'status' => Invoice::STATUS_OPEN,
        ]);

        // Pay partial 400.000
        $response = $this->actingAs($this->salesUser)->post(route('b2b.invoices.pay', $invoice), [
            'amount' => 400000,
            'payment_method' => 'TRANSFER',
            'reference_number' => 'TRF-BCA-TEST',
            'notes' => 'Cicilan pertama',
        ]);

        $response->assertSessionHas('success');

        $invoice->refresh();
        $this->assertEquals(400000.00, (float) $invoice->paid_amount);
        $this->assertEquals(600000.00, (float) $invoice->outstanding_amount);
        $this->assertEquals(Invoice::STATUS_PARTIALLY_PAID, $invoice->status);

        // Pay remaining 600.000 -> full settlement
        $this->post(route('b2b.invoices.pay', $invoice), [
            'amount' => 600000,
            'payment_method' => 'CASH',
            'reference_number' => 'CASH-SETTLE',
        ]);

        $invoice->refresh();
        $this->assertEquals(1000000.00, (float) $invoice->paid_amount);
        $this->assertEquals(0.00, (float) $invoice->outstanding_amount);
        $this->assertEquals(Invoice::STATUS_PAID, $invoice->status);
    }
}
