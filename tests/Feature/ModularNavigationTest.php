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
            ->assertSee('Kemitraan B2B &amp; Plafon Piutang', false)
            ->assertSee('Dasbor &amp; Mitra', false)
            ->assertSee('Buat Order B2B')
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

    public function test_header_elements_removed_and_desktop_overflow_present(): void
    {
        $user = User::where('username', 'superadmin')->first();
        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        // Movement colors and notification bell removed from header
        $response->assertDontSee('data-i18n="header_color_legend"');
        $response->assertDontSee('title="Notifikasi Anomali & Peringatan Sistem"');

        // Desktop overflow arrow button & dropdown present
        $response->assertSee('id="desktopOverflowWrapper"', false);
        $response->assertSee('id="desktopOverflowDropdown"', false);
    }

    public function test_master_pages_render_create_links_and_dedicated_create_pages(): void
    {
        $user = User::where('username', 'superadmin')->first();

        // 1. Categories Index & Create Page
        $catRes = $this->actingAs($user)->get('/master/categories');
        $catRes->assertStatus(200);
        $catRes->assertSee(route('master.categories.create'), false);
        $catRes->assertDontSee('openAddModal()');
        $catRes->assertDontSee('id="addCategoryModal"', false);
        $catRes->assertSee('data-async-table="categories-table"', false);
        $catRes->assertDontSee('lg:col-span-4');

        $catCreateRes = $this->actingAs($user)->get('/master/categories/create');
        $catCreateRes->assertStatus(200);
        $catCreateRes->assertSee('Formulir Kategori Baru');
        $catCreateRes->assertSee('Simpan Kategori');

        // 2. UOMs Index & Create Page
        $uomRes = $this->actingAs($user)->get('/master/uoms');
        $uomRes->assertStatus(200);
        $uomRes->assertSee(route('master.uoms.create'), false);
        $uomRes->assertDontSee('openAddModal()');
        $uomRes->assertDontSee('id="addUomModal"', false);
        $uomRes->assertSee('data-async-table="uoms-table"', false);
        $uomRes->assertDontSee('lg:col-span-4');

        $uomCreateRes = $this->actingAs($user)->get('/master/uoms/create');
        $uomCreateRes->assertStatus(200);
        $uomCreateRes->assertSee('Formulir Satuan UOM Baru');
        $uomCreateRes->assertSee('Simpan Satuan UOM');

        // 3. Suppliers Index & Create Page
        $supRes = $this->actingAs($user)->get('/master/suppliers');
        $supRes->assertStatus(200);
        $supRes->assertSee(route('master.suppliers.create'), false);
        $supRes->assertDontSee('openAddModal()');
        $supRes->assertDontSee('id="addSupplierModal"', false);
        $supRes->assertSee('data-async-table="suppliers-table"', false);
        $supRes->assertDontSee('lg:col-span-4');

        $supCreateRes = $this->actingAs($user)->get('/master/suppliers/create');
        $supCreateRes->assertStatus(200);
        $supCreateRes->assertSee('Formulir Suplayer Baru');
        $supCreateRes->assertSee('Simpan Data Suplayer');

        // 4. Members Index & Create Page
        $mbrRes = $this->actingAs($user)->get('/master/members');
        $mbrRes->assertStatus(200);
        $mbrRes->assertSee(route('master.members.create'), false);
        $mbrRes->assertDontSee('openAddModal()');
        $mbrRes->assertDontSee('id="addMemberModal"', false);
        $mbrRes->assertSee('data-async-table="members-table"', false);
        $mbrRes->assertDontSee('lg:col-span-4');

        $mbrCreateRes = $this->actingAs($user)->get('/master/members/create');
        $mbrCreateRes->assertStatus(200);
        $mbrCreateRes->assertSee('Formulir Member / Mitra Baru');
        $mbrCreateRes->assertSee('Daftarkan Member / Mitra');

        // 5. Locations Index & Create Page
        $locRes = $this->actingAs($user)->get('/master/locations');
        $locRes->assertStatus(200);
        $locRes->assertSee(route('master.locations.create'), false);
        $locRes->assertDontSee('openAddModal()');
        $locRes->assertDontSee('id="addLocationModal"', false);
        $locRes->assertSee('data-async-table="locations-table"', false);

        $locCreateRes = $this->actingAs($user)->get('/master/locations/create');
        $locCreateRes->assertStatus(200);
        $locCreateRes->assertSee('Formulir Lokasi Operasional Baru');
        $locCreateRes->assertSee('Simpan Lokasi Cabang');
    }
}

