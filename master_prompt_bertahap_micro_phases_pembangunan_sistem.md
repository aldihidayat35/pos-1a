# Panduan Master Prompt Bertahap (Micro-Phases)
**Sistem Operasional Pasar (Gudang, Toko Ritel, & B2B)**

Dokumen ini berisi kumpulan *prompt* yang telah dipecah menjadi **10 Fase Kecil (Micro-Phases)**. 

### Cara Penggunaan:
1. Pastikan file `desain.md`, contoh halaman referensi Anda, dan file `prd_sistem_operasional_pasar.md` sudah berada di dalam direktori kerja atau dilampirkan pada konteks AI *coding assistant* Anda.
2. Salin (*copy*) dan jalankan *prompt* **satu per satu secara berurutan** (mulai dari Fase 1 hingga Fase 10).
3. Jangan lanjut ke fase berikutnya sebelum fase yang sedang berjalan selesai diuji dan tampilannya sesuai dengan `desain.md`.

---

## FASE 1: Pondasi Proyek, Desain Sistem (`desain.md`), & Multi-Bahasa (ID/EN)
**Tujuan:** Membangun kerangka dasar aplikasi, sistem navigasi responsif (*Mobile/Tablet Priority*), tema visual sesuai `desain.md`, dan sakelar bahasa Indonesia/Inggris.

> **Salin Prompt Fase 1:**
> ```text
> Kita akan membangun aplikasi "Sistem Operasional Pasar Terpadu (Gudang, Toko, & B2B)" secara bertahap berdasarkan spesifikasi di `prd_sistem_operasional_pasar.md` dan panduan tampilan di `desain.md`.
> 
> Untuk FASE 1 ini, tolong bangun pondasi utamanya saja:
> 1. Setup arsitektur proyek dan terapkan seluruh aturan styling, palet warna, tipografi, dan komponen UI persis mengikuti `desain.md` serta contoh halaman yang ada.
> 2. Buat layout responsif dengan pendekatan "Mobile & Tablet Priority" (navigasi bawah/bottom-bar yang ramah jari untuk layar mobile/tablet, serta sidebar rapi untuk desktop).
> 3. Implementasikan fitur Multi-Bahasa (i18n) untuk Bahasa Indonesia (ID) dan Inggris (EN) lengkap dengan tombol pengubah bahasa secara instan di header.
> 4. Buat komponen Badge/Indikator Warna standar untuk pergerakan sistem:
>    - Hijau (#10B981): Barang Masuk Normal
>    - Biru (#3B82F6): Pergerakan/Distribusi Internal
>    - Ungu (#8B5CF6): Penjualan Keluar (POS & B2B)
>    - Oranye (#F59E0B): Aktivitas Khusus / Stok Emergency
>    - Merah (#EF4444): Selisih Negatif / Anomali / Opname Minus
> 
> Jangan buat fitur bisnis yang kompleks dulu. Pastikan kerangka layout, halaman awal, sistem bahasa ID/EN, dan komponen sesuai `desain.md` berjalan sempurna.
> ```

---

## FASE 2: Autentikasi, 8 Role Pengguna, & Manajemen Izin Akun (Tanpa Approval)
**Tujuan:** Membangun sistem login, simulasi/pengaturan 8 *Role*, serta mekanisme izin per akun (*Account-Based Permission*) dan pencatatan *Audit Trail*.

> **Salin Prompt Fase 2:**
> ```text
> Lanjut ke FASE 2: Sistem Autentikasi, Manajemen 8 Role, dan Pertanggungjawaban Akun.
> Tetap patuhi desain di `desain.md` dan dukung multi-bahasa (ID/EN).
> 
> Tolong bangun fitur berikut:
> 1. Skema data & halaman Login serta Pengelolaan Pengguna (khusus Super Admin) untuk 8 Role:
>    - Super Admin, Owner, Kepala Toko, Kepala Gudang, Staf Gudang (Helper), Staf Toko (Kasir/Helper), Sales, dan B2B (Mitra).
> 2. Fitur "Granular Account Permission": Karena sistem ini TIDAK menggunakan birokrasi approval atasan, buat pengaturan izin per akun (misal: centang izin apakah Staf Toko A boleh menginput Stok Emergency atau tidak).
> 3. Buat layanan/helper "Audit & Accountability Logger": Setiap kali user melakukan aksi, catat otomatis `user_id`, `nama_akun`, `role`, `waktu`, dan `kode_warna` pergerakannya.
> 4. Buat Role Switcher (atau akun demo untuk masing-masing dari 8 role) agar kita mudah menguji tampilan menu yang berbeda untuk setiap role sesuai matriks akses di PRD.
> ```

---

