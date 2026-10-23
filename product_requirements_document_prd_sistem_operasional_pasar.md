# Product Requirements Document (PRD)
**Nama Proyek:** Sistem Manajemen Operasional Pasar Terpadu (Gudang, Ritel Toko, & B2B)  
**Versi Dokumen:** 1.0  
**Target Platform:** Web Application (Mobile & Tablet Priority, Desktop Responsive)  
**Bahasa Utama:** Multi-bahasa (Indonesia & Inggris)

---

## 1. Tujuan & Visi Produk (Product Overview)
Membangun sistem informasi manajemen operasional pasar yang menghubungkan rantai pasok **Gudang Pusat**, penjualan ritel cepat di **Toko**, dan penjualan grosir kemitraan **B2B** dalam satu ekosistem *real-time*. 

### Filosofi Utama Sistem:
1. **Mempermudah, Bukan Memperberat Operasional:** Antarmuka dirancang untuk kecepatan eksekusi di lapangan (minim ketikan manual, prioritas layar sentuh *mobile/tablet*, dan pemindaian QR/Barcode).
2. **Tanpa Birokrasi *Approval* (*Zero-Bottleneck Operations*):** Tidak ada alur kerja yang terhenti karena menunggu klik persetujuan (*approval*) dari Owner atau Super Admin. Kendali mutu dan keamanan dijalankan menggunakan **Batas Kunci Otomatis (*System Lock*)** dan **Pertanggungjawaban Langsung Berbasis Izin Akun (*Account-Based Responsibility*)**.
3. **Pelacakan Visual Berbasis Warna (*Color-Coded Movement Tracking*):** Setiap pergerakan stok dan uang memiliki indikator warna mutlak agar anomali atau penyimpangan dapat dikenali dalam hitungan detik oleh Owner tanpa harus membaca tabel angka yang rumit.

---

## 2. Standar Desain UI/UX & Konfigurasi Global
1. **Kepatuhan Desain (`desain.md`):** Seluruh komponen antarmuka, palet warna dasar, tipografi, dan tata letak halaman wajib merujuk pada panduan di dalam berkas `desain.md` serta contoh halaman yang telah disediakan.
2. **Prioritas Perangkat (*Mobile & Tablet Priority*):**
   * **Layar Staf Gudang, Staf Toko, Sales, & B2B:** Mengutamakan tampilan *Mobile/Tablet* dengan tombol sentuh berukuran besar (minimal $48 \times 48\text{ px}$), navigasi bawah (*bottom navigation*), dan integrasi kamera/scanner untuk QR/Barcode.
   * **Layar Kasir (POS):** Optimal untuk layar *Tablet Landscape* maupun *Desktop*, mendukung *shortcut* cepat dan keranjang belanja satu layar penuh.
   * **Layar Super Admin & Owner:** Responsif di *Mobile* (untuk pemantauan cepat) dan *Desktop* (untuk konfigurasi tabel dan unduh laporan).
3. **Dukungan Multi-Bahasa (i18n):**
   * Sistem menyediakan sakelar bahasa instan (**ID** - Bahasa Indonesia & **EN** - Bahasa Inggris) di *header* aplikasi tanpa memuat ulang (*reload*) halaman.
