<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Define Permissions
        $permissions = [
            // Dashboard & Reporting
            ['name' => 'dashboard.view', 'label' => 'Melihat Dashboard Dasar', 'module' => 'dashboard'],
            ['name' => 'dashboard.financial', 'label' => 'Melihat Kesehatan Finansial & Omzet', 'module' => 'dashboard'],
            ['name' => 'dashboard.audit', 'label' => 'Melihat Live Stream & Accountability Board', 'module' => 'dashboard'],
            ['name' => 'reports.export', 'label' => 'Mengunduh Laporan Transaksi & Stok', 'module' => 'reports'],

            // Inventory & Master
            ['name' => 'inventory.view', 'label' => 'Melihat Daftar Inventaris & Saldo', 'module' => 'inventory'],
            ['name' => 'inventory.manage', 'label' => 'Mengelola Master Produk, Satuan & Suplayer', 'module' => 'inventory'],
            ['name' => 'inventory.hpp_view', 'label' => 'Melihat Rincian HPP / Nilai Pokok', 'module' => 'inventory'],

            // Purchasing & Emergency
            ['name' => 'purchasing.po', 'label' => 'Membuat & Mengelola PO Suplayer', 'module' => 'purchasing'],
            ['name' => 'purchasing.receive', 'label' => 'Mencatat Penerimaan Fisik Barang PO', 'module' => 'purchasing'],
            ['name' => 'emergency.input', 'label' => 'Input Stok Emergency Toko (+Foto Nota)', 'module' => 'emergency'],

            // Transfer Antar Lokasi
            ['name' => 'transfer.dispatch', 'label' => 'Mengirim / Dispatch Transfer Barang', 'module' => 'transfer'],
            ['name' => 'transfer.receive', 'label' => 'Menerima Kiriman Barang Transfer', 'module' => 'transfer'],

            // POS & Kasir Toko
            ['name' => 'pos.access', 'label' => 'Akses Kasir POS & Checkout Penjualan', 'module' => 'pos'],
            ['name' => 'pos.cash_shift', 'label' => 'Buka & Tutup Sesi Laci Kasir', 'module' => 'pos'],
            ['name' => 'pos.void', 'label' => 'Otorisasi Void / Pembatalan Struk', 'module' => 'pos'],
            ['name' => 'pos.price_adjust', 'label' => 'Penyesuaian Harga Tawar (di atas Floor Price)', 'module' => 'pos'],

            // B2B Kemitraan & Penyerahan
            ['name' => 'b2b.order', 'label' => 'Membuat & Mengelola Pesanan Mitra B2B', 'module' => 'b2b'],
            ['name' => 'b2b.pickup', 'label' => 'Scan QR & Serah Terima Barang B2B', 'module' => 'b2b'],

            // Stok Opname
            ['name' => 'opname.blind_count', 'label' => 'Input Hasil Hitung Buta (Blind Count)', 'module' => 'opname'],
            ['name' => 'opname.reconcile', 'label' => 'Melihat Rekonsiliasi & Input Alasan Selisih', 'module' => 'opname'],
            ['name' => 'opname.finalize', 'label' => 'Mengesahkan & Finalisasi Opname Saldo', 'module' => 'opname'],

            // System Admin
            ['name' => 'system.settings', 'label' => 'Konfigurasi Sistem & Batas Kunci (Lock)', 'module' => 'system'],
            ['name' => 'system.users', 'label' => 'Manajemen Pengguna & Hak Akses', 'module' => 'system'],
        ];

        foreach ($permissions as $p) {
            Permission::firstOrCreate(['name' => $p['name']], $p);
        }

        // 2. Define 8 Roles
        $roles = [
            'super_admin' => [
                'label' => 'Super Admin',
                'description' => 'Administrator sistem penuh dengan akses konfigurasi global dan audit trail.',
                'permissions' => array_column($permissions, 'name'), // All permissions
            ],
            'owner' => [
                'label' => 'Owner & Pengawas',
                'description' => 'Pemilik bisnis read-only untuk memantau performa finansial, aset, dan anomali tanpa beban operasional.',
                'permissions' => [
                    'dashboard.view',
                    'dashboard.financial',
                    'reports.export',
                    'inventory.view',
                    'inventory.hpp_view',
                ],
            ],
            'kepala_toko' => [
                'label' => 'Kepala Toko',
                'description' => 'Penanggung jawab operasional toko, POS, stok emergency, dan pengesahan opname toko.',
                'permissions' => [
                    'dashboard.view',
                    'inventory.view',
                    'inventory.hpp_view',
                    'emergency.input',
                    'transfer.receive',
                    'pos.access',
                    'pos.cash_shift',
                    'pos.void',
                    'pos.price_adjust',
                    'b2b.pickup',
                    'opname.blind_count',
                    'opname.reconcile',
                    'opname.finalize',
                ],
            ],
            'kepala_gudang' => [
                'label' => 'Kepala Gudang',
                'description' => 'Penanggung jawab rantai pasok gudang pusat, PO suplayer, distribusi, dan opname gudang.',
                'permissions' => [
                    'dashboard.view',
                    'inventory.view',
                    'inventory.manage',
                    'inventory.hpp_view',
                    'purchasing.po',
                    'purchasing.receive',
                    'transfer.dispatch',
                    'b2b.pickup',
                    'opname.blind_count',
                    'opname.reconcile',
                    'opname.finalize',
                ],
            ],
            'staf_gudang' => [
                'label' => 'Staf Gudang',
                'description' => 'Operator fisik gudang: penerimaan barang masuk, picking/packing, dan blind count.',
                'permissions' => [
                    'dashboard.view',
                    'inventory.view',
                    'purchasing.receive',
                    'b2b.pickup',
                    'opname.blind_count',
                ],
            ],
            'staf_toko' => [
                'label' => 'Staf Toko / Kasir',
                'description' => 'Operator kasir ritel POS, penerimaan emergency, penyerahan tiket QR, dan blind count toko.',
                'permissions' => [
                    'dashboard.view',
                    'pos.access',
                    'pos.cash_shift',
                    'pos.price_adjust',
                    'emergency.input',
                    'b2b.pickup',
                    'opname.blind_count',
                ],
            ],
            'sales' => [
                'label' => 'Sales & Kemitraan',
                'description' => 'Pemasaran dan pengelola pesanan B2B, inquiry stok, dan pemantauan limit plafon.',
                'permissions' => [
                    'dashboard.view',
                    'inventory.view',
                    'b2b.order',
                ],
            ],
            'b2b_mitra' => [
                'label' => 'Mitra B2B',
                'description' => 'Pelanggan grosir dengan portal mandiri katalog khusus, pesanan, dan tiket pickup QR.',
                'permissions' => [
                    'dashboard.view',
                    'b2b.order',
                ],
            ],
        ];

        foreach ($roles as $roleKey => $data) {
            $role = Role::firstOrCreate(
                ['name' => $roleKey],
                [
                    'label' => $data['label'],
                    'description' => $data['description'],
                    'is_system' => true,
                ]
            );

            $permIds = Permission::whereIn('name', $data['permissions'])->pluck('id');
            $role->permissions()->sync($permIds);
        }
    }
}
