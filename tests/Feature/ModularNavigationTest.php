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
            ->assertSee('Ringkasan Utama')
            ->assertSee('Stream Masuk (Hijau)')
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
            ->assertSee('Penerimaan PO')
            ->assertSee('Konversi Satuan')
            ->assertSee('translateY(68px)');
    }

    public function test_authenticated_user_can_access_store_page(): void
    {
        $user = User::where('username', 'superadmin')->first();
        $this->actingAs($user)
            ->get('/store')
            ->assertStatus(200)
            ->assertSee('Operasional Toko & Kasir POS')
            ->assertSee('Kasir POS Ritel')
            ->assertSee('Stok Emergency')
            ->assertSee('translateY(136px)');
    }

    public function test_authenticated_user_can_access_b2b_page(): void
    {
        $user = User::where('username', 'superadmin')->first();
        $this->actingAs($user)
            ->get('/b2b')
            ->assertStatus(200)
            ->assertSee('Kemitraan B2B & Sales')
            ->assertSee('Rekanan Grosir')
            ->assertSee('Plafon Kredit')
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
            ->assertSee('Semua Anomali')
            ->assertSee('translateY(272px)');
    }

    public function test_authenticated_user_can_access_master_products_page(): void
    {
        $user = User::where('username', 'superadmin')->first();
        $this->actingAs($user)
            ->get('/master/products')
            ->assertStatus(200)
            ->assertSee('Katalog Master Produk')
            ->assertSee('Kategori')
            ->assertSee('Satuan UOM')
            ->assertSee('Suplayer')
            ->assertSee('Member &amp; B2B', false)
            ->assertSee('translateY(340px)');
    }

    public function test_header_sub_menu_switches_active_item_on_master_categories(): void
    {
        $user = User::where('username', 'superadmin')->first();
        $response = $this->actingAs($user)->get('/master/categories');
        
        $response->assertStatus(200);
        $response->assertSee('Kategori Master Produk');
        // Check that 'Kategori' is active in header
        $response->assertSee('bg-white text-sidebar font-bold');
    }
}
