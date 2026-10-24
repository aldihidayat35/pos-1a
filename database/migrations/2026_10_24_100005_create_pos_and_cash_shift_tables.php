<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for Fase 5 — POS dan Kasir
     */
    public function up(): void
    {
        // 1. Cash Shifts (Laci Kasir & Blind Cash Count Reconciliation)
        Schema::create('cash_shifts', function (Blueprint $table) {
            $table->id();
            $table->string('shift_number', 50)->unique();
            $table->foreignId('location_id')->constrained('locations')->restrictOnDelete();
            $table->foreignId('cashier_user_id')->constrained('users')->restrictOnDelete();
            $table->string('status', 30)->default('OPEN'); // OPEN, CLOSED, VALIDATED
            $table->dateTime('opened_at');
            $table->dateTime('closed_at')->nullable();
            $table->decimal('starting_cash', 20, 2)->default(0.00);
            $table->decimal('expected_cash', 20, 2)->nullable(); // starting_cash + cash_sales - cash_refunds
            $table->decimal('actual_cash', 20, 2)->nullable();   // entered by cashier at closing
            $table->decimal('difference', 20, 2)->nullable();    // actual_cash - expected_cash
            $table->text('notes')->nullable();
            $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('validated_at')->nullable();
            $table->timestamps();

            $table->index(['location_id', 'status']);
            $table->index(['cashier_user_id', 'status']);
        });

        // 2. Sales (POS Transactions Header)
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('sale_number', 50)->unique();
            $table->string('idempotency_key', 64)->nullable()->unique();
            $table->foreignId('location_id')->constrained('locations')->restrictOnDelete();
            $table->foreignId('cash_shift_id')->nullable()->constrained('cash_shifts')->nullOnDelete();
            $table->foreignId('cashier_user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('member_id')->nullable()->constrained('members')->nullOnDelete();
            $table->string('customer_name', 100)->default('Pelanggan Umum');
            $table->string('status', 30)->default('POSTED'); // POSTED, VOIDED
            $table->decimal('subtotal', 20, 2);
            $table->decimal('discount_amount', 20, 2)->default(0.00);
            $table->decimal('tax_amount', 20, 2)->default(0.00);
            $table->decimal('total_amount', 20, 2);
            $table->decimal('cogs_total', 20, 2)->default(0.00); // Snapshot HPP for profit/loss calculation
            $table->string('payment_method', 30)->default('CASH'); // CASH, QRIS, TRANSFER, DEBIT
            $table->string('payment_status', 30)->default('PAID'); // PAID, REFUNDED
            $table->decimal('paid_amount', 20, 2);
            $table->decimal('change_amount', 20, 2)->default(0.00);
            $table->dateTime('posted_at');
            $table->dateTime('voided_at')->nullable();
            $table->foreignId('voided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('void_reason')->nullable();
            $table->timestamps();

            $table->index(['location_id', 'posted_at']);
            $table->index(['cash_shift_id', 'status']);
            $table->index(['payment_method', 'posted_at']);
        });

        // 3. Sale Items (Detail Produk POS)
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('sales')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->foreignId('product_uom_id')->constrained('product_uoms')->restrictOnDelete();
            $table->decimal('quantity_input', 18, 3);
            $table->decimal('factor_snapshot', 20, 6)->default(1.000000);
            $table->decimal('quantity_base', 18, 3);
            $table->decimal('unit_price', 20, 2);
            $table->decimal('floor_price_snapshot', 20, 2)->default(0.00);
            $table->decimal('unit_cost_snapshot', 20, 6)->default(0.000000); // Snapshot Moving Average HPP
            $table->decimal('subtotal', 20, 2);
            $table->decimal('cogs_amount', 20, 2)->default(0.00);
            $table->timestamps();

            $table->index(['sale_id', 'product_id']);
        });

        // 4. Payments (Dokumentasi Pembayaran Transaksi)
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_number', 50)->unique();
            $table->foreignId('sale_id')->constrained('sales')->cascadeOnDelete();
            $table->string('payment_method', 30)->default('CASH');
            $table->decimal('amount', 20, 2);
            $table->string('reference_code', 100)->nullable(); // e.g. QRIS transaction ref / debit auth
            $table->foreignId('received_by')->constrained('users')->restrictOnDelete();
            $table->dateTime('paid_at');
            $table->timestamps();

            $table->index(['sale_id', 'payment_method']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('sale_items');
        Schema::dropIfExists('sales');
        Schema::dropIfExists('cash_shifts');
    }
};
