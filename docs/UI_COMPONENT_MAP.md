# Peta Komponen & Standar UI/UX (UI_COMPONENT_MAP.md)
**Sistem Manajemen Operasional Pasar Terpadu (Gudang, Toko, & B2B)**  
**Versi:** 1.0 (Baseline Audit)  
**Acuan Desain:** `desain.md` dan Komponen Existing Repositori  

---

## 1. Arsitektur Layout & Struktur Shell

Aplikasi menggunakan layout tunggal yang kokoh di `resources/views/layouts/app.blade.php` dengan tata letak tinggi penuh layar (`h-screen overflow-hidden`) yang membagi antarmuka menjadi 3 area utama:

```text
┌────────────────────────────────────────────────────────────────────────────────────────┐
│ [Sidebar / Mobile Nav]                                                                 │
│  ├─ Desktop: Fixed Left Aside (w-[92px], rounded-[26px], #342A6E)                      │
│  └─ Mobile: Fixed Bottom Nav Bar (h-[62px], rounded-t-[24px], z-40)                    │
│                                                                                        │
│ [Main Viewport Area] (flex-1 flex flex-col h-full overflow-hidden)                     │
│  ├─ [Fixed Header] (partials/header.blade.php)                                         │
│  │   ├─ Left: Mobile Hamburger / Desktop Sub-Menu Pills                               │
│  │   └─ Right: i18n Switcher (ID/EN) + Color Legend + Settings + Bell + Avatar         │
│  │                                                                                     │
│  └─ [Scrollable Workspace] (id="scrollableWorkspace", overflow-y-auto)                 │
│      ├─ [Left/Center Workspace] (@yield('content') / dashboard/pages/page-0..5)        │
│      └─ [Right Side Panel] (partials/right-panel.blade.php, lg:w-[370px])              │
│                                                                                        │
│ [Floating Elements]                                                                    │
│  └─ Toast Notification (partials/toast.blade.php)                                      │
└────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 2. Inventaris Blade Partials & Komponen

| File Komponen | Tipe | Peran & Tanggung Jawab | Status Reusability |
|---|---|---|---|
| `layouts/app.blade.php` | Layout Master | Kerangka dasar HTML5, deklarasi token Tailwind, font Poppins, animasi global, dan penampung stack styles/scripts. | Reusable di seluruh modul |
| `partials/sidebar.blade.php` | Partial Layout | Navigasi vertikal desktop dengan liquid curve notch sliding, logo PasarHub, dan 6 tombol navigasi modul. | Reusable (Dinamis per role) |
| `partials/mobile-nav.blade.php` | Partial Layout | Navigasi horizontal bawah layar untuk mobile/tablet dengan indikator kapsul aktif. | Reusable (Dinamis per role) |
| `partials/header.blade.php` | Partial Layout | Header atas aplikasi, sub-menu dinamis per konteks modul, pengalih bahasa ID/EN, quick action, dan profil operator. | Reusable |
| `partials/right-panel.blade.php` | Partial Layout | Panel sisi kanan berisi kartu toggle otomasi (Optimasi Rak, Smart Scan, Sensor Suhu, Sinkron ERP) dan chart throughput. | Reusable (Dapat disesuaikan) |
| `partials/toast.blade.php` | Partial Komponen | Kotak pesan melayang (*floating toast*) untuk feedback instan tindakan pengguna dengan animasi slide & fade. | Reusable |
| `partials/scripts.blade.php` | Script Partial | Engine client-side: kamus i18n dwibahasa, switchPage tabs, mobile drawer toggler, filtering status 5-warna. | Reusable (Bakal dipisah per modul) |
| `components/movement-badge.blade.php` | Blade Component | Komponen badge resmi pergerakan 5-warna PRD (Hijau, Biru, Ungu, Oranye, Merah) dengan ikon SVG dan label dwibahasa. | Siap pakai di semua tabel & card |

---

## 3. Matriks Token Warna & Semantik

Mengacu ketat pada `desain.md` dan konfigurasi `layouts/app.blade.php`:

### 3.1 Palet Inti Antarmuka
| Token CSS | Hex Code | Penggunaan Wajib | Larangan Keras |
|---|---|---|---|
| `sidebar` | `#342A6E` | Latar sidebar desktop, mobile bottom nav, teks utama kontras | Dilarang diganti ungu neon atau biru terang |
| `sidebarDark` | `#292059` | Hover state tombol navigasi sidebar | Dilarang menggunakan shadow glow |
| `brandOrange` | `#F29F3D` | Hero banner utama, tombol sorotan aksi, badge status | Dilarang memakai gradasi pelangi |
| `brandCoral` | `#E86B74` | Indikator peringatan/kritis, tombol aksi sekunder, ring kapasitas | Dilarang efek neon |
| `brandCyan` | `#7AC7D9` | Indikator sensor, tag pendukung, aksen dingin | Dilarang efek glow |
| `brandLavender`| `#E9E8F5` | Background sub-menu hover, kartu non-aktif | - |
| `panelBg` | `#F6F6FA` | Latar belakang panel samping, card container, sub-menu pill | Dilarang diganti warna abu-abu kotor |
| `textMain` | `#382E6B` | Teks utama, judul, angka transaksi | Dilarang memakai hitam pekat `#000000` |
| `textMuted` | `#9490AD` | Teks sekunder, keterangan tanggal, placeholder form | - |
| `borderSubtle`| `#ECEBF4` | Border batas kartu, modul, dan kontainer tabel | - |
| `borderDarker`| `#E6E4F0` | Border tombol, input form, dan dropdown | - |
| `white` | `#FFFFFF` | Kanvas dasar, latar kartu aktif, notch kurva cair | - |

