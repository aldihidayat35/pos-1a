<?php

namespace Database\Seeders;

use App\Models\B2bOrder;
use App\Models\B2bOrderItem;
use App\Models\InventoryBalance;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Location;
use App\Models\Member;
use App\Models\PickupTicket;
use App\Models\Product;
use App\Models\StockReservation;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class B2bSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $salesUser = User::where('username', 'sales')->first() ?? User::first();
        $stafToko = User::where('username', 'staftoko')->first() ?? User::first();
        $gudang = Location::where('code', 'GUD-PUSAT')->first() ?? Location::first();
        $toko = Location::where('code', 'TOK-PASAR')->first() ?? Location::first();

        $member1 = Member::where('member_code', 'MBR-B2B-0001')->first();
        $member2 = Member::where('member_code', 'MBR-B2B-0002')->first();
        $product1 = Product::where('sku', 'SKU-BRS-001')->first() ?? Product::first();
        $product2 = Product::where('sku', 'SKU-GLA-001')->first() ?? Product::find(2);

        if (!$member1 || !$gudang || !$product1) {
            return;
        }

        // 1. Order 1: Active Reserved Order (Ready for pickup demo)
        $orderNum1 = 'B2B-' . date('Ymd') . '-0001';
        $order1 = B2bOrder::firstOrCreate(
            ['order_number' => $orderNum1],
            [
                'member_id' => $member1->id,
                'creator_user_id' => $salesUser->id,
                'fulfillment_location_id' => $gudang->id,
                'payment_term' => B2bOrder::TERM_NET_14,
                'due_date' => Carbon::today()->addDays(14),
                'subtotal' => 2850000.00,
                'discount_amount' => 150000.00,
                'tax_amount' => 0.00,
                'total_amount' => 2700000.00,
                'status' => B2bOrder::STATUS_RESERVED,
                'notes' => 'Pesanan grosir 200kg beras premium siap ambil di Gudang Pusat.',
                'placed_at' => now()->subHours(3),
                'ready_at' => now()->subHours(2),
            ]
        );

        $orderItem1 = B2bOrderItem::firstOrCreate(
            [
                'b2b_order_id' => $order1->id,
                'product_id' => $product1->id,
            ],
            [
                'quantity_input' => 200.000,
                'quantity_base' => 200.000,
                'unit_price' => 14250.00,
                'cogs_snapshot' => 12500.000000,
                'discount_percent' => 5.00,
                'subtotal' => 2700000.00,
            ]
        );

        // Reserve stock on InventoryBalance
        $balance1 = InventoryBalance::where('location_id', $gudang->id)
            ->where('product_id', $product1->id)
            ->first();

        if ($balance1) {
            $balance1->reserved_qty = (float) $balance1->reserved_qty + 200.000;
            $balance1->save();
        }

        StockReservation::firstOrCreate(
            [
                'b2b_order_id' => $order1->id,
                'b2b_order_item_id' => $orderItem1->id,
            ],
            [
                'location_id' => $gudang->id,
                'product_id' => $product1->id,
                'reserved_qty_base' => 200.000,
                'status' => StockReservation::STATUS_ACTIVE,
                'reserved_at' => now()->subHours(2),
            ]
        );

        $token1 = 'TK-DEMO7702';
        PickupTicket::firstOrCreate(
            ['b2b_order_id' => $order1->id],
            [
                'ticket_number' => 'TKT-' . date('Ymd') . '-0001',
                'token_hash' => hash('sha256', $token1),
                'raw_token_preview' => $token1,
                'fulfillment_location_id' => $gudang->id,
                'status' => PickupTicket::STATUS_ISSUED,
                'issued_at' => now()->subHours(2),
                'expires_at' => Carbon::now()->addDays(7),
            ]
        );

        // 2. Order 2: Completed Handed Over Order with Invoice
        if ($member2 && $product2) {
            $orderNum2 = 'B2B-' . date('Ymd', strtotime('-3 days')) . '-0002';
            $order2 = B2bOrder::firstOrCreate(
                ['order_number' => $orderNum2],
                [
                    'member_id' => $member2->id,
                    'creator_user_id' => $salesUser->id,
                    'fulfillment_location_id' => $toko->id,
                    'payment_term' => B2bOrder::TERM_NET_7,
                    'due_date' => Carbon::today()->addDays(4),
                    'subtotal' => 1500000.00,
                    'discount_amount' => 30000.00,
                    'tax_amount' => 0.00,
                    'total_amount' => 1470000.00,
                    'status' => B2bOrder::STATUS_HANDED_OVER,
                    'notes' => 'Pesanan gula kemasan sudah diambil oleh pemilik warung.',
                    'placed_at' => now()->subDays(3),
                    'ready_at' => now()->subDays(3),
                    'handed_over_at' => now()->subDays(2),
                ]
            );

            B2bOrderItem::firstOrCreate(
                [
                    'b2b_order_id' => $order2->id,
                    'product_id' => $product2->id,
                ],
                [
                    'quantity_input' => 100.000,
                    'quantity_base' => 100.000,
                    'unit_price' => 14700.00,
                    'cogs_snapshot' => 13000.000000,
                    'discount_percent' => 2.00,
                    'subtotal' => 1470000.00,
                ]
            );

            PickupTicket::firstOrCreate(
                ['b2b_order_id' => $order2->id],
                [
                    'ticket_number' => 'TKT-' . date('Ymd', strtotime('-3 days')) . '-0002',
                    'token_hash' => hash('sha256', 'TK-REDEEMED99'),
                    'raw_token_preview' => 'TK-REDEEMED99',
                    'fulfillment_location_id' => $toko->id,
                    'status' => PickupTicket::STATUS_REDEEMED,
                    'issued_at' => now()->subDays(3),
                    'expires_at' => Carbon::now()->addDays(4),
                    'redeemed_at' => now()->subDays(2),
                    'redeemed_by_user_id' => $stafToko->id,
                ]
            );

            // Invoice with partial payment
            $inv = Invoice::firstOrCreate(
                ['invoice_number' => 'INV-B2B-' . date('Ymd', strtotime('-3 days')) . '-0001'],
                [
                    'b2b_order_id' => $order2->id,
                    'member_id' => $member2->id,
                    'invoice_date' => Carbon::today()->subDays(3),
                    'due_date' => Carbon::today()->addDays(4),
                    'total_amount' => 1470000.00,
                    'paid_amount' => 500000.00,
                    'outstanding_amount' => 970000.00,
                    'status' => Invoice::STATUS_PARTIALLY_PAID,
                ]
            );

            InvoicePayment::firstOrCreate(
                [
                    'invoice_id' => $inv->id,
                    'reference_number' => 'DP-B2B-WARUNG',
                ],
                [
                    'member_id' => $member2->id,
                    'amount' => 500000.00,
                    'payment_method' => 'TRANSFER',
                    'recorded_by_id' => $salesUser->id,
                    'notes' => 'Uang muka pembayaran tempo 500rb via transfer BCA',
                    'paid_at' => now()->subDays(2),
                ]
            );
        }
    }
}