## FASE 3: Master Produk, Konversi Multi-Satuan, & Deteksi Kemiripan Nama
**Tujuan:** Membangun manajemen produk yang mendukung satuan bertingkat (Karung ke Kg/Pcs), perhitungan HPP fleksibel, dan pendeteksi duplikasi nama produk.

> **Salin Prompt Fase 3:**
> ```text
> Lanjut ke FASE 3: Manajemen Master Produk, Multi-Satuan (UOM), dan Analisis Kemiripan Nama.
> Gunakan komponen UI sesuai `desain.md` (prioritas kenyamanan layar sentuh tablet/mobile).
> 
> Tolong bangun fitur berikut:
> 1. CRUD Master Produk & Kategori dengan atribut: SKU, Barcode, Nama Produk, Satuan Dasar (misal: Kg, Pcs), HPP saat ini, Margin Batas Bawah, serta Range Harga Pasar (Harga Min & Harga Max).
> 2. Fitur "Analisis Kemiripan Nama Produk": Saat pengguna mengetik nama produk baru di form input, jalankan pengecekan kemiripan string secara real-time. Jika mirip >= 75% dengan produk yang sudah ada di database, tampilkan peringatan visual beserta daftar produk yang mirip agar tidak terjadi duplikasi data.
> 3. Fitur "Konversi Multi-Satuan (Multi-UOM)": Pengaturan konversi dari satuan besar gudang (misal: 1 Karung = 50 Kg, 1 Karton = 24 Pcs) beserta fitur aksi "Bongkar/Konversi Satuan" yang otomatis mengurangi stok satuan besar dan menambah stok satuan kecil.
> ```

---

## FASE 4: Sistem Kartu Member (QR/Barcode) & Manajemen Mitra B2B
**Tujuan:** Membuat fitur registrasi member ritel & B2B, generator Kartu Member ber-QR/Barcode, serta pengaturan harga khusus & plafon kredit B2B.

> **Salin Prompt Fase 4:**
> ```text
> Lanjut ke FASE 4: Sistem Kartu Member dengan QR/Barcode dan Manajemen Klien B2B.
> 
> Tolong bangun fitur berikut:
> 1. Manajemen Member (untuk Pelanggan Ritel dan Mitra B2B) yang bisa diakses oleh Super Admin, Kepala Toko, dan Sales.
> 2. Generator "Kartu Member Digital & Cetak": Setiap member otomatis memiliki kartu bergaya modern (sesuai `desain.md`) yang menampilkan Nama, Tipe/Tier Member, serta QR Code dan Barcode yang bisa di-scan atau diunduh.
> 3. Pengaturan khusus Mitra B2B:
>    - Tier Harga Khusus B2B (misal: Reguler, Gold, VIP) atau harga kontrak per produk.
>    - Pengaturan Plafon Piutang (Credit Limit) dan pelacakan Sisa Hutang berjalan.
> 4. Komponen Scanner / Input Cepat QR/Barcode Member yang siap dipanggil di halaman Kasir POS, Gudang, dan B2B.
> ```

---

## FASE 5: Operasional Gudang (PO Suplayer, Barang Masuk, & Distribusi)
**Tujuan:** Membangun modul kerja Kepala Gudang dan Staf Gudang untuk mengelola stok pusat, suplayer, dan pengiriman barang ke Toko.

> **Salin Prompt Fase 5:**
> ```text
> Lanjut ke FASE 5: Modul Operasional Gudang (Kepala Gudang & Staf/Helper Gudang).
> Pastikan tampilan untuk Staf Gudang sangat sederhana, ramah mobile/tablet, dan menyembunyikan informasi HPP/keuangan.
> 
> Tolong bangun fitur berikut:
> 1. Manajemen Suplayer & Purchase Order (PO) oleh Kepala Gudang, termasuk fitur Retur Barang Rusak ke Suplayer.
> 2. Penerimaan Barang Fisik (Goods Receipt) oleh Staf Gudang: mencocokkan barang datang dari suplayer. Saat selesai, stok gudang bertambah (Kode Warna: HIJAU) dan sistem memperbarui nilai HPP Fleksibel secara otomatis.
> 3. Manajemen Distribusi & Transfer Stok (Gudang <-> Toko):
>    - Pembuatan Surat Jalan pengiriman barang dari Gudang ke Toko.
>    - Tampilan "Picking & Packing" untuk Staf Gudang menyiapkan barang.
>    - Semua pergerakan transfer antar lokasi ini tercatat dengan Kode Warna: BIRU.
> 4. Indikator "Low Stock Alert" (peringatan stok menipis) di layar Gudang.
> ```

---

## FASE 6: Operasional Stok Toko, Stok *Emergency*, & Pengaturan Harga Pasar
**Tujuan:** Membangun manajemen inventaris di tingkat Toko, fitur belanja darurat (*Emergency Stock*) tanpa *approval*, dan pengaturan *range* harga pasar harian.

