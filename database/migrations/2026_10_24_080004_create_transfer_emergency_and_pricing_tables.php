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
        // 1. Stock Transfers (Warehouse <-> Store with Virtual Transit)
        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->id();
            $table->string('transfer_number', 60)->unique();
            $table->foreignId('from_location_id')->constrained('locations')->restrictOnDelete();
            $table->foreignId('to_location_id')->constrained('locations')->restrictOnDelete();
            $table->foreignId('transit_location_id')->constrained('locations')->restrictOnDelete();
            $table->string('status', 30)->default('DRAFT'); // DRAFT, DISPATCHED, PARTIALLY_RECEIVED, RECEIVED, CANCELLED
            $table->text('notes')->nullable();
            $table->dateTime('dispatched_at')->nullable();
            $table->dateTime('received_at')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['from_location_id', 'status']);
            $table->index(['to_location_id', 'status']);
        });

        // 2. Stock Transfer Items
        Schema::create('stock_transfer_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_transfer_id')->constrained('stock_transfers')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->foreignId('product_uom_id')->constrained('product_uoms')->restrictOnDelete();
            $table->decimal('requested_qty_input', 18, 3);
            $table->decimal('factor_snapshot', 20, 6)->default(1.000000);
            $table->decimal('requested_qty_base', 18, 3);
            $table->decimal('dispatched_qty_base', 18, 3)->default(0.000);
            $table->decimal('received_qty_base', 18, 3)->default(0.000);
            $table->decimal('cost_snapshot', 20, 6)->default(0.000000); // Unit cost carried over from origin
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['stock_transfer_id', 'product_id']);
        });

        // 3. Emergency Purchases (In-store urgent purchases without Owner approval, photo required)
        Schema::create('emergency_purchases', function (Blueprint $table) {
            $table->id();
            $table->string('purchase_number', 60)->unique();
            $table->foreignId('location_id')->constrained('locations')->restrictOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->foreignId('product_uom_id')->constrained('product_uoms')->restrictOnDelete();
            $table->decimal('qty_input', 18, 3);
            $table->decimal('factor_snapshot', 20, 6)->default(1.000000);
            $table->decimal('qty_base', 18, 3);
            $table->decimal('unit_cost_input', 20, 2);
            $table->decimal('unit_cost_base', 20, 6);
            $table->decimal('total_amount', 20, 2);
            $table->string('merchant_name', 150); // Vendor / pasar pedagang ad-hoc
            $table->string('receipt_photo_path', 255); // Photo proof path (mandatory)
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->dateTime('posted_at');
            $table->timestamps();

            $table->index(['location_id', 'posted_at']);
        });

        // 4. Anomaly Events (Owner visibility board & semantic audit feed)
        Schema::create('anomaly_events', function (Blueprint $table) {
            $table->id();
            $table->string('severity', 20)->default('ORANGE'); // ORANGE, RED
            $table->string('kind', 50); // EMERGENCY_PURCHASE, PRICE_FLOOR_BREACH, NEGATIVE_STOCK, DISCREPANCY
            $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->foreignId('actor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('reference_type', 60)->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('reference_number', 100)->nullable();
            $table->decimal('amount', 20, 2)->nullable();
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->dateTime('occurred_at');
            $table->dateTime('acknowledged_at')->nullable();
            $table->timestamps();

            $table->index(['severity', 'occurred_at']);
            $table->index(['location_id', 'kind']);
        });

        // 5. Pricing Policies (Scope, Floor Markup Policy)
        Schema::create('pricing_policies', function (Blueprint $table) {
            $table->id();
            $table->string('scope', 30)->default('GLOBAL'); // GLOBAL, LOCATION
            $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->string('channel', 30)->default('RETAIL'); // RETAIL, B2B
            $table->enum('min_markup_type', ['PERCENTAGE', 'FIXED'])->default('PERCENTAGE');
            $table->decimal('min_markup_value', 10, 2)->default(5.00); // e.g. 5.00% or Rp 500.00
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['channel', 'is_active']);
        });

        // 6. Price Rules (List Price, Market Min & Max Range per Product)
        Schema::create('price_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->string('channel', 30)->default('RETAIL'); // RETAIL, B2B
            $table->foreignId('tier_id')->nullable()->constrained('member_tiers')->nullOnDelete();
            $table->decimal('list_price', 20, 2); // Base selling list price
            $table->decimal('market_min_price', 20, 2); // Market floor price guideline
            $table->decimal('market_max_price', 20, 2); // Market ceiling price guideline
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['product_id', 'channel', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_rules');
        Schema::dropIfExists('pricing_policies');
        Schema::dropIfExists('anomaly_events');
        Schema::dropIfExists('emergency_purchases');
        Schema::dropIfExists('stock_transfer_items');
        Schema::dropIfExists('stock_transfers');
    }
};
