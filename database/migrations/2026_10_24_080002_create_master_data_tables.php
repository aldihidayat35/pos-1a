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
        // 1. Product Categories (Hierarchical tree support)
        Schema::create('product_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('product_categories')->nullOnDelete();
            $table->string('code', 50)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Units of Measurement (UOM)
        Schema::create('uoms', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique(); // e.g. KG, G, PCS, LTR, ML, BOX
            $table->string('name');              // e.g. Kilogram, Gram, Piece, Liter
            $table->string('symbol', 15);        // e.g. kg, g, pcs, L, ml
            $table->enum('dimension', ['COUNT', 'WEIGHT', 'VOLUME']);
            $table->unsignedTinyInteger('precision')->default(0); // Decimal display precision
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. Products
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku', 60)->unique();
            $table->string('name');
            $table->string('normalized_name'); // For fuzzy duplicate similarity matching
            $table->foreignId('category_id')->constrained('product_categories')->restrictOnDelete();
            $table->foreignId('base_uom_id')->constrained('uoms')->restrictOnDelete();
            $table->text('description')->nullable();
            $table->boolean('track_stock')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('normalized_name');
            $table->index('is_active');
        });

        // 4. Product UOM Conversions & Barcodes
        Schema::create('product_uoms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('uom_id')->constrained('uoms')->restrictOnDelete();
            $table->decimal('factor_to_base', 20, 6)->default(1.000000); // 1 for base, multiplier for packages
            $table->string('barcode', 80)->nullable()->unique();
            $table->boolean('is_purchase')->default(true);
            $table->boolean('is_sale')->default(true);
            $table->boolean('is_base')->default(false);
            $table->timestamps();

            $table->unique(['product_id', 'uom_id']);
        });

        // 5. Suppliers
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('supplier_code', 50)->unique();
            $table->string('name');
            $table->string('contact_person')->nullable();
            $table->string('phone', 35)->nullable();
            $table->string('email', 100)->nullable();
            $table->text('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 6. Member Tiers (Pricing & Credit Tiers)
        Schema::create('member_tiers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique(); // e.g. REGULAR, SILVER, GOLD
            $table->string('name');
            $table->unsignedSmallInteger('priority')->default(1);
            $table->decimal('discount_percent', 5, 2)->default(0.00);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 7. Members (Retail & B2B Customers)
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('member_code', 60)->unique(); // Digital QR / barcode token
            $table->string('name');
            $table->enum('type', ['RETAIL', 'B2B'])->default('RETAIL');
            $table->foreignId('tier_id')->nullable()->constrained('member_tiers')->nullOnDelete();
            $table->decimal('credit_limit', 20, 2)->default(0.00); // Max tempo limit for B2B
            $table->string('phone', 35)->nullable();
            $table->string('email', 100)->nullable();
            $table->text('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['type', 'is_active']);
        });

        // 8. Member Users Pivot (for B2B portal login assignment)
        Schema::create('member_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('contact_role', 50)->nullable(); // e.g. PIC, Purchasing, Owner
            $table->timestamps();

            $table->unique(['member_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_users');
        Schema::dropIfExists('members');
        Schema::dropIfExists('member_tiers');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('product_uoms');
        Schema::dropIfExists('products');
        Schema::dropIfExists('uoms');
        Schema::dropIfExists('product_categories');
    }
};