> **Salin Prompt Fase 6:**
> ```text
> Lanjut ke FASE 6: Manajemen Stok Toko, Stok Emergency, dan Pengaturan Range Harga Pasar.
> 
> Tolong bangun fitur berikut:
> 1. Halaman Penerimaan Transfer Stok di Toko (menerima kiriman dari Gudang) dan pengajuan permintaan stok dari Toko ke Gudang.
> 2. Fitur "Masukan Stok: Emergency & Suplayer Lokal" di Toko:
>    - Hanya bisa diakses oleh Kepala Toko atau Staf Toko yang akunnya diberi izin.
>    - Form input cepat berisi: Produk, Kuantitas, Harga Beli Darurat, Sumber/Pedagang, dan WAJIB unggah foto nota/bukti barang.
>    - Tanpa menunggu approval Owner, stok toko langsung bertambah saat disimpan, HPP fleksibel ter-update, dan log pergerakan otomatis ditandai dengan Kode Warna: ORANYE beserta nama akun penginputnya.
> 3. Halaman "Pengaturan Update Harga & Range Pasar" (Khusus Kepala Toko):
>    - Mengatur Harga Min dan Harga Max pasar harian per produk.
>    - Menampilkan informasi HPP Fleksibel terbaru dan Batas Kunci Harga Bawah (Floor Price) otomatis agar harga pasar tidak disetel merugi.
> ```

---

## FASE 7: Kasir POS Toko, *Floor Price Lock*, & Manajemen Shift Laci Kasir
**Tujuan:** Membangun layar Point of Sale (POS) untuk Staf Toko (Kasir) yang cepat, mendukung *range* harga pasar, mengunci harga di bawah modal, dan mengelola uang kasir.

> **Salin Prompt Fase 7:**
> ```text
> Lanjut ke FASE 7: Point of Sale (POS) Toko, Kunci Harga Bawah Otomatis, dan Shift Kasir.
> Desain halaman POS harus sangat responsif untuk Tablet & Desktop mengikuti `desain.md`.
> 
> Tolong bangun fitur berikut:
> 1. Manajemen Shift Kasir (Buka & Tutup Kasir):
>    - Input modal awal laci kasir saat buka shift, pencatatan kas keluar/masuk operasional toko, dan input hitungan uang fisik saat tutup shift (closing).
>    - Jika ada selisih uang kasir saat closing, otomatis tercatat dengan Kode Warna: MERAH atas nama akun kasir tersebut.
> 2. Antarmuka Kasir POS Cepat:
>    - Pencarian produk instan, scan Barcode barang, dan scan QR/Barcode Kartu Member pelanggan.
>    - Fitur "Input Harga Range Pasar": Kasir dapat memilih atau menyesuaikan harga jual produk di dalam rentang Harga Pasar yang berlaku.
>    - Fitur "System Floor Price Lock": Jika harga yang dimasukkan menyentuh angka di bawah Floor Price (HPP + Margin Minimum), sistem otomatis memblokir transaksi dan memberi peringatan merah (tanpa perlu panggil atasan untuk approval).
> 3. Pembayaran Multi-Metode (Tunai, QRIS, Transfer, & Split Payment) serta fitur Retur Pelanggan Toko. Semua penjualan selesai dicatat dengan Kode Warna: UNGU.
> ```

---

## FASE 8: Modul Transaksi B2B, Portal Mitra, & Tiket QR *Pick-Up* (Toko/Gudang)
**Tujuan:** Membangun alur penjualan grosir B2B, portal untuk Sales & Mitra B2B, validasi plafon piutang otomatis, serta mekanisme pengambilan barang di Toko atau Gudang.

> **Salin Prompt Fase 8:**
> ```text
> Lanjut ke FASE 8: Modul B2B, Portal Sales/Mitra, dan Alur Pick-Up Barang (Toko maupun Gudang).
> 
> Tolong bangun fitur berikut:
> 1. Antarmuka Pemesanan & Request Stok B2B (untuk Role Sales dan Role B2B):
>    - Menampilkan katalog produk dengan "Harga Khusus B2B" sesuai tier member.
>    - Fitur "Request Stok" jika barang yang diinginkan mitra sedang habis.
>    - Fitur "Auto-Block Credit Limit": Jika mitra memilih bayar Tempo (TOP) namun total hutangnya melewati plafon kredit atau ada tagihan jatuh tempo, sistem otomatis menolak opsi tempo.
> 2. Pilihan Lokasi Pick-Up & Tiket QR Pengambilan:
>    - Saat transaksi B2B dibuat, pengguna memilih lokasi pengambilan: "Pick-Up di Toko" atau "Pick-Up di Gudang".
>    - Sistem menghasilkan Tiket Pengambilan berisi QR Code unik.
> 3. Fitur "Scan Serah Terima Pick-Up" (untuk Staf Toko & Staf Gudang):
>    - Staf memindai QR Tiket Pesanan atau QR Kartu Member B2B dari layar mobile/tablet mereka.
>    - Saat dikonfirmasi selesai diserahkan, stok di lokasi tersebut (Toko atau Gudang) otomatis terpotong (Kode Warna: UNGU) dan status tiket terkunci menjadi "Selesai Diambil" lengkap dengan nama akun staf yang menyerahkan.
> ```

