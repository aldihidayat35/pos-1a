<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ModularNavigationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
        $this->get('/warehouse')->assertRedirect('/login');
        $this->get('/store')->assertRedirect('/login');
        $this->get('/b2b')->assertRedirect('/login');
        $this->get('/audit')->assertRedirect('/login');
        $this->get('/master/products')->assertRedirect('/login');
    }

    public function test_authenticated_user_can_access_dashboard_page(): void
    {
        $user = User::where('username', 'superadmin')->first();
        $this->actingAs($user)
            ->get('/dashboard')
            ->assertStatus(200)
            ->assertSee('PasarHub')
            ->assertSee('Halo,')
            ->assertDontSee('id="page-1"')
            ->assertDontSee('id="page-2"');
    }

    public function test_authenticated_user_can_access_warehouse_page(): void
    {
        $user = User::where('username', 'superadmin')->first();
        $this->actingAs($user)
            ->get('/warehouse')
            ->assertStatus(200)
            ->assertSee('Operasional Gudang Pusat')
            ->assertSee('translateY(68px)');
    }

    public function test_authenticated_user_can_access_store_page(): void
    {
        $user = User::where('username', 'superadmin')->first();
        $this->actingAs($user)
            ->get('/store')
            ->assertStatus(200)
            ->assertSee('Operasional Toko & Kasir POS')
            ->assertSee('translateY(136px)');
    }

    public function test_authenticated_user_can_access_b2b_page(): void
    {
        $user = User::where('username', 'superadmin')->first();
        $this->actingAs($user)
            ->get('/b2b')
            ->assertStatus(200)
            ->assertSee('Kemitraan B2B & Sales')
            ->assertSee('translateY(204px)');
    }

    public function test_authenticated_user_can_access_audit_page(): void
    {
        $user = User::where('username', 'superadmin')->first();
        $this->actingAs($user)
            ->get('/audit')
            ->assertStatus(200)
            ->assertSee('Audit Trail')
            ->assertSee('Laporan Anomali')
            ->assertSee('translateY(272px)');
    }

    public function test_authenticated_user_can_access_master_products_page(): void
    {
        $user = User::where('username', 'superadmin')->first();
        $this->actingAs($user)
            ->get('/master/products')
            ->assertStatus(200)
            ->assertSee('Katalog Master Produk')
            ->assertSee('translateY(340px)');
    }
}