### 3.2 Indikator Pergerakan 5-Warna PRD (Movement Color Codes)
| Status | Token Hex | Token Background | Token Teks | Makna Operasional PRD |
|---|---|---|---|---|
| 🟢 **Hijau** | `#10B981` | `#ECFDF5` | `#065F46` | **Masuk Normal:** Penerimaan PO Suplayer, Konversi Satuan Masuk, Retur Layak Jual. |
| 🔵 **Biru** | `#3B82F6` | `#EFF6FF` | `#1E40AF` | **Internal:** Transfer Stok Gudang $\leftrightarrow$ Toko, Alokasi Pick-up Tiket Mandiri. |
| 🟣 **Ungu** | `#8B5CF6` | `#F5F3FF` | `#5B21B6` | **Penjualan Keluar:** Transaksi Kasir POS Ritel & Pesanan Kemitraan B2B Selesai. |
| 🟠 **Oranye** | `#F59E0B` | `#FFFBEB` | `#92400E` | **Khusus/Peringatan:** Input Stok Emergency di Toko (+Foto Nota), Limit Kredit Menipis. |
| 🔴 **Merah** | `#EF4444` | `#FEF2F2` | `#991B1B` | **Anomali/Kritis:** Selisih Negatif Opname (Susut/Rusak/Hilang), Void Struk, Minus Kasir. |

---

## 4. Standar Tipografi & Skala Teks (Poppins)

Seluruh elemen teks wajib memakai font **Poppins**:

| Peran Tipografi | Kelas Tailwind | Ukuran Pixel | Ketebalan (*Weight*) |
|---|---|---|---|
| **Page / Hero Heading** | `text-2xl sm:text-[32px]` | 24px – 32px | `font-bold` (700) |
| **Section Title** | `text-lg` | 18px | `font-bold` (700) |
| **Card / Item Title** | `text-sm` / `text-base` | 14px – 16px | `font-bold` (700) |
| **Metrik Angka Besar** | `text-xl` / `text-2xl` / `text-3xl` | 20px – 30px | `font-extrabold` (800) |
| **Standard Body / Table** | `text-xs` / `text-sm` | 12px – 14px | `font-medium` (500) |
| **Label / Sub-Menu Pill** | `text-[11px]` / `text-xs` | 11px – 12px | `font-semibold` (600) |
| **Micro Badge / Tag** | `text-[10px]` | 10px | `font-bold` (700) |

---

## 5. Katalog Interaksi & Motion Design

1. **Liquid Curve Sidebar Notch:**
   - Transisi pergeseran vertikal: `transform 0.45s cubic-bezier(0.34, 1.42, 0.64, 1)`.
   - Menggunakan kurva cekung atas (`::before`) dan bawah (`::after`) dengan box-shadow `10px 10px 0 10px #FFFFFF` sehingga menyatu mulus tanpa garis batas ke kanvas putih.
2. **Interactive Cards (Hover Lift):**
   - Transisi: `transform hover:-translate-y-0.5 transition duration-200 cursor-pointer`.
3. **Button Tap Feedback:**
   - Tombol operasional sentuh: `active:scale-95 transition min-h-[48px]`.
4. **Page Transition:**
   - `animation: pageSlideFade 0.3s ease-out forwards` (fade-in dengan kenaikan 6px dari bawah).
5. **Scrollbar Elimination:**
   - `scrollbar-width: none; ::-webkit-scrollbar { display: none; }` untuk kebersihan visual dashboard.

---

## 6. Rencana Adaptasi Komponen untuk Fase-Fase Berikutnya

| Modul Target | Halaman Existing | Komponen yang Akan Digunakan Kembali / Disesuaikan |
|---|---|---|
| **Fase 1 (Auth & Shell)** | `layouts/app.blade.php`, `partials/header.blade.php` | Form login baru dengan gaya panel `rounded-[26px]`, selektor outlet/lokasi di header, badge role dinamis. |
| **Fase 2 (Master Data)** | `dashboard/pages/page-2.blade.php` & `page-5.blade.php` | Tabel katalog produk dengan filter kategori, modal tambah produk dengan cek kemiripan teks, selector multi-UOM. |
| **Fase 3 (Inventory & PO)** | `dashboard/pages/page-1.blade.php` | Alur goods receipt gudang, tabel ledger saldo mutasi dengan `components/movement-badge`. |
| **Fase 4 (Transfer & Emergency)** | `dashboard/pages/page-4.blade.php` | Modal input emergency stock + upload foto nota belanja pasar, visualisasi barang transit. |
| **Fase 5 (POS Kasir)** | `dashboard/pages/page-2.blade.php` | Antarmuka kasir touch-friendly, shortcut angka, keranjang belanja responsif, modal pembayaran. |
| **Fase 6 (B2B & QR)** | `dashboard/pages/page-3.blade.php` | Modal scan QR member, kartu member virtual dengan barcode SVG, indikator sisa plafon kredit. |
| **Fase 7 (Opname)** | `dashboard/pages/page-4.blade.php` | Layar blind-count staf (hanya nama barang), layar rekonsiliasi kepala toko dengan selisih rupiah. |
| **Fase 8 (Owner Board)** | `dashboard/pages/page-0.blade.php` | Dashboard finansial read-only, stream anomali berbasis warna merah/oranye, board pertanggungjawaban akun. |
