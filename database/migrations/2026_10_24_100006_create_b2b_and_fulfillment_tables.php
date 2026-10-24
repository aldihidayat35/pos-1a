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
        // 1. B2B Orders (Header)
        Schema::create('b2b_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 64)->unique();
            $table->foreignId('member_id')->constrained('members')->restrictOnDelete();
            $table->foreignId('creator_user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('fulfillment_location_id')->constrained('locations')->restrictOnDelete();
            $table->enum('payment_term', ['CASH', 'NET_7', 'NET_14', 'NET_30'])->default('CASH');
            $table->date('due_date')->nullable();
            $table->decimal('subtotal', 20, 2)->default(0.00);
            $table->decimal('discount_amount', 20, 2)->default(0.00);
            $table->decimal('tax_amount', 20, 2)->default(0.00);
            $table->decimal('total_amount', 20, 2)->default(0.00);
            $table->enum('status', [
                'DRAFT',
                'PLACED',
                'RESERVED',
                'READY_FOR_PICKUP',
                'HANDED_OVER',
                'CANCELLED'
            ])->default('PLACED');
            $table->text('notes')->nullable();
            $table->timestamp('placed_at')->nullable();
            $table->timestamp('ready_at')->nullable();
            $table->timestamp('handed_over_at')->nullable();
            $table->timestamps();

            $table->index(['member_id', 'status']);
            $table->index(['fulfillment_location_id', 'status']);
            $table->index('order_number');
        });

        // 2. B2B Order Items (Line Items)
        Schema::create('b2b_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('b2b_order_id')->constrained('b2b_orders')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->foreignId('product_uom_id')->nullable()->constrained('product_uoms')->nullOnDelete();
            $table->decimal('quantity_input', 18, 3);
            $table->decimal('quantity_base', 18, 3);
            $table->decimal('unit_price', 20, 2);
            $table->decimal('cogs_snapshot', 20, 6)->default(0.000000);
            $table->decimal('discount_percent', 5, 2)->default(0.00);
            $table->decimal('subtotal', 20, 2);
            $table->timestamps();

            $table->index(['b2b_order_id', 'product_id']);
        });

        // 3. Stock Reservations (Protects physical stock from POS and competing orders)
        Schema::create('stock_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('b2b_order_id')->constrained('b2b_orders')->cascadeOnDelete();
            $table->foreignId('b2b_order_item_id')->constrained('b2b_order_items')->cascadeOnDelete();
            $table->foreignId('location_id')->constrained('locations')->restrictOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->decimal('reserved_qty_base', 18, 3);
            $table->enum('status', ['ACTIVE', 'FULFILLED', 'RELEASED'])->default('ACTIVE');
            $table->timestamp('reserved_at');
            $table->timestamp('released_at')->nullable();
            $table->timestamps();

            $table->index(['location_id', 'product_id', 'status']);
        });

        // 4. One-Time QR Pickup Tickets (Cryptographic hash token, single-use redemption)
        Schema::create('pickup_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('b2b_order_id')->constrained('b2b_orders')->cascadeOnDelete();
            $table->string('ticket_number', 64)->unique();
            $table->string('token_hash', 64)->unique(); // SHA-256 of plain token
            $table->string('raw_token_preview', 32)->nullable();
            $table->foreignId('fulfillment_location_id')->constrained('locations')->restrictOnDelete();
            $table->enum('status', ['ISSUED', 'REDEEMED', 'EXPIRED', 'REVOKED'])->default('ISSUED');
            $table->timestamp('issued_at');
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('redeemed_at')->nullable();
            $table->foreignId('redeemed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['fulfillment_location_id', 'status']);
            $table->index('token_hash');
        });

        // 5. Invoices (Accounts Receivable Ledger for B2B)
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 64)->unique();
            $table->foreignId('b2b_order_id')->nullable()->constrained('b2b_orders')->nullOnDelete();
            $table->foreignId('member_id')->constrained('members')->restrictOnDelete();
            $table->date('invoice_date');
            $table->date('due_date');
            $table->decimal('total_amount', 20, 2);
            $table->decimal('paid_amount', 20, 2)->default(0.00);
            $table->decimal('outstanding_amount', 20, 2);
            $table->enum('status', ['OPEN', 'PARTIALLY_PAID', 'PAID', 'OVERDUE'])->default('OPEN');
            $table->timestamps();

            $table->index(['member_id', 'status']);
            $table->index('due_date');
        });

        // 6. Invoice Payments (Payment allocations for credit settlement)
        Schema::create('invoice_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->foreignId('member_id')->constrained('members')->restrictOnDelete();
            $table->decimal('amount', 20, 2);
            $table->enum('payment_method', ['CASH', 'TRANSFER', 'GIRO', 'OTHER'])->default('TRANSFER');
            $table->string('reference_number', 100)->nullable();
            $table->foreignId('recorded_by_id')->constrained('users')->restrictOnDelete();
            $table->text('notes')->nullable();
            $table->timestamp('paid_at');
            $table->timestamps();

            $table->index(['invoice_id', 'paid_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_payments');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('pickup_tickets');
        Schema::dropIfExists('stock_reservations');
        Schema::dropIfExists('b2b_order_items');
        Schema::dropIfExists('b2b_orders');
    }
};