---

## FASE 9: Fitur Stok Opname Mandiri di Toko & Gudang (*Blind Count*)
**Tujuan:** Membangun alur Stok Opname di Toko maupun Gudang dengan metode hitung buta (*blind count*) oleh staf dan finalisasi langsung oleh Kepala tanpa *approval* Owner.

> **Salin Prompt Fase 9:**
> ```text
> Lanjut ke FASE 9: Fitur Stok Opname untuk Area Toko dan Area Gudang.
> 
> Tolong bangun alur Stok Opname 4 tahap berikut yang berlaku baik di Toko maupun di Gudang:
> 1. Buka Sesi Opname: Kepala Toko (untuk stok Toko) atau Kepala Gudang (untuk stok Gudang) membuka sesi opname baru.
> 2. Layar "Blind Count" (untuk Staf Toko & Staf Gudang di Mobile/Tablet):
>    - Staf menginput jumlah fisik barang yang dihitung di rak/etalase.
>    - PENTING: Sembunyikan angka "Stok Sistem" dari layar Staf agar hitungan mereka jujur dan objektif.
> 3. Layar Rekonsiliasi (untuk Kepala Toko / Kepala Gudang):
>    - Menampilkan perbandingan Stok Sistem vs Hitungan Fisik Staf beserta selisihnya.
>    - Untuk setiap produk yang selisih, Kepala wajib memilih alasan: Susut, Rusak/Busuk, Hilang, atau Salah Input.
> 4. Finalisasi Langsung Tanpa Approval:
>    - Saat Kepala menekan "Selesai & Sesuaikan Stok", stok langsung diperbarui saat itu juga.
>    - Semua selisih minus otomatis masuk ke Log Pergerakan dengan Kode Warna: MERAH, menghitung nilai kerugian (Selisih x HPP), dan mencantumkan nama akun Kepala Toko/Gudang sebagai penanggung jawab penuh.
> ```

---

## FASE 10: Dashboard Eksekutif Owner, Log Pergerakan Berwarna, & Laporan Anomali
**Tujuan:** Membangun pusat pemantauan pasif untuk Owner dan Super Admin yang merangkum laporan keuangan, pergerakan stok berwarna, dan laporan penyimpangan (*Exception Report*).

> **Salin Prompt Fase 10:**
> ```text
> Lanjut ke FASE 10 (Fase Terakhir): Dashboard Laporan Owner, Log Pergerakan Berwarna, dan Laporan Anomali.
> Pastikan tampilannya sangat bersih, mudah dipindai di layar Mobile maupun Desktop sesuai `desain.md`.
> 
> Tolong bangun fitur pemantauan pasif (tanpa tombol approval yang membebani Owner) berikut:
> 1. Dashboard Keuangan & Analitik Eksekutif:
>    - Ringkasan Laba-Rugi real-time, Arus Kas, Perbandingan Omzet Toko vs B2B, Total Nilai Aset Stok (Gudang vs Toko), dan Daftar Piutang B2B Jatuh Tempo.
> 2. "Live Color-Coded Movement Log" (Log Pergerakan Berwarna):
>    - Tabel/timeline seluruh pergerakan barang dan transaksi dengan filter cepat berdasarkan 5 Kode Warna (Hijau, Biru, Ungu, Oranye, Merah), lokasi (Gudang/Toko/B2B), dan nama akun eksekutor.
> 3. "Laporan Anomali & Pertanggungjawaban Akun (Exception Report)":
>    - Panel khusus yang langsung menyorot aktivitas Warna MERAH dan ORANYE:
>      a. Riwayat Hasil Stok Opname Toko & Gudang (rincian barang rusak/susut/hilang, nilai kerugian, dan nama Kepala penanggung jawab).
>      b. Daftar input Stok Emergency di Toko lengkap dengan preview Foto Nota belanja dan nama akun penginput.
>      c. Riwayat selisih uang laci kasir saat tutup shift dan riwayat pembatalan struk (void).
> 4. Lakukan pengecekan menyeluruh agar seluruh alur dari Fase 1 hingga Fase 10 saling terhubung dengan mulus dan seluruh teks mendukung terjemahan Bahasa Indonesia (ID) dan Inggris (EN).
> ```