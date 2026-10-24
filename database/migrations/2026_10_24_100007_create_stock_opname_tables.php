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
        Schema::create('stock_opnames', function (Blueprint $table) {
            $table->id();
            $table->string('opname_number', 50)->unique();
            $table->foreignId('location_id')->constrained('locations')->restrictOnDelete();
            $table->enum('scope_type', ['ALL', 'CATEGORY', 'CUSTOM'])->default('ALL');
            $table->foreignId('category_id')->nullable()->constrained('product_categories')->nullOnDelete();
            $table->enum('status', ['OPEN', 'COUNTING', 'RECONCILIATION', 'FINALIZED', 'CANCELLED'])->default('OPEN');
            $table->foreignId('opened_by_user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('finalized_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedInteger('total_items_counted')->default(0);
            $table->unsignedInteger('total_items_discrepancy')->default(0);
            $table->decimal('total_variance_value', 20, 2)->default(0.00);
            $table->decimal('total_loss_value', 20, 2)->default(0.00);
            $table->text('notes')->nullable();
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('reconciled_at')->nullable();
            $table->timestamp('finalized_at')->nullable();
            $table->timestamps();

            $table->index(['location_id', 'status']);
        });

        Schema::create('stock_opname_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_opname_id')->constrained('stock_opnames')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->foreignId('product_uom_id')->nullable()->constrained('product_uoms')->nullOnDelete();
            $table->decimal('system_qty_snapshot', 18, 3)->default(0.000);
            $table->decimal('physical_qty_counted', 18, 3)->nullable();
            $table->decimal('variance_qty', 18, 3)->default(0.000);
            $table->decimal('unit_cost_snapshot', 20, 6)->default(0.000000);
            $table->decimal('variance_value', 20, 2)->default(0.00);
            $table->enum('reason_code', [
                'NONE',
                'NATURAL_SHRINK',
                'DAMAGED_EXPIRED',
                'LOST_THEFT',
                'INPUT_ERROR',
                'FOUND_EXTRA',
            ])->default('NONE');
            $table->text('notes')->nullable();
            $table->foreignId('counter_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('counted_at')->nullable();
            $table->timestamps();

            $table->unique(['stock_opname_id', 'product_id']);
            $table->index(['stock_opname_id', 'reason_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_opname_items');
        Schema::dropIfExists('stock_opnames');
    }
};
