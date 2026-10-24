<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class RoleDashboardTest extends TestCase
{
    public function test_superadmin_sees_executive_dashboard_with_financials_and_charts(): void
    {
        $superadmin = User::where('username', 'superadmin')->firstOrFail();

        $response = $this->actingAs($superadmin)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Halo, Super Administrator!');
        $response->assertDontSee('Ringkasan Eksekutif • Pasar Terpadu Real-Time');
        $response->assertSee('Kesehatan Finansial & Aset Pasar', false);
        $response->assertSee('Omzet Realisasi Hari Ini');
        $response->assertSee('Laba Kotor Real-Time');
        $response->assertSee('Nilai Aset Persediaan');
        $response->assertSee('Piutang B2B Berjalan');
        $response->assertSee('executiveFinancialChart');
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

    public function test_store_head_sees_store_head_dashboard_with_sales_charts(): void
    {
        $kepalatoko = User::where('username', 'kepalatoko')->firstOrFail();

        $response = $this->actingAs($kepalatoko)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Kepala Toko • Unit Kerja:');
        $response->assertDontSee('Manajemen Toko Ritel • Operasional Pasar Aktif');
        $response->assertSee('Penjualan Kasir Hari Ini');
        $response->assertSee('Kasir Aktif (Shift Laci)');
        $response->assertSee('Belanja Emergency Bulan Ini');
        $response->assertSee('Transfer Masuk (Transit)');
        $response->assertSee('storeSalesChart');
        // Head must not see executive corporate profit margin
        $response->assertDontSee('Laba Kotor Real-Time');
    }

    public function test_store_staff_sees_personal_cashier_performance_and_shift_attendance(): void
    {
        $staftoko = User::where('username', 'staftoko')->firstOrFail();

        $response = $this->actingAs($staftoko)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Operator Kasir POS • Toko:');
        $response->assertDontSee('Status Kasir: Shift Laci');
        $response->assertSee('Buka Terminal Kasir POS (F2)');
        $response->assertSee('Kehadiran (Shift Buka)');
        $response->assertSee('Total Transaksi Selesai');
        $response->assertSee('Akumulasi Omzet Kasir');
        $response->assertSee('Rata-Rata Struk (Basket)');
        $response->assertSee('Target Penjualan Ritel Kasir');
        // Cashier must NOT see executive gross profit margin or executive banners
        $response->assertDontSee('Laba Kotor Real-Time');
        $response->assertDontSee('Nilai Aset Persediaan');
        $response->assertDontSee('Piutang B2B Berjalan');
    }

    public function test_warehouse_head_sees_logistics_analytics_and_throughput_charts(): void
    {
        $kepalagudang = User::where('username', 'kepalagudang')->firstOrFail();

        $response = $this->actingAs($kepalagudang)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Kepala Gudang • Fasilitas Pergudangan:');
        $response->assertDontSee('Rantai Pasok & Logistik Sentral • Real-Time');
        $response->assertSee('PO Menunggu Terima');
        $response->assertSee('Transfer Keluar (Draft)');
        $response->assertSee('Stok Menipis');
        $response->assertSee('Sesi Stok Opname');
        $response->assertSee('warehouseThroughputChart');
        $response->assertDontSee('Laba Kotor Real-Time');
    }

    public function test_warehouse_staff_sees_operator_task_performance(): void
    {
        $stafgudang = User::where('username', 'stafgudang')->firstOrFail();

        $response = $this->actingAs($stafgudang)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Staf Gudang • Lokasi Kerja:');
        $response->assertDontSee('Operator Logistik Fisik • Sentral Pergudangan');
        $response->assertSee('Penerimaan PO Diterima');
        $response->assertSee('Transfer Disiapkan (Picking)');
        $response->assertSee('Hitung Blind Count');
        $response->assertSee('Target Aktivitas Operasional Bulan Ini');
        $response->assertDontSee('Laba Kotor Real-Time');
    }

    public function test_sales_sees_b2b_sales_performance_and_target_progress(): void
    {
        $sales = User::where('username', 'sales')->firstOrFail();

        $response = $this->actingAs($sales)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Sales Executive • Cakupan Pasar:');
        $response->assertSee('Realisasi Omzet B2B');
        $response->assertSee('Pesanan Dibuat');
        $response->assertSee('Mitra Grosir B2B');
        $response->assertSee('Target Omzet Penjualan B2B Anda');
        $response->assertDontSee('Laba Kotor Real-Time');
    }

    public function test_b2b_partner_sees_customer_portal_qr_tickets_and_credit_limit(): void
    {
        $mitrab2b = User::where('username', 'mitrab2b')->firstOrFail();

        $response = $this->actingAs($mitrab2b)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Kode Mitra:');
        $response->assertDontSee('Portal Kemitraan Grosir Resmi PasarHub');
        $response->assertSee('Sisa Plafon Kredit');
        $response->assertSee('Tiket QR Siap Ambil');
        $response->assertSee('Tagihan Tempo Berjalan');
        $response->assertSee('Total Pembelian Bulan Ini');
        $response->assertDontSee('Laba Kotor Real-Time');
    }
}
