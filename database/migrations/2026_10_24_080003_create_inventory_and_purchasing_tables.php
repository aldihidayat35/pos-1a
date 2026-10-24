<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Inventory Balances (Real-time physical on-hand, reserved, and Moving Average HPP per Location & Product)
        Schema::create('inventory_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->constrained('locations')->restrictOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->decimal('on_hand_qty', 18, 3)->default(0.000);
            $table->decimal('reserved_qty', 18, 3)->default(0.000);
            $table->decimal('avg_unit_cost', 20, 6)->default(0.000000); // Moving Average Cost (HPP) per base UOM
            $table->unsignedBigInteger('version')->default(1);
            $table->timestamps();

            $table->unique(['location_id', 'product_id']);
            $table->index(['product_id', 'on_hand_qty']);
        });

        // 2. Stock Batches (Lot/Batch numbers, expiry tracking, FIFO basis)
        Schema::create('stock_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->constrained('locations')->restrictOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->string('batch_number', 100);
            $table->decimal('initial_qty', 18, 3)->default(0.000);
            $table->decimal('current_qty', 18, 3)->default(0.000);
            $table->decimal('unit_cost', 20, 6)->default(0.000000);
            $table->date('received_date');
            $table->date('expiry_date')->nullable();
            $table->string('status', 30)->default('ACTIVE'); // ACTIVE, DEPLETED, EXPIRED
            $table->timestamps();

            $table->index(['location_id', 'product_id', 'expiry_date']);
            $table->index(['batch_number']);
        });

        // 3. Stock Operations (Transaction Header for Atomic Operations)
        Schema::create('stock_operations', function (Blueprint $table) {
            $table->id();
            $table->uuid('operation_uuid')->unique();
            $table->string('operation_type', 50); // INBOUND_PO, OPENING_BALANCE, TRANSFER_DISPATCH, TRANSFER_RECEIVE, POS_SALE, ADJUSTMENT
            $table->string('document_type', 50);  // GOODS_RECEIPT, PURCHASE_ORDER, STOCK_TRANSFER, SALE, OPNAME
            $table->unsignedBigInteger('document_id')->nullable();
            $table->string('document_number', 100)->nullable();
            $table->foreignId('location_id')->constrained('locations')->restrictOnDelete();
            $table->foreignId('actor_user_id')->constrained('users')->restrictOnDelete();
            $table->timestamp('posted_at');
            $table->text('notes')->nullable();
            $table->string('idempotency_key', 100)->nullable();
            $table->timestamps();

            $table->index(['operation_type', 'document_type', 'document_id']);
            $table->index(['location_id', 'posted_at']);
        });

        // 4. Stock Movements (Immutable Append-Only Ledger with 5 Semantic Colors)
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operation_id')->constrained('stock_operations')->cascadeOnDelete();
            $table->foreignId('location_id')->constrained('locations')->restrictOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->decimal('delta_qty_base', 18, 3); // Positive for IN, Negative for OUT
            $table->decimal('unit_cost_snapshot', 20, 6)->default(0.000000);
            $table->decimal('amount_value', 20, 2)->default(0.00);
            $table->decimal('balance_after_qty', 18, 3)->default(0.000);
            $table->string('movement_type', 50); // INBOUND_PO, TRANSFER_IN, TRANSFER_OUT, POS_SALE, B2B_SALE, EMERGENCY_PURCHASE, OPNAME_ADJUSTMENT
            $table->string('semantic_status', 20)->default('GREEN'); // GREEN, BLUE, PURPLE, ORANGE, RED
            $table->timestamp('occurred_at');
            $table->timestamps();

            $table->index(['product_id', 'location_id', 'occurred_at']);
            $table->index(['movement_type', 'semantic_status']);
        });

        // 5. Purchase Orders
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('po_number', 60)->unique();
            $table->foreignId('supplier_id')->constrained('suppliers')->restrictOnDelete();
            $table->foreignId('destination_location_id')->constrained('locations')->restrictOnDelete();
            $table->string('status', 30)->default('DRAFT'); // DRAFT, ISSUED, PARTIALLY_RECEIVED, RECEIVED, CLOSED, CANCELLED
            $table->dateTime('ordered_at');
            $table->date('expected_delivery_at')->nullable();
            $table->decimal('subtotal', 20, 2)->default(0.00);
            $table->decimal('tax_amount', 20, 2)->default(0.00);
            $table->decimal('grand_total', 20, 2)->default(0.00);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['status', 'destination_location_id']);
            $table->index(['supplier_id', 'ordered_at']);
        });

        // 6. Purchase Order Items
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->foreignId('product_uom_id')->constrained('product_uoms')->restrictOnDelete();
            $table->decimal('quantity_ordered', 18, 3);
            $table->decimal('factor_to_base_snapshot', 20, 6)->default(1.000000);
            $table->decimal('unit_purchase_price', 20, 2)->default(0.00);
            $table->decimal('line_total', 20, 2)->default(0.00);
            $table->decimal('quantity_received_base', 18, 3)->default(0.000); // In base UOM
            $table->timestamps();

            $table->index(['purchase_order_id', 'product_id']);
        });

        // 7. Goods Receipts (Inbound Receiving Voucher)
        Schema::create('goods_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_number', 60)->unique();
            $table->string('receipt_type', 30)->default('PO'); // PO, EMERGENCY
            $table->foreignId('purchase_order_id')->nullable()->constrained('purchase_orders')->nullOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->string('supplier_name_snapshot', 255)->nullable();
            $table->string('supplier_do_number', 100)->nullable(); // Nomor Surat Jalan vendor
            $table->foreignId('location_id')->constrained('locations')->restrictOnDelete();
            $table->string('status', 30)->default('POSTED'); // DRAFT, POSTED, CANCELLED
            $table->text('notes')->nullable();
            $table->dateTime('posted_at')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();

            $table->index(['location_id', 'posted_at']);
            $table->index(['purchase_order_id', 'status']);
        });

        // 8. Goods Receipt Items
        Schema::create('goods_receipt_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('goods_receipt_id')->constrained('goods_receipts')->cascadeOnDelete();
            $table->foreignId('purchase_order_item_id')->nullable()->constrained('purchase_order_items')->nullOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->foreignId('product_uom_id')->constrained('product_uoms')->restrictOnDelete();
            $table->decimal('qty_input', 18, 3); // In selected UOM (e.g., 10 Karung)
            $table->decimal('factor_snapshot', 20, 6)->default(1.000000);
            $table->decimal('qty_base', 18, 3); // In base UOM (e.g., 500 kg)
            $table->decimal('unit_cost_input', 20, 2)->default(0.00); // Cost per input UOM
            $table->decimal('unit_cost_base', 20, 6)->default(0.000000); // Cost per base UOM
            $table->decimal('line_total', 20, 2)->default(0.00);
            $table->string('batch_number', 100)->nullable();
            $table->date('expiry_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['goods_receipt_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods_receipt_items');
        Schema::dropIfExists('goods_receipts');
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('stock_operations');
        Schema::dropIfExists('stock_batches');
        Schema::dropIfExists('inventory_balances');
    }
};
