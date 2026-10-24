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
        // 1. Alter users table to add system fields
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique()->after('name');
            $table->string('phone', 30)->nullable()->after('email');
            $table->string('preferred_locale', 10)->default('id')->after('phone');
            $table->boolean('is_active')->default(true)->after('remember_token');
        });

        // 2. Roles table
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g. super_admin, owner, kepala_toko, etc.
            $table->string('label'); // Display name e.g. "Kepala Toko"
            $table->string('description')->nullable();
            $table->boolean('is_system')->default(true);
            $table->timestamps();
        });

        // 3. Permissions table
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g. 'dashboard.view', 'pos.sale', etc.
            $table->string('label');
            $table->string('module', 50); // Grouping: auth, inventory, pos, b2b, opname, report, admin
            $table->timestamps();
        });

        // 4. Pivot user_roles
        Schema::create('user_roles', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->primary(['user_id', 'role_id']);
            $table->timestamp('assigned_at')->useCurrent();
        });

        // 5. Pivot role_permissions
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->primary(['role_id', 'permission_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('user_roles');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'phone', 'preferred_locale', 'is_active']);
        });
    }
};
