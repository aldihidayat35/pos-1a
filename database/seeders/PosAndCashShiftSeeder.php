<?php

namespace Database\Seeders;

use App\Models\CashShift;
use App\Models\InventoryBalance;
use App\Models\Location;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductUom;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use App\Models\StockOperation;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PosAndCashShiftSeeder extends Seeder
{
    public function run(): void
    {
        $stafToko = User::where('username', 'staftoko')->first();
        $store = Location::where('code', 'STR-PASAR-1')->first();

        if (!$stafToko || !$store) {
            return;
        }

        // 1. Create or retrieve active open shift for staf toko
        $shift = CashShift::firstOrCreate(
            ['shift_number' => 'SFT-20261024-0001'],
            [
                'location_id' => $store->id,
                'cashier_user_id' => $stafToko->id,
                'status' => 'OPEN',
                'opened_at' => now()->subHours(2),
                'starting_cash' => 250000.00,
                'notes' => 'Shift Pagi Kasir Toko Pasar Raya Blok A',
            ]
        );

        // 2. Ensure store inventory balance for sample products
        $product = Product::with(['productUoms', 'baseUom'])->where('sku', 'SKU-BRS-001')->first();
        if ($product) {
            $baseUom = $product->productUoms->where('is_base', true)->first();
            $bal = InventoryBalance::firstOrCreate(
                ['location_id' => $store->id, 'product_id' => $product->id],
                ['on_hand_qty' => 50.0, 'reserved_qty' => 0.0, 'avg_unit_cost' => 12500.0, 'version' => 1]
            );

            // 3. Create a sample completed POS sale
            $existingSale = Sale::where('sale_number', 'POS-20261024-0001')->first();
            if (!$existingSale && $baseUom) {
                $qty = 2.0;
                $price = 15000.00;
                $subtotal = $qty * $price;

                $sale = Sale::create([
                    'sale_number' => 'POS-20261024-0001',
                    'idempotency_key' => (string) Str::uuid(),
                    'location_id' => $store->id,
                    'cash_shift_id' => $shift->id,
                    'cashier_user_id' => $stafToko->id,
                    'customer_name' => 'Ibu Siti Khadijah',
                    'status' => 'POSTED',
                    'subtotal' => $subtotal,
                    'discount_amount' => 0.00,
                    'tax_amount' => 0.00,
                    'total_amount' => $subtotal,
                    'cogs_total' => $qty * (float) $bal->avg_unit_cost,
                    'payment_method' => 'CASH',
                    'payment_status' => 'PAID',
                    'paid_amount' => 50000.00,
                    'change_amount' => 20000.00,
                    'posted_at' => now()->subHour(),
                ]);

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'product_uom_id' => $baseUom->id,
                    'quantity_input' => $qty,
                    'factor_snapshot' => 1.0,
                    'quantity_base' => $qty,
                    'unit_price' => $price,
                    'floor_price_snapshot' => 13750.00,
                    'unit_cost_snapshot' => $bal->avg_unit_cost,
                    'subtotal' => $subtotal,
                    'cogs_amount' => $qty * (float) $bal->avg_unit_cost,
                ]);

                Payment::create([
                    'payment_number' => 'PAY-20261024-0001',
                    'sale_id' => $sale->id,
                    'payment_method' => 'CASH',
                    'amount' => $subtotal,
                    'received_by' => $stafToko->id,
                    'paid_at' => now()->subHour(),
                ]);

                // Record Stock Movement PURPLE
                $op = StockOperation::create([
                    'operation_uuid' => (string) Str::uuid(),
                    'operation_type' => 'SALE_POS',
                    'document_type' => 'SALE',
                    'document_id' => $sale->id,
                    'document_number' => $sale->sale_number,
                    'location_id' => $store->id,
                    'actor_user_id' => $stafToko->id,
                    'posted_at' => now()->subHour(),
                    'notes' => 'Penjualan Kasir POS Struk POS-20261024-0001',
                ]);

                StockMovement::create([
                    'operation_id' => $op->id,
                    'location_id' => $store->id,
                    'product_id' => $product->id,
                    'delta_qty_base' => -$qty,
                    'unit_cost_snapshot' => $bal->avg_unit_cost,
                    'amount_value' => $qty * (float) $bal->avg_unit_cost,
                    'balance_after_qty' => $bal->on_hand_qty,
                    'movement_type' => 'SALE_POS',
                    'semantic_status' => 'PURPLE',
                    'occurred_at' => now()->subHour(),
                ]);
            }
        }
    }
}
