# Checklist Go-Live & Panduan UAT — PasarHub Sistem Operasional Pasar Terpadu

Dokumen ini merupakan panduan kesiapan peluncuran (*Production Readiness & Go-Live Checklist*) serta ringkasan verifikasi UAT (*User Acceptance Testing*) sistem operasional PasarHub sesuai **PRD v2.0** dan **Blueprint Arsitektur Laravel 12 + MySQL InnoDB**.

---

## 1. Matriks Acceptance Test Bisnis (AT-01 s/d AT-16)

Seluruh 16 skenario uji kritis dari PRD Bagian 10 telah diimplementasikan dan diverifikasi dengan automated test suite (*92 passed, 0 failed, 428 assertions*):

| ID | Skenario Bisnis | Hasil yang Diwajibkan PRD | Status Implementasi & Verifikasi |
|---|---|---|---|
| **AT-01** | User tanpa hak mengakses endpoint HPP / harga pokok | HTTP 403; data HPP dan margin tidak bocor ke staf kasir / publik | **Lolos & Terverifikasi** (`InventoryAndPurchaseOrderTest`, `OwnerReportingAndReadOnlyTest`) |
| **AT-02** | 2 kasir membeli sisa stok terakhir secara bersamaan | Hanya transaksi dengan stok mencukupi yang berhasil; stok fisik tidak pernah minus | **Lolos & Terverifikasi** (`StabilizationAndUatTest::test_at02_pos_checkout_strictly_blocks_negative_stock`) |
| **AT-03** | Checkout dikirim ulang karena timeout jaringan / *double click* | Struk sama diterbitkan kembali secara idempoten; tidak ada pemotongan stok kedua | **Lolos & Terverifikasi** (`StabilizationAndUatTest::test_at03_checkout_idempotency_prevents_duplicate_sales_and_inventory_decrement`) |
| **AT-04** | Barang diterima PO secara bertahap (parsial) | Saldo bertambah sesuai kuantitas riil diterima; moving average HPP terhitung otomatis | **Lolos & Terverifikasi** (`InventoryAndPurchaseOrderTest::test_partial_goods_receipt_updates_inventory_and_moving_average_hpp`) |
| **AT-05** | Transfer dikirim tapi belum diterima di toko | Barang berada pada lokasi virtual Transit; tidak hilang dan tidak terhitung ganda | **Lolos & Terverifikasi** (`TransferEmergencyPricingTest::test_transfer_dispatch_cuts_origin_and_increments_transit_stock_no_loss_or_double_count`) |
| **AT-06** | Pembelian emergency di toko tanpa upload foto nota | Transaksi ditolak server; saldo persediaan tidak bertambah | **Lolos & Terverifikasi** (`TransferEmergencyPricingTest::test_emergency_purchase_requires_mandatory_photo_proof`) |
| **AT-07** | Harga checkout dimanipulasi di bawah batas bawah (*floor price*) | Ditolak server HTTP 422 dengan pesan Floor Lock, meski UI dimanipulasi | **Lolos & Terverifikasi** (`PosAndCashShiftTest::test_checkout_fails_if_price_below_floor_price`) |
| **AT-08** | Kredit B2B melebihi limit atau terdapat faktur overdue | Pembayaran tempo otomatis diblokir; opsi tunai/transfer tetap tersedia | **Lolos & Terverifikasi** (`B2bCreditAndFulfillmentTest::test_b2b_credit_check_blocks_order_when_exposure_exceeds_limit`) |
| **AT-09** | Dua scan serah terima QR pada tiket yang sama | Hanya 1 serah terima yang berhasil; pemotongan stok reservasi hanya sekali | **Lolos & Terverifikasi** (`StabilizationAndUatTest::test_at09_one_time_qr_token_pickup_cannot_be_redeemed_twice`) |
| **AT-10** | Opname gagal / dibatalkan sebelum commit | Transaksi dibatalkan secara atomik; saldo dan kartu stok tidak setengah final | **Lolos & Terverifikasi** (`StockOpnameAndAuditTest::test_opname_cancellation_preserves_inventory_balance`) |
| **AT-11** | Staf lapangan menjalankan blind count opname | Payload respons server tidak membocorkan `system_qty`, bukan sekadar sembunyi CSS | **Lolos & Terverifikasi** (`StockOpnameAndAuditTest::test_staff_blind_count_screen_does_not_reveal_system_quantity`) |
| **AT-12** | Staf toko membuka data toko lain via manipulasi URL | Ditolak policy dan location scope middleware (HTTP 403) | **Lolos & Terverifikasi** (`LocationScopeTest::test_user_cannot_switch_to_unauthorized_location`) |
| **AT-13** | POS void / pembatalan struk oleh Kepala Toko | Membentuk jurnal pembalik stok, mencatat alasan, dan memicu anomali merah | **Lolos & Terverifikasi** (`PosAndCashShiftTest::test_authorized_user_can_void_sale_with_reversal_and_red_movement`) |
| **AT-14** | Owner membuka dasbor eksekutif | Memiliki visibilitas penuh omzet, laba kotor, aset, dan piutang; dilarang mutasi (read-only) | **Lolos & Terverifikasi** (`OwnerReportingAndReadOnlyTest::test_strict_owner_read_only_guard_rejects_all_mutative_actions`) |
| **AT-15** | Penggantian bahasa antarmuka (ID / EN) | Label sistem berpindah bahasa seketika tanpa merusak layout atau relasi data | **Lolos & Terverifikasi** (`StabilizationAndUatTest::test_at15_bilingual_language_switcher_persists_and_translates_labels`) |
| **AT-16** | HPP produk berubah akibat penerimaan barang baru | Snapshot HPP pada struk penjualan lama tetap abadi (*immutable snapshot*) | **Lolos & Terverifikasi** (`StabilizationAndUatTest::test_at16_historical_sale_cogs_snapshot_remains_immutable_when_hpp_changes`) |

