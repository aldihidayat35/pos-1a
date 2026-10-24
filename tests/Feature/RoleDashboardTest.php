<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class RoleDashboardTest extends TestCase
{
    public function test_superadmin_sees_executive_dashboard_with_financials(): void
    {
        $superadmin = User::where('username', 'superadmin')->firstOrFail();

        $response = $this->actingAs($superadmin)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Ringkasan Eksekutif • Pasar Terpadu Real-Time');
        $response->assertSee('Kesehatan Finansial & Aset Pasar', false);
        $response->assertSee('Omzet Realisasi Hari Ini');
        $response->assertSee('Laba Kotor Real-Time');
        $response->assertSee('Nilai Aset Persediaan');
        $response->assertSee('Piutang B2B Berjalan');
        $response->assertSee('Feed Pergerakan 5-Warna Real-Time');
    }

    public function test_owner_sees_executive_dashboard_read_only(): void
    {
        $owner = User::where('username', 'owner')->firstOrFail();

        $response = $this->actingAs($owner)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Owner (Read-Only)');
        $response->assertSee('Kesehatan Finansial & Aset Pasar', false);
        $response->assertSee('Laba Kotor Real-Time');
        $response->assertDontSee('+ Belanja Emergency');
        $response->assertDontSee('Buka Terminal Kasir POS');
    }

    public function test_store_staff_and_head_see_store_operational_dashboard(): void
    {
        $staftoko = User::where('username', 'staftoko')->firstOrFail();

        $response = $this->actingAs($staftoko)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Buka Terminal Kasir POS');
        $response->assertSee('Status Shift Laci');
        $response->assertSee('Penjualan Kasir Hari Ini');
        $response->assertSee('Belanja Emergency Bulan Ini');
        $response->assertSee('Transfer Masuk (Transit)');
        // Cashier must NOT see executive gross profit margin or executive banners
        $response->assertDontSee('Ringkasan Eksekutif • Pasar Terpadu Real-Time');
        $response->assertDontSee('Laba Kotor Real-Time');
        $response->assertDontSee('Nilai Aset Persediaan');
        $response->assertDontSee('Piutang B2B Berjalan');
    }

    public function test_warehouse_staff_and_head_see_warehouse_logistics_dashboard(): void
    {
        $kepalagudang = User::where('username', 'kepalagudang')->firstOrFail();

        $response = $this->actingAs($kepalagudang)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Rantai Pasok &amp; Logistik Sentral', false);
        $response->assertSee('PO Menunggu Terima');
        $response->assertSee('Transfer Keluar (Draft)');
        $response->assertSee('Stok Menipis');
        $response->assertSee('Sesi Stok Opname');
        $response->assertSee('Pergerakan Stok Gudang Terkini');
        $response->assertDontSee('Laba Kotor Real-Time');
    }

    public function test_sales_sees_b2b_wholesale_dashboard(): void
    {
        $sales = User::where('username', 'sales')->firstOrFail();

        $response = $this->actingAs($sales)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Portal Grosir &amp; Kemitraan B2B', false);
        $response->assertSee('Pesanan Grosir Aktif');
        $response->assertSee('Tiket QR Siap Ambil');
        $response->assertSee('Mitra Grosir Terdaftar');
        $response->assertSee('Status Overdue Piutang');
        $response->assertSee('Pesanan Grosir B2B Terkini');
        $response->assertDontSee('Laba Kotor Real-Time');
    }
}