4. **Standar Indikator Warna Pergerakan (*Movement Color Codes*):**
   * 🟢 **Hijau (`#10B981`):** Barang Masuk Normal (Penerimaan PO Suplayer, Hasil Konversi Satuan Masuk, Retur Pelanggan yang Layak Jual).
   * 🔵 **Biru (`#3B82F6`):** Pergerakan/Distribusi Internal (Transfer Stok Gudang $\leftrightarrow$ Toko, Alokasi Barang untuk *Pick-up*).
   * 🟣 **Ungu (`#8B5CF6`):** Penjualan Keluar (Transaksi POS Ritel Toko & Pesanan B2B Selesai).
   * 🟠 **Oranye (`#F59E0B`):** Aktivitas Khusus/Peringatan (Input Stok *Emergency* di Toko, Penyesuaian Harga di Luar Kebiasaan, Piutang B2B Mendekati Limit).
   * 🔴 **Merah (`#EF4444`):** Selisih Negatif & Anomali Kritis (Selisih Kurang Stok Opname karena Rusak/Busuk/Susut/Hilang, Pembatalan Struk/*Void*, Selisih Minus Kasir).

---

## 3. Matriks Hak Akses & Pertanggungjawaban Role (RBAC)

Setiap aksi yang dieksekusi oleh pengguna di bawah ini akan mengikat `user_id`, `role`, dan `timestamp` secara permanen pada catatan *Audit Trail* dan *Log Pergerakan*.

| Role | Lingkup Akses Utama | Akses HPP & Laba | Eksekusi Tanpa Approval |
| :--- | :--- | :--- | :--- |
| **1. Super Admin** | Konfigurasi sistem, *user & permission*, master data global, parameter *system lock*, cetak Kartu Member, & semua laporan. | **Penuh** | Mengubah parameter pengunci sistem & hak akses akun. |
| **2. Owner** | *Read-only* seluruh laporan keuangan, laba-rugi, arus kas, nilai aset stok, log warna pergerakan, & **Laporan Anomali**. | **Penuh (Lihat Saja)** | Pasif (hanya memantau, tidak dibebani tugas *approval*). |
| **3. Kepala Toko** | Operasional toko, POS, validasi tutup kasir, transfer stok, validasi stok *emergency*, *update* harga pasar, & finalisasi Opname Toko. | **Ya (Skup Toko)** | Mengubah *range* harga pasar, mengesahkan Stok Opname Toko, & retur toko. |
| **4. Kepala Gudang** | Operasional gudang, manajemen suplayer & PO, konversi multi-satuan, distribusi ke toko/B2B, & finalisasi Opname Gudang. | **Ya (Harga Beli/PO)** | Membuat PO, mengonversi satuan, & mengesahkan Stok Opname Gudang. |
| **5. Staf Gudang** | *Helper* fisik: hitung barang masuk suplayer, *picking/packing* distribusi, scan QR *pick-up* B2B, & input *blind count* Opname Gudang. | **Tidak** | Mencatat penerimaan fisik & serah terima barang dengan scan QR. |
| **6. Staf Toko** | Kasir POS, scan Kartu Member, buka/tutup laci kasir, terima stok gudang, input stok *emergency* (+foto nota), scan *pick-up* B2B, & *blind count* Opname Toko. | **Tidak** | Transaksi POS (dalam batas *range* & di atas *floor price*) & input stok *emergency* (jika diizinkan). |
| **7. Sales** | Akuisisi mitra B2B, aktivasi Kartu Member B2B, buat pesanan/*request* stok B2B, cek stok *real-time*, & pantau tagihan klien. | **Tidak** | Menginput pesanan B2B (selama plafon kredit klien masih mencukupi). |
| **8. B2B (Mitra)** | Portal mandiri: Kartu Member QR digital, katalog harga khusus, pesan/*request* stok, pilih lokasi *pick-up*, & pantau *invoice*. | **Tidak** | Membuat pesanan mandiri & mengunduh tiket QR *pick-up*. |

---

## 4. Spesifikasi Fungsional & Logika Bisnis Inti

### 4.1. Master Produk, Analisis Kemiripan Nama, & Konversi Satuan
1. **Deteksi Kemiripan Nama Produk (*Fuzzy Matching*):**
   * Saat pengguna mengetik nama produk baru (misal: *"Beras Anak Daro 10Kg"*), sistem menjalankan algoritma kemiripan teks (seperti *Levenshtein Distance* atau *Trigram Similarity*) secara *real-time*.
   * Jika ditemukan kemiripan $\ge 75\%$ dengan produk yang sudah ada (misal: *"Beras Anak Daro 10 Kg"*), sistem menampilkan peringatan visual agar pengguna tidak membuat produk ganda.
2. **Konversi Multi-Satuan (*Multi-UOM*):**
   * Setiap produk memiliki **Satuan Dasar Terkecil** (contoh: $\text{Kg}$ atau $\text{Pcs}$) dan **Satuan Bertingkat** (contoh: $1\text{ Karung} = 50\text{ Kg}$, $1\text{ Karton} = 24\text{ Pcs}$).
   * Saat Gudang membongkar $1\text{ Karung}$ menjadi eceran, stok Karung berkurang $1$ dan stok $\text{Kg}$ bertambah $50$ secara otomatis dengan kode warna **Biru (Internal)** atau **Hijau (Konversi Masuk)**.

### 4.2. Perhitungan HPP Fleksibel & Kunci Harga Pasar (*Range & Floor Price*)
1. **Rumus HPP Fleksibel (*Weighted Moving Average* / Sumber Produk):**
   * Karena harga beli pasar berubah-ubah (baik dari PO Suplayer maupun belanja *Emergency* di Toko), HPP dihitung ulang secara dinamis setiap ada stok masuk baru:
     $$\text{HPP Baru} = \frac{(\text{Stok Lama} \times \text{HPP Lama}) + (\text{Stok Masuk} \times \text{Harga Beli Baru})}{\text{Stok Lama} + \text{Stok Masuk}}$$
   * Sistem juga menyimpan riwayat HPP per *batch*/sumber produk agar Kepala Toko dapat melihat harga modal terakhir secara akurat.
2. **Pengaturan *Range* Harga Pasar & *System Floor Price Lock*:**
   * Setiap produk di Toko memiliki **Harga Bawah Pasar ($\text{Min Price}$)** dan **Harga Atas Pasar ($\text{Max Price}$)** yang diatur oleh Kepala Toko.
   * **Aturan Kunci Otomatis:** Sistem menetapkan **Batas Harga Terendah Mutlak ($\text{Floor Price}$)** dengan rumus:
     $$\text{Floor Price} = \text{HPP Berlaku} + \text{Margin Minimum Sistem (Rp / \%)}$$
   * Kasir diizinkan menyesuaikan harga tawar-menawar di POS dalam rentang $\text{Min Price}$ hingga $\text{Max Price}$. Namun, jika harga yang diinput menyentuh angka $< \text{Floor Price}$, tombol bayar **terkunci otomatis** oleh sistem.

### 4.3. Alur Stok *Emergency* di Toko
1. Ketika barang di Toko habis mendadak dan tidak sempat menunggu kiriman Gudang, akun **Kepala Toko** atau **Staf Toko (yang diberi izin)** dapat membuka menu **Masukan Stok -> Emergency**.
2. Pengguna wajib menginput: Nama Produk, Jumlah, Harga Beli Aktual, Nama Suplayer/Pedagang Pasar, dan **Wajib Mengunggah Foto Nota/Barang**.
3. Begitu disimpan, stok Toko langsung bertambah saat itu juga (tanpa menunggu *approval* Owner) agar kasir bisa langsung berjualan.
4. Sistem memberi label **Warna Oranye (`#F59E0B`)** pada transaksi ini dan langsung menampilkannya di **Laporan Anomali Owner** lengkap dengan nama akun penginput dan foto notanya.

### 4.4. Sistem Kartu Member (QR/Barcode) & Alur B2B
1. **Struktur Kartu Member:**
   * Memiliki ID unik yang direpresentasikan dalam bentuk **QR Code** dan **Barcode 1D** (dapat dicetak pada kartu fisik atau ditampilkan di layar HP mitra).
   * Menyimpan informasi: Tipe Pelanggan (Ritel Reguler / Mitra B2B), *Tier* Harga Khusus (Silver / Gold / VIP), Plafon Kredit Maksimal ($\text{Credit Limit}$), dan Sisa Piutang Berjalan.
2. **Penguncian Piutang Otomatis (*Auto-Block Credit Limit*):**
   * Saat B2B atau Sales membuat pesanan dengan metode bayar Tempo (TOP), sistem memeriksa rumus:
     $$\text{Total Piutang Aktif} + \text{Nilai Pesanan Baru} \le \text{Plafon Kredit Mitra}$$
   * Jika melebihi plafon atau terdapat *invoice* yang sudah lewat tanggal jatuh tempo (*overdue*), sistem otomatis menolak pembayaran tempo dan hanya mengizinkan pembayaran Tunai/Lunas.
3. **Alur *Pick-Up* Fleksibel (Toko atau Gudang):**
   * Saat pesanan B2B dibuat, mitra memilih lokasi pengambilan: **Ambil di Toko** atau **Ambil di Gudang**.
   * Sistem menerbitkan **Tiket QR Pick-Up** dengan status `MENUNGGU DIAMBIL`.
   * Saat mitra datang ke Toko atau Gudang, Staf cukup memindai QR Kartu Member atau Tiket Pick-Up tersebut. Setelah barang diserahkan dan dikonfirmasi di aplikasi staf, stok lokasi terkait langsung terpotong (Warna **Ungu**) dan tiket berubah menjadi `SELESAI DIAMBIL` (mencegah pengambilan ganda di lokasi berbeda).

### 4.5. Alur Stok Opname Mandiri (Toko & Gudang)
1. **Tahap 1 - Inisiasi:** Kepala Toko (untuk area Toko) atau Kepala Gudang (untuk area Gudang) membuat jadwal/sesi Opname per kategori atau seluruh barang.
2. **Tahap 2 - *Blind Count* (Hitung Buta oleh Staf):** Staf Toko/Gudang membuka sesi di perangkat *mobile/tablet*. Aplikasi hanya menampilkan Nama Produk dan Satuan **tanpa menampilkan jumlah stok sistem saat ini**, lalu staf menginput jumlah fisik yang mereka hitung.
3. **Tahap 3 - Rekonsiliasi & Alasan Selisih:** Kepala Toko/Gudang melihat perbandingan `Stok Sistem` vs `Stok Fisik`. Jika ada selisih ($\Delta \neq 0$), Kepala wajib memilih kategori alasan per baris barang: `Susut Alami`, `Rusak / Busuk`, `Hilang`, atau `Koreksi Salah Input`.
4. **Tahap 4 - Finalisasi Langsung:** Saat Kepala menekan tombol **Finalisasi Opname**, stok langsung diperbarui. Seluruh selisih negatif otomatis diberi label **Warna Merah (`#EF4444`)**, dihitung nilai kerugian rupiahnya ($\Delta \times \text{HPP}$), dan masuk ke dasbor **Laporan Anomali Owner** atas nama akun Kepala yang mengesahkan.

---

## 5. Arsitektur Skema Data Utama (Database Schema Blueprint)

Berikut adalah entitas tabel inti yang harus dibangun pada sistem:
1. `users`: `id`, `name`, `username`, `password_hash`, `role` (ENUM: 8 Role), `permissions_json`, `preferred_lang` (`id`/`en`), `location_id` (Gudang/Toko), `is_active`.
2. `members`: `id`, `member_code` (QR/Barcode string), `name`, `type` (`RETAIL`/`B2B`), `tier_level`, `credit_limit`, `current_debt`, `phone`, `address`.
3. `products`: `id`, `sku`, `barcode`, `name`, `normalized_name` (untuk analisis kemiripan), `category_id`, `base_uom`, `current_hpp`, `floor_price_margin`, `min_market_price`, `max_market_price`.
4. `product_uoms`: `id`, `product_id`, `uom_name` (Karung/Karton), `conversion_factor` (jumlah satuan dasar).
5. `inventories`: `id`, `product_id`, `location_type` (`GUDANG`/`TOKO`), `quantity_base_uom`, `min_stock_alert`.
6. `stock_movements`: `id`, `product_id`, `location_type`, `movement_type` (`IN_PO`, `IN_EMERGENCY`, `TRANSFER`, `SALE_POS`, `SALE_B2B`, `OPNAME_ADJ`, `CONVERSION`), `color_code` (`GREEN`, `BLUE`, `PURPLE`, `ORANGE`, `RED`), `qty_change`, `hpp_snapshot`, `reference_id`, `attachment_url` (foto nota emergency/opname), `notes`, `created_by_user_id`, `created_at`.
7. `transactions` (POS & B2B): `id`, `invoice_number`, `transaction_type` (`POS_RETAIL`/`B2B_ORDER`), `member_id`, `sales_user_id`, `cashier_user_id`, `total_amount`, `payment_method` (`CASH`, `QRIS`, `TRANSFER`, `SPLIT`, `TEMPO_TOP`), `payment_status`, `due_date`, `pickup_location` (`TOKO`/`GUDANG`/`DIRECT`), `pickup_qr_token`, `pickup_status`, `handed_over_by_user_id`.
8. `stock_opnames`: `id`, `opname_code`, `location_type` (`GUDANG`/`TOKO`), `status` (`OPEN_BLIND_COUNT`, `RECONCILIATION`, `FINALIZED`), `opened_by_user_id`, `finalized_by_user_id`, `total_loss_value`, `created_at`, `finalized_at`.
9. `stock_opname_items`: `id`, `opname_id`, `product_id`, `system_qty`, `physical_qty`, `diff_qty`, `reason_category`, `counted_by_user_id`, `notes`.
10. `cash_shifts`: `id`, `cashier_user_id`, `opened_at`, `closed_at`, `starting_cash`, `system_expected_cash`, `actual_ending_cash`, `discrepancy_amount`, `validated_by_kep_toko_id`.

---

## 6. Spesifikasi Laporan Owner & Super Admin (Exception-Driven Dashboard)
Halaman utama Owner dirancang agar dapat dibaca dalam waktu kurang dari $1\text{ menit}$ melalui perangkat *mobile* dengan struktur kartu ringkasan berikut:
1. **Kartu Kesehatan Finansial:** Omzet Hari Ini (Toko vs B2B), Estimasi Laba Kotor Hari Ini (Omzet dikurangi HPP), Total Nilai Aset Stok di Gudang & Toko, serta Total Piutang B2B Berjalan.
2. **Feed Pergerakan Berwarna (*Live Color Stream*):** Daftar aktivitas terbaru yang bisa difilter hanya untuk menampilkan warna **Merah (Anomali/Kerugian)** dan **Oranye (Stok Emergency/Peringatan)**.
3. **Tabel Pertanggungjawaban Akun (*Accountability Board*):** Menampilkan ringkasan eksekusi krusial per akun (contoh: *"Hari ini Kep. Toko [Nama] memfinalisasi Opname dengan selisih $-\text{Rp }150.000$"* atau *"Staf Toko [Nama] menginput $3$ transaksi Stok Emergency senilai $\text{Rp }1.200.000$ [Lihat Foto Nota]"*).