---

## 2. Checklist Kesiapan Produksi (Production Readiness)

### A. Konfigurasi Lingkungan Server & Basis Data
- [x] **PHP Runtime:** PHP 8.2 atau 8.3 dengan ekstensi wajib: `pdo_mysql`, `mbstring`, `openssl`, `bcmath`, `curl`, `ctype`, `fileinfo`, `xml`, `tokenizer`.
- [x] **Database Engine:** MySQL 8.0+ / 8.4 LTS dengan storage engine **InnoDB** murni di seluruh tabel.
- [x] **Collation & Encoding:** `utf8mb4_unicode_ci` untuk seluruh tabel guna mendukung teks internasional dan simbol.
- [x] **Timezone:** `Asia/Jakarta` (WIB) pada `config/app.php` dan database timestamp UTC konsisten.
- [x] **Konfigurasi Produksi (.env):**
  - `APP_ENV=production`
  - `APP_DEBUG=false`
  - `APP_KEY` digenerate dengan kuat (`php artisan key:generate`).
  - `SESSION_SECURE_COOKIE=true` pada environment HTTPS.
  - `SESSION_LIFETIME=120` menit.

### B. Otomasi Pencadangan Basis Data (Backup & Recovery)
- [x] **Perintah Pencadangan Mandiri:**
  ```bash
  php artisan app:backup
  ```
  Menghasilkan snapshot SQL lengkap dari seluruh 49 tabel dengan transaksi aman (`START TRANSACTION ... COMMIT`) dan `FOREIGN_KEY_CHECKS=0`. Berkas tersimpan di `storage/app/backups/backup_pasarhub_YYYYMMDD_HHMMSS.sql`.
- [x] **Perintah Pemulihan (Restorasi):**
  ```bash
  php artisan app:backup --restore=storage/app/backups/backup_pasarhub_YYYYMMDD_HHMMSS.sql
  ```
- [x] **Jadwal Cron Produksi:**
  Tambahkan cron job harian pada crontab server (pukul 02:00 WIB):
  ```cron
  0 2 * * * cd /path/to/project && php artisan app:backup >> /var/log/pasarhub_backup.log 2>&1
  ```

### C. Integrasi Perangkat Keras Lapangan (Hardware & Scanner)
- [x] **Barcode Scanner USB / Wireless:**
  Input scanner di terminal POS didukung langsung pada kolom pencarian (`#posSearchInput`) dengan pemindaian barcode langsung menambahkan barang ke keranjang (*instant add-to-cart*).
- [x] **Tombol Pintas Keyboard POS:**
  - `F2`: Fokus cepat ke kolom pemindaian barcode / SKU.
  - `F9`: Buka modal pembayaran dan cetak struk.
  - `Escape`: Tutup modal pembayaran / bersihkan pencarian.
- [x] **Thermal Receipt Printer (58mm / 80mm):**
  Halaman struk thermal POS (`/store/pos/receipt/{sale}`) dirancang khusus dengan styling CSS `@media print` monokrom tanpa margin agar pas pada printer thermal Epson / kasir standard.

---

## 3. Akun Pengguna Role Baku untuk Pengujian Lapangan

Semua akun terdaftar secara aman pada database dev dengan password standar `password`:

| Username | Role | Lokasi Akses Default | Deskripsi Kewenangan |
|---|---|---|---|
| `superadmin` | Super Admin | Global (Semua Lokasi) | Akses konfigurasi penuh, audit trail, dan master data |
| `owner` | Owner | Global (Semua Lokasi) | Dasbor finansial eksekutif, omzet, HPP, aset, penuaan piutang, read-only |
| `kepalagudang` | Kepala Gudang | WH-CENTRAL | Pembuatan PO, penerimaan barang, kirim transfer, finalisasi opname gudang |
| `stafgudang` | Staf Gudang | WH-CENTRAL | Pemeriksaan fisik, blind count opname, serah terima pickup B2B |
| `kepalatoko` | Kepala Toko | STR-PASAR-1 | Persetujuan emergency, validasi kasir, penutupan shift, void struk, opname toko |
| `staftoko` | Staf Toko / Kasir | STR-PASAR-1 | Terminal kasir POS, scan barcode, cetak struk, penerimaan transfer masuk |
| `salesb2b` | Sales B2B | Global / Gudang | Pendaftaran mitra, pembuatan pesanan grosir, penerbitan tiket pickup |
| `mitrab2b` | Member B2B | - | Portal pesanan mitra, cek pagu kredit, tiket QR pengambilan mandiri |

---

## 4. Panduan Eksekusi Tes Menyeluruh

Jalankan seluruh rangkaian automated tests sebelum deployment:
```bash
# Menjalankan 92 test suite
php artisan test

# Menjalankan tes stabilitas dan UAT saja
php artisan test tests/Feature/StabilizationAndUatTest.php

# Menjalankan pencadangan basis data
php artisan app:backup
```
