<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RbacTest extends TestCase
{
    public function test_all_eight_roles_exist(): void
    {
        $expectedRoles = [
            'super_admin',
            'owner',
            'kepala_toko',
            'kepala_gudang',
            'staf_gudang',
            'staf_toko',
            'sales',
            'b2b_mitra',
        ];

        foreach ($expectedRoles as $roleName) {
            $this->assertDatabaseHas('roles', ['name' => $roleName]);
        }
    }

    public function test_owner_is_read_only_and_has_no_mutation_permissions(): void
    {
        $ownerRole = Role::where('name', 'owner')->first();
        $this->assertNotNull($ownerRole);

        $this->assertTrue($ownerRole->permissions->contains('name', 'dashboard.view'));
        $this->assertTrue($ownerRole->permissions->contains('name', 'dashboard.financial'));
        $this->assertTrue($ownerRole->permissions->contains('name', 'dashboard.audit'));

        // Owner MUST NOT have mutation permissions per PRD zero-bottleneck rule
        $this->assertFalse($ownerRole->permissions->contains('name', 'emergency.input'));
        $this->assertFalse($ownerRole->permissions->contains('name', 'opname.finalize'));
        $this->assertFalse($ownerRole->permissions->contains('name', 'pos.access'));
        $this->assertFalse($ownerRole->permissions->contains('name', 'pos.void'));
        $this->assertFalse($ownerRole->permissions->contains('name', 'purchasing.po'));
    }

    public function test_kepala_toko_has_store_and_emergency_permissions_but_no_po(): void
    {
        $role = Role::where('name', 'kepala_toko')->first();
        $this->assertNotNull($role);

        $this->assertTrue($role->permissions->contains('name', 'pos.access'));
        $this->assertTrue($role->permissions->contains('name', 'emergency.input'));
        $this->assertTrue($role->permissions->contains('name', 'opname.finalize'));
        $this->assertFalse($role->permissions->contains('name', 'purchasing.po'));
    }

    public function test_kepala_gudang_has_warehouse_and_po_permissions_but_no_pos(): void
    {
        $role = Role::where('name', 'kepala_gudang')->first();
        $this->assertNotNull($role);

        $this->assertTrue($role->permissions->contains('name', 'purchasing.po'));
        $this->assertTrue($role->permissions->contains('name', 'transfer.dispatch'));
        $this->assertTrue($role->permissions->contains('name', 'opname.finalize'));
        $this->assertFalse($role->permissions->contains('name', 'pos.access'));
    }

    public function test_server_side_permission_middleware_blocks_unauthorized_user(): void
    {
        // Define a protected test route on the fly
        Route::get('/test-protected-route', function () {
            return response('Protected Action Executed', 200);
        })->middleware(['web', 'auth', 'permission:emergency.input']);

        $stafGudang = User::where('username', 'stafgudang')->first();
        $this->assertNotNull($stafGudang);

        // Staf Gudang does NOT have 'emergency.input' permission -> Expect 403 Forbidden
        $response = $this->actingAs($stafGudang)->get('/test-protected-route');
        $response->assertStatus(403);

        // Kepala Toko DOES have 'emergency.input' permission -> Expect 200 OK
        $kepalaToko = User::where('username', 'kepalatoko')->first();
        $this->assertNotNull($kepalaToko);

        $responseAuthorized = $this->actingAs($kepalaToko)->get('/test-protected-route');
        $responseAuthorized->assertStatus(200);
        $responseAuthorized->assertSee('Protected Action Executed');
    }
}
