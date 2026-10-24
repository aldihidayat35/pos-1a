# Dokumen Status Sistem Saat Ini (CURRENT_STATE.md)
**Sistem Manajemen Operasional Pasar Terpadu (Gudang, Toko, & B2B)**  
**Versi Audit:** Fase 0 Baseline  
**Tanggal:** 24 Oktober 2026  
**Status:** Baseline Terverifikasi — Server Berjalan & Siap Pengembangan Modular  

---

## 1. Inventaris Stack Teknologi

| Komponen | Status di Repositori | Versi Aktual | Catatan Lapangan |
|---|---|---|---|
| **Framework Backend** | Laravel Framework | `12.70.1` | Berjalan pada PHP 8.3.31 (NTS Win64). Modular Monolith siap dibangun. |
| **Database** | MySQL (InnoDB) | `8.x` | Port 3306 pada 127.0.0.1. Database `posagy` telah dibuat dan terhubung. |
| **Driver Sesi & Cache** | Database Driver | Laravel 12 Default | Tabel `sessions`, `cache`, `cache_locks` sudah dimigrasi pada batch 1. |
| **Queue & Worker** | Database Queue | Laravel 12 Default | Tabel `jobs`, `job_batches`, `failed_jobs` sudah dimigrasi pada batch 1. |
| **Frontend Compiler** | Vite + Tailwind CSS | Vite `6.4.5`, Tailwind `4.0.0` | `@tailwindcss/vite` terkonfigurasi. `npm run build` sukses mengompilasi CSS/JS. |
| **Frontend Runtime** | Blade + CDN Tailwind | v3/v4 script CDN | Template `layouts/app.blade.php` masih memuat `https://cdn.tailwindcss.com`. |
| **Tipografi** | Poppins (Google Fonts) | 300..900 weight | Sesuai aturan wajib `desain.md`. Bebas dari font sembarang. |
| **Ikonografi** | Inline SVG Heroicons | 24x24 outline | 100% SVG inline, nol emoji unicode sesuai panduan Anti-AI Slop. |
| **Multi-Bahasa (i18n)** | Client-side JS Engine | ID & EN | Terpasang di `partials/scripts.blade.php`, switch instan tanpa reload. |

---

## 2. Struktur Route & Arsitektur Saat Ini

### 2.1 Route Aktif (`routes/web.php`)
```php
Route::get('/', function () {
    return view('dashboard.index');
})->name('dashboard');
```
- **Kondisi:** Hanya terdapat 1 route publik.
- **Ketiadaan:** Belum ada route autentikasi (`/login`, `/logout`), route modul domain (`/inventory`, `/pos`, `/b2b`, `/opname`), maupun route API internal.

### 2.2 Struktur Kode Backend (`app/`)
- `app/Http/Controllers/Controller.php`: Controller dasar kosong. Belum ada controller domain atau resource.
- `app/Models/User.php`: Model User bawaan Laravel (kolom dasar `name`, `email`, `password`). Belum ada model relasi role, permission, lokasi, produk, stok, atau transaksi.
- `app/Providers/AppServiceProvider.php`: Provider standar.
- **Kesimpulan:** Arsitektur modular monolith belum diimplementasikan di layer PHP. Seluruh interaksi saat ini berjalan di layer Blade dan script client-side.

---

## 3. Kondisi Tampilan & Navigasi Existing

1. **Shell Navigasi:**
   - **Desktop:** Sidebar kiri ramping (`w-[92px]`) warna `#342A6E` dengan indikator kurva cair (*liquid curve notch*) putih yang meluncur mulus mengikuti tombol menu aktif.
   - **Mobile / Tablet:** Bar navigasi bawah (*mobile bottom bar*) dengan 6 tombol sentuh besar ($>48\times 48\text{ px}$).
   - **Header:** Bersih tanpa border kaku, memuat selector sub-menu dinamis (pill desktop & dropdown mobile), sakelar bahasa instan ID/EN, badge legenda 5 warna, lonceng notifikasi, dan avatar profil pengguna.
   - **Panel Kanan:** Panel modular untuk kartu toggle fungsi otomasi gudang dan visualisasi grafik batang throughput mingguan.

2. **Pembagian Halaman Tab (`resources/views/dashboard/pages/`):**
   - `page-0.blade.php`: Dasbor Utama & Kesehatan Finansial (Omzet, Laba Kotor, Nilai Aset Stok, Piutang B2B, Legenda Warna, dan Feed Pergerakan Real-Time).
   - `page-1.blade.php`: Operasional Gudang Pusat (Kalender jadwal, kapasitas rak cold storage & dry pallet).
   - `page-2.blade.php`: Direktori Inventaris & Rak (Pencarian SKU, kategori zona rak A-D, tabel dummy inventaris).
   - `page-3.blade.php`: Kalibrasi Parameter IoT (Slider suhu cold storage, kecepatan AGV, reorder limit).
   - `page-4.blade.php`: Feed Pergerakan 5-Warna & Laporan Anomali (Zero-bottleneck board, selisih minus, audit trail).
   - `page-5.blade.php`: Persetujuan Surat Jalan & Dokumen (Daftar manifest DO/PO dan konfirmasi retur).

---

## 4. Kelemahan, Resiko, & Technical Debt Existing

1. **Dummy / Statis Total:**
   - Seluruh data metrik, angka finansial, nama barang, dan riwayat pergerakan adalah hardcoded di Blade/JavaScript.
   - Tidak ada sinkronisasi ke database MySQL sama sekali. Operasi simpan hanya memicu toast notification lokal.
2. **Ketergantungan CDN Eksternal:**
   - `layouts/app.blade.php` masih memanggil `https://cdn.tailwindcss.com`. Jika jaringan offline atau lambat, layout berisiko unstyled. Proyek sudah memiliki Vite dan `@tailwindcss/vite`, sehingga perlu dimigrasikan ke bundle lokal pada Fase 1.
3. **Konten Campuran Gudang-Only vs Pasar Terpadu:**
   - Sebagian halaman (Page 1 dan Page 3) masih memuat artefak mockup lama berupa "Robot AGV" dan "Sensor IoT Industri", yang perlu diselaraskan dengan kebutuhan nyata Pasar Terpadu (multi-toko, penerimaan barang pasar, kasir POS, dan kemitraan B2B).
4. **Ketiadaan Server-Side RBAC & Location Scope:**
   - Tidak ada proteksi akses di server. Siapa pun yang mengakses URL dapat melihat seluruh halaman tanpa otentikasi.

---

## 5. Kesiapan Menuju Fase 1

- [x] Lingkungan PHP 8.3 & Laravel 12 terverifikasi.
- [x] Database MySQL `posagy` terhubung dan tabel dasar session/cache telah siap.
- [x] Vite build berhasil tanpa error sintaks.
- [x] Template visual, layout shell, token warna, dan standar desain telah dipetakan secara komprehensif.
- [ ] Fase 1 siap dimulai untuk membangun mekanisme autentikasi nyata, RBAC relasional, location scope, dan shell dashboard berbasis peran pengguna.
