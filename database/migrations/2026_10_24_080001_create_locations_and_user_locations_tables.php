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
        // 1. Locations table (Warehouse, Store, Transit)
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique(); // e.g. WH-CENTRAL, STR-PASAR-1, LOC-TRANSIT
            $table->string('name');
            $table->enum('type', ['WAREHOUSE', 'STORE', 'TRANSIT']);
            $table->string('phone', 30)->nullable();
            $table->text('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. User Locations pivot (multi-location assignment with default flag)
        Schema::create('user_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('location_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'location_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_locations');
        Schema::dropIfExists('locations');
    }
};
