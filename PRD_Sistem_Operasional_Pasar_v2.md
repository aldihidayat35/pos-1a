# PRODUCT REQUIREMENTS DOCUMENT (PRD)
## Sistem Manajemen Operasional Pasar Terpadu
**Cakupan:** Gudang Pusat, Multi-Toko, POS Ritel, Distribusi, dan Kemitraan B2B  
**Versi:** 2.0 — Rancangan hasil perapian dan pengembangan PRD 1.0  
**Status:** Draft siap direview pemilik produk dan tim pengembang  
**Tanggal:** 24 Oktober 2026  
**Platform:** Web responsif; mobile/tablet priority untuk operasional  
**Stack ditetapkan:** Laravel 12 dan MySQL (InnoDB)  
**Frontend:** Adaptasi template UI Laravel yang **sudah digunakan**, teknologi detail menunggu inspeksi proyek  
**Bahasa:** Indonesia dan Inggris; konteks awal mata uang IDR, zona waktu Asia/Jakarta

> **Batas dokumen:** Semua kebutuhan bisnis utama diambil dari PRD 1.0 pengguna. Bagian bertanda **[Pengembangan v2]** merupakan usulan penyempurnaan, bukan keputusan bisnis yang telah disetujui. File `desain.md`, template UI, dan source code Laravel belum dilampirkan sehingga standar visual aktual dan stack frontend belum dapat diverifikasi.

---

## 1. Ringkasan eksekutif
Sistem mengintegrasikan penerimaan barang dari pemasok, pengendalian persediaan per lokasi, penjualan ritel cepat, penjualan B2B dengan harga khusus dan fasilitas tempo, pemindahan barang, stok opname, pengendalian kasir, dan pengawasan Owner berbasis anomali. Semua perubahan stok dan uang harus dapat ditelusuri sampai dokumen sumber, pengguna, waktu, lokasi, dan nilai transaksi.

### 1.1 Prinsip produk yang wajib dipertahankan
1. **Cepat di lapangan:** input minimal, scan QR/barcode, desain sentuh, dan alur ringkas.
2. **Zero owner-approval bottleneck:** transaksi yang memenuhi aturan dieksekusi oleh pemegang izin, tanpa antrian persetujuan Owner. Finalisasi oleh Kepala Toko/Gudang adalah tanggung jawab operasional, bukan approval Owner.
3. **System lock di server:** harga, kredit, stok, pembatalan, dan peran dicegah atau diizinkan oleh aturan backend; UI hanya menampilkan hasilnya.
4. **Accountability by default:** pencatatan audit append-only, aktor, konteks role saat tindakan, lokasi, dokumen rujukan, dan alasan.
5. **Exception-driven owner view:** Owner hanya melihat dan menelusuri informasi, tanpa aksi operasional/approval.
6. **Gunakan template UI yang sudah ada:** konsistensi lebih penting daripada membuat gaya visual baru.

### 1.2 Sasaran keberhasilan [Pengembangan v2]
| Sasaran | Indikator penerimaan yang diusulkan |
|---|---|
| Kecepatan POS | Kasir dapat scan/cari, ubah jumlah, lalu selesaikan pembayaran dengan alur minimum |
| Integritas stok | Tidak ada penjualan atau pengambilan ganda dan saldo tidak negatif, kecuali kebijakan khusus yang disetujui secara eksplisit |
| Integritas kredit | Pesanan tempo tidak melewati eksposur kredit dan tidak diperbolehkan saat overdue |
| Traceability | Setiap koreksi saldo memiliki dokumen referensi, alasan, akun, dan timestamp |
| Visibilitas Owner | KPI harian, peringatan oranye/merah, dan tautan ke bukti dapat diakses dari satu dashboard |
| Konsistensi UI | Semua modul menggunakan layout, komponen, navigasi, token, dan perilaku responsif sesuai template existing |

---

## 2. Batas sistem dan aktor
### 2.1 Lokasi operasional
- **Gudang:** menyimpan dan mendistribusikan barang, termasuk pemenuhan pesanan B2B.
- **Toko:** menjual langsung kepada pelanggan; dapat menjadi lokasi pengambilan B2B.
- **Transit [Pengembangan v2]:** lokasi logis untuk barang yang sudah keluar dari asal, tetapi belum diterima tujuan; mencegah stok hilang atau terhitung ganda saat distribusi.
- **Multi-cabang [Pengembangan v2]:** satu database dapat memiliki banyak lokasi bertipe gudang atau toko. Setiap transaksi wajib membawa `location_id` yang sesuai.

### 2.2 Delapan role awal
| Role | Kewenangan dan batas akses |
|---|---|
| Super Admin | Master global, pengguna/izin, konfigurasi system lock, semua laporan; aksi kritis diaudit |
| Owner | Read-only KPI, laporan, nilai stok, HPP/laba, piutang, audit dan anomali |
| Kepala Toko | Harga pasar di lokasi, POS, kasir, penerimaan, emergency, transfer, finalisasi opname toko; HPP lokal |
| Kepala Gudang | Supplier, PO, penerimaan, transfer, penyiapan B2B, finalisasi opname gudang; harga beli |
| Staf Gudang | Penerimaan fisik, picking/packing, serah-terima QR, blind count; tanpa HPP |
| Staf Toko | POS, kasir, penerimaan stok, emergency bila diizinkan, QR pickup, blind count; tanpa HPP |
| Sales | Registrasi/aktivasi mitra, pesanan B2B, informasi stok dan tagihan yang diberi izin; tanpa HPP |
| B2B/Mitra | Katalog/harga pribadi, order, tiket pickup, invoice dan histori tagihannya sendiri |

**Aturan RBAC [Pengembangan v2]:** role tidak disimpan sebagai satu ENUM permanen di `users`. Gunakan role/permission relasional, policy Laravel, dan `user_locations`; seseorang dapat bekerja pada beberapa lokasi. Owner tidak mendapat permission mutasi dari akses dashboard.

---

## 3. Ruang lingkup fungsional
| ID | Modul | Cakupan hasil |
|---|---|---|
| M01 | Akses & Pengguna | Login, otorisasi granular, penugasan lokasi, profil, pilihan bahasa |
| M02 | Master | Produk, kategori, SKU, barcode, satuan, supplier, lokasi, pelanggan/mitra |
| M03 | Purchasing | PO, penerimaan, selisih kuantitas, pencatatan harga beli |
| M04 | Inventory | Saldo per lokasi, riwayat mutasi, stok tersedia vs terpesan, konversi satuan |
| M05 | Transfer | Permintaan/pengiriman/penerimaan gudang↔toko dengan barang transit |
| M06 | Emergency | Pembelian mendesak di toko, wajib bukti, tanpa Owner approval |
| M07 | Pricing & HPP | Moving average, harga bertingkat, rentang pasar, floor lock |
| M08 | POS & Cash Shift | Transaksi cepat, pembayaran multi-metode, retur/void terkontrol, buka/tutup kasir |
| M09 | Member & B2B | Kartu digital/fisik, tier, order, limit kredit, invoice, pembayaran piutang |
| M10 | Fulfillment | Reservasi barang, picking, tiket pickup, scan penyerahan sekali pakai |
| M11 | Opname | Sesi, blind count, rekonsiliasi, alasan selisih, finalisasi |
| M12 | Owner & Reports | Dashboard finansial, arus aktivitas warna, penelusuran anomali, ekspor laporan |
| M13 | Platform | Audit, notifikasi, lampiran privat, terjemahan, backup, pengujian |

---

## 4. Peraturan bisnis lintas modul
### 4.1 Keadaan persediaan
- **On hand** = kuantitas fisik pembukuan per produk dan lokasi, dalam satuan dasar.
- **Reserved** = kuantitas yang dialokasikan untuk order aktif dan belum dipenuhi.
- **Available** = `on_hand - reserved`; harus nonnegatif pada proses normal.
- **In transit** = barang di lokasi transit, bukan sekaligus di lokasi asal maupun tujuan.
- **Penjualan** baru dapat diposting jika kuantitas tersedia mencukupi dan system lock terpenuhi.
- Mutasi stok ditulis **hanya** dari layanan domain inventori, secara atomik dengan dokumen bisnis terkait.

### 4.2 Produk, satuan, dan kemiripan nama
- SKU dan barcode harus unik sesuai kebijakan bisnis; nama produk tidak otomatis menjadi kunci unik.
- Nama ternormalisasi (spasi, kapital, variasi penulisan satuan) dipakai untuk pencarian kandidat produk duplikat. Kemiripan >=75% menghasilkan **peringatan**, bukan pemblokiran mutlak; parameter dapat dikonfigurasi.
- Stok dibukukan pada **satu satuan dasar** (contoh kg, pcs, liter). Satuan jual/beli (karung, karton, lusin) memakai faktor konversi yang konsisten.
- **[Pengembangan v2]** Menjual 1 karung berisi 50 kg berarti pengurangan 50 kg pada ledger. Membuka karung tidak menciptakan tambahan stok 50 kg bila sebelumnya sudah dibukukan sebagai 50 kg. Jika kemasan utuh dan curah benar-benar harus dibedakan sebagai stok fisik, modelkan sebagai SKU/kemasan berbeda dan proses repack yang seimbang.
- Faktor konversi yang sudah dipakai pada transaksi tidak boleh diubah retroaktif; simpan snapshot faktor dan satuan pada item transaksi.

### 4.3 HPP dan batas harga
- Metode HPP default: **weighted moving average per produk-lokasi**; gunakan harga pokok dalam satuan dasar.
- Penerimaan dengan harga beli memperbarui rata-rata lokasi tujuan. Perpindahan antar lokasi membawa cost dari asal; bukan dianggap pembelian baru, dan tidak menciptakan laba.
- Contoh: stok lama 20 kg @Rp12.000, barang masuk 10 kg @Rp15.000, HPP baru = Rp13.000/kg.
- Saat penjualan diposting, `unit_cost_snapshot` dan `cogs_total` disimpan di detail penjualan untuk stabilitas histori laba.
- `floor_price = current_unit_cost + minimum_markup`; `minimum_markup` ditetapkan sebagai nominal atau persentase atas HPP. **Definisi markup vs margin penjualan harus disepakati** sebelum coding.
- `effective_min_price = max(market_min_price, floor_price)`. Rentang jual valid hanya bila `effective_min_price <= market_max_price`. Jika tidak, transaksi terkunci sampai konfigurasi harga diperbaiki.
- Harga jual dan batas diverifikasi kembali di backend saat `checkout`, tidak cukup validasi JavaScript.
- Diskon/negosiasi B2B mengikuti kebijakan channel/tier dan tetap menjalani batas harga minimum yang berlaku bila kebijakan tidak menyediakan pengecualian eksplisit.

### 4.4 Kredit B2B
- **Eksposur kredit** = invoice belum lunas + nilai order tempo aktif yang belum menjadi invoice (tanpa menghitung dua kali order yang sudah difakturkan).
- Jika `exposure + proposed_credit_amount > credit_limit`, atau ada invoice overdue, opsi tempo ditolak secara otomatis; mitra boleh memilih pembayaran non-tempo sesuai aturan.
- Pemeriksaan kredit dan pencatatan komitmen piutang harus di transaksi database dengan penguncian baris mitra terkait.
- Piutang aktual dihitung dari invoice dan alokasi pembayaran; `current_debt` bukan angka bebas yang diedit pengguna.

### 4.5 Warna aktivitas (semantic status)
| Warna | Token | Arti | Contoh |
|---|---|---|---|
| Hijau | `movement.inbound` / `#10B981` | Masuk normal | penerimaan PO, retur layak jual |
| Biru | `movement.internal` / `#3B82F6` | Perpindahan internal | transfer, reservasi/picking |
| Ungu | `movement.outbound` / `#8B5CF6` | Keluar untuk penjualan | POS, penyerahan B2B |
| Oranye | `movement.warning` / `#F59E0B` | Aktivitas khusus/peringatan | pembelian emergency, kredit mendekati limit |
| Merah | `movement.critical` / `#EF4444` | Anomali/kerugian | selisih opname negatif, void, minus kasir |

Warna di atas adalah **semantik peristiwa**, bukan sumber data akuntansi. UI wajib menampilkan ikon dan label teks selain warna. Peta token harus diadaptasi ke palet/komponen template existing tanpa mengubah maknanya.

---

## 5. User stories dan alur operasional
### 5.1 Master produk dan satuan (M02)
**Sebagai Kepala Gudang**, saya dapat menambah produk, memilih satuan dasar, barcode, SKU, kategori, dan satuan beli/jual agar seluruh transaksi berbicara kuantitas yang sama.

**Alur:** buka master → isi produk → periksa saran nama mirip → tetapkan UOM → simpan → produk dapat dicari di PO/POS. Produk tidak dihapus fisik jika memiliki histori; gunakan status tidak aktif.

**Penerimaan:** SKU unik; faktor UOM >0; duplikasi baris tidak dibuat oleh permintaan ganda; saran nama mirip muncul tanpa membocorkan data yang tak boleh dilihat role.

### 5.2 PO dan penerimaan barang (M03)
**Sebagai Kepala Gudang**, saya membuat PO dan staf menerima barang fisik sesuai jumlah aktual.

**Alur:** `DRAFT → ISSUED → PARTIALLY_RECEIVED/RECEIVED → CLOSED`; penerimaan disimpan sebagai dokumen goods receipt dengan item, qty diterima, harga aktual, supplier, dan aktor. Selisih PO dicatat; stok dan HPP berubah hanya untuk penerimaan yang diposting.

**Penerimaan:** dapat menerima parsial; tidak melebihi toleransi PO tanpa izin tertentu; satu receipt tidak boleh terposting dua kali; referensi mutasi ke receipt dapat dilihat.

### 5.3 Transfer Gudang ↔ Toko (M05)
**Sebagai Kepala Gudang/Kepala Toko**, saya mengirim barang dan pihak tujuan mencatat penerimaan aktual.

**Alur:** `DRAFT → DISPATCHED → PARTIALLY_RECEIVED/RECEIVED` atau `CANCELLED` menurut aturan. Saat dispatch stok asal berkurang dan transit bertambah. Saat receive transit berkurang dan stok tujuan bertambah, dengan snapshot nilai cost yang sama.

**Penerimaan:** barang tidak muncul dua kali; penerimaan parsial dan selisih rusak/hilang memiliki catatan; transfer terkunci bila saldo asal tidak cukup; audit pengirim/penerima tersimpan.

### 5.4 Pembelian emergency di toko (M06)
**Sebagai Staf Toko berizin**, saya dapat membeli barang mendesak dari pedagang pasar tanpa approval Owner agar penjualan tetap berjalan.

**Alur:** pilih lokasi dan produk → input kuantitas/satuan, harga aktual, pemasok/pedagang → unggah foto nota/produk → posting penerimaan → stok & HPP lokasi diperbarui → anomali **oranye** dipublikasikan.

**Penerimaan:** bukti wajib; idempotensi posting; bukti privat; `created_by` dan metadata dokumentasi disimpan; nominal aneh dapat memicu peringatan tambahan, tetapi tidak menahan transaksi yang sah.

### 5.5 POS ritel dan shift kasir (M08)
**Sebagai Kasir**, saya melakukan transaksi dengan cepat pada tablet atau desktop.

**Alur:** buka shift → scan produk/member opsional → atur qty dan harga tawar dalam batas → checkout → konfirmasi jenis pembayaran → posting sale, pembayaran, stok keluar, HPP snapshot dan struk → tutup shift → Kepala Toko memvalidasi hasil jika peran mensyaratkan.

**Penerimaan:** cek on-hand/reservasi, harga, shift terbuka, dan pembayaran dilakukan saat posting; QRIS/transfer yang belum terkonfirmasi tidak dinyatakan lunas; satu request checkout hanya menghasilkan satu transaksi; kasir tidak melihat HPP.

**Retur/void:** transaksi yang sudah diposting tidak boleh dihapus. Buat dokumen pembalik yang mengacu ke transaksi asal, alasan wajib, izin khusus dan audit merah.

### 5.6 Keanggotaan dan katalog (M09)
**Sebagai Sales/Mitra**, saya dapat membuat atau memakai kartu member berkode unik, melihat tier harga dan riwayat pembelian saya.

**Penerimaan:** tier dan penetapan harga disimpan terpisah; kode kartu tidak berisi data pribadi mentah; kartu dapat dinonaktifkan/diganti; Sales dibatasi mitra/area kewenangannya.

### 5.7 Pesanan B2B, tempo, dan pickup QR (M09–M10)
**Sebagai Mitra**, saya dapat memesan dengan harga khusus, memilih ambil di toko atau gudang, dan membayar lunas atau tempo bila memenuhi aturan.

**Alur:** `DRAFT → PLACED → RESERVED → PICKING → READY_FOR_PICKUP → HANDED_OVER`; cabang `CANCELLED/EXPIRED` mengikuti keadaan barang. Saat order dikunci: lokasi pemenuhan ditetapkan, harga disnapshot, ketersediaan direservasi, limit kredit dicek secara atomik. Tiket QR dibuat saat barang **siap diambil**; status yang ditampilkan pelanggan: **Menunggu diambil**. Scan dilakukan staf di lokasi valid; barang keluar satu kali, reservasi dilepas, dan invoice dibukukan sesuai kebijakan penagihan.

**Penerimaan:** tidak bisa scan tiket dari lokasi lain; scan ulang mengembalikan status `already redeemed`, bukan mengurangi stok lagi; pickup memerlukan staf login; QR token acak/ditandatangani, punya masa berlaku dan hanya hash token disimpan; order yang dibatalkan melepaskan reservasi dan komitmen kredit.

**Catatan keputusan:** aturan penerbitan invoice (ketika order confirmed atau handed over) dan penanganan pembayaran muka perlu diputuskan; blueprint mengusulkan invoice pada penyerahan barang untuk menghindari piutang barang yang belum diserahkan.

### 5.8 Blind stock opname (M11)
**Sebagai Kepala Gudang/Toko**, saya membuat sesi opname; **sebagai staf**, saya hanya melihat barang dan satuan saat menghitung.

**Alur:** `OPEN → COUNTING → RECONCILIATION → FINALIZED`; pilih lokasi dan subset produk → staf mengisi kuantitas fisik tanpa sistem qty → kepala melihat expected vs counted → alasan untuk setiap selisih → posting koreksi atomik → anomali merah bila negatif.

**Penerimaan:** setiap item memiliki `counted_at`, `baseline_qty`, `expected_qty_at_count`, `difference` dan actor. Jika transaksi stok pada barang yang sama terjadi selama penghitungan, sistem meminta hitung ulang atau menerapkan mode penghitungan berlandaskan ledger yang tervalidasi. Finalisasi dihitung ulang terhadap saldo terkini di dalam transaksi, dan tidak bisa dijalankan dua kali.

### 5.9 Owner dashboard (M12)
**Sebagai Owner**, saya melihat satu halaman untuk menjawab: apa omzet hari ini, laba kotor, nilai persediaan, sisa piutang, dan aktivitas abnormal.

**Komponen:**
1. KPI omzet ritel vs B2B, estimasi laba kotor = pendapatan bersih dari retur/diskon – COGS transaksi yang telah diposting; nilai aset stok; piutang outstanding.
2. Feed peristiwa 5 warna dengan filter cepat **Merah** dan **Oranye**, timestamp dan lokasi.
3. Accountability board per pengguna dan dokumen sumber, termasuk link bukti emergency/selisih.
4. Filter periode dan lokasi, drill-down transaksi, ekspor.

**Penerimaan:** semua nilai memakai definisi dan cutoff waktu yang konsisten; aktivitas yang batal/retur diperlakukan benar; tidak menyamakan nilai penjualan yang belum diserahkan dengan omzet yang sudah terealisasi.

---

## 6. Aturan keamanan dan tanggung jawab
- Semua endpoint menjalankan autentikasi, **permission + policy + location scope**, bukan hanya menyembunyikan menu.
- Mutasi operasi krusial mencatat `actor_user_id`, `actor_role_snapshot`, `location_id`, `event_at`, `reference`, `before/after` (tanpa menulis data rahasia), dan `reason` bila perlu.
- Lampiran nota disimpan di private disk; unduhan melalui route terotorisasi. Kontrol jenis/ukuran file; nama berkas acak.
- Akun B2B tidak boleh membaca data B2B lain. Kepala Toko tidak boleh membaca HPP toko lain bila tidak diberi izin.
- Perubahan harga, margin minimum, limit kredit, pembatalan, koreksi stok dan status user memunculkan log.
- Sistem mendukung soft deactivation untuk entitas master; dokumen final dan ledger tidak diedit/hapus melalui UI, koreksi melalui transaksi pembalik.
- Backup, retensi, proteksi akses log, prosedur restore, dan batas sesi diatur sebelum go-live.

---

## 7. UI/UX — kontrak integrasi template existing
### 7.1 Aturan utamanya
1. **Tidak mendesain ulang layout**. Inventarisasi `layouts`, komponen, Tailwind/CSS variables, warna, tipografi, sidebar, header, topbar, kartu, tabel, modal, form, toast, dan breakpoint dari proyek saat ini.
2. Sediakan **design tokens adapter**, memetakan status bisnis seperti `critical` atau `warning` ke class/style yang dipakai template. Jangan menambah palet independen per halaman.
3. Susun **shared components** untuk `AppShell`, `PageHeader`, `StatCard`, `DataTable`, `FilterBar`, `StatusBadge`, `Money`, `Quantity`, `ConfirmDialog`, `ProofViewer`, `BarcodeScanner`, `EmptyState`, `LoadingState`, dan `PermissionGate`. Nama/teknologi disesuaikan template.
4. Navigasi dan breadcrumb sama di setiap role, dengan menu tersaring permission dan lokasi. Jangan menduplikasi markup header/sidebar dalam setiap modul.
5. Semua halaman memiliki keadaan `loading`, `empty`, `error`, `success`, `permission denied`, dan opsi retry jika sesuai.
6. Gunakan label+ikon selain warna; semua tombol operasional menyertakan feedback atas aksi dan proteksi klik ganda.
7. Konsistensi bahasa: string melalui sistem translasi ID/EN. Sakelar bahasa memperbarui komponen aktif tanpa full-page reload jika stack existing mendukung navigasi reaktif; bila tidak, implementasi perlu direncanakan khusus.

### 7.2 Adaptasi per perangkat
| Konteks | Tata letak yang dituju |
|---|---|
| Staf gudang/toko mobile | Bottom navigation; pencarian/scan satu tap; aksi utama sticky; target sentuh minimal 48×48 px |
| POS tablet landscape | Pencarian/scan produk dan keranjang dalam satu layar; keypad besar; shortcut keyboard desktop |
| Sales/B2B mobile | Daftar kartu produk, ringkasan order sticky, status tagihan jelas, halaman pickup QR terang |
| Kepala toko/gudang | Daftar operasi + detail; tindakan finalisasi jelas, menampilkan konsekuensi sebelum konfirmasi |
| Owner mobile | Kartu KPI, feed merah/oranye, filter sederhana; detail via drill-down |
| Super Admin desktop | Tabel terstruktur, filter, kolom penting, pagination dan form konfigurasi |

### 7.3 Peta halaman minimum
| Grup | Halaman utama |
|---|---|
| Akses | Login, profil, pengguna/role, penugasan lokasi |
| Produk | Produk, kategori, satuan, barcode, supplier, lokasi |
| Gudang | Daftar PO, detail penerimaan, transfer keluar/masuk, stok, opname |
| Toko | POS, riwayat struk, shift kasir, stok toko, emergency, transfer masuk, opname |
| B2B | Mitra, tier harga, order, picking, pickup QR, invoice, pembayaran |
| Owner | Ringkasan, feed aktivitas, anomali, laporan stok, laba, piutang, arus kas operasional |
| Sistem | Pengaturan kunci, audit logs, integrasi dan preferensi bahasa |

**Motion/animasi [Pengembangan v2]:** bila template memiliki animasi liquid/mengalir, pakai untuk transisi non-kritis yang ringan. Jangan menghambat POS/scanner; hormati `prefers-reduced-motion` dan hindari animasi yang menyamarkan perubahan saldo.

---

## 8. Persyaratan non-fungsional
| Area | Ketentuan |
|---|---|
| Data | MySQL 8.x InnoDB (disarankan 8.4 LTS), foreign key, unique constraint, transaksi atomik |
| Presisi | Kuantitas `DECIMAL(18,3)` sebagai default yang dapat diperluas; unit cost presisi tinggi; uang `DECIMAL(20,2)` tanpa float |
| Integritas | `idempotency_key` pada checkout, penerimaan, dispatch, pickup, dan finalisasi; locking per saldo/mitra |
| Audit | Append-only; reversal untuk koreksi; keterlacakan status dan pengguna |
| Performa [Target awal] | Respon scan/produk praktis instan; checkout tanpa query berat; dashboard dapat memakai agregasi/cache dengan indikator waktu pembaruan |
| Reliability | Queue untuk laporan/notifikasi; worker idempotent; event baru dipublish setelah commit |
| Keamanan | Validasi server, policy, CSRF/auth/session, rate limit login/QR, akses file privat |
| Observability | Log exception/latency, health check, failed jobs, alert backup dan error posting |
| Pengoperasian | Zona waktu disimpan UTC di database bila memungkinkan, ditampilkan Asia/Jakarta; angka dan mata uang konsisten |
| Testing | Unit untuk perhitungan, feature untuk permission dan workflow, concurrency untuk jual/pickup/credit/opname |
| Aksesibilitas | Keyboard POS, fokus jelas, label kontrol, ukuran target sentuh, alternatif ikon selain warna |

---

## 9. Skala prioritas implementasi
| Tahap | Deliverable | Kondisi selesai |
|---|---|---|
| 0 — Fondasi | Audit template existing, RBAC, master lokasi, master produk/UOM, audit log, desain kontrak UI | Menu dan komponen sesuai UI eksisting; izin per lokasi lulus test |
| 1 — Gudang/Toko | Supplier, PO/receipt, saldo & movement, transfer, emergency, HPP/pricing | Seluruh mutasi dapat ditelusuri, tidak ada stok ganda/minus |
| 2 — POS | Checkout, struk, payment, shift, void/retur, floor lock | Checkout tahan double click dan concurrency; laba terhitung |
| 3 — B2B | Member, tier, credit exposure, order reservasi, invoice, QR pickup | Tempo otomatis terkunci saat invalid; QR sekali pakai |
| 4 — Kontrol | Blind opname, dashboard Owner, anomaly stream, laporan, audit, ekspor | Selisih beralasan dan terlacak; dashboard read-only |
| 5 — Hardening | Load test, perangkat scan, bilingual, backup/restore, monitoring | UAT lapangan lolos dan operasional aman |

**Catatan:** tahap adalah urutan konstruksi, bukan penghapusan modul dari lingkup akhir v2. Rilis per tahap hanya setelah alur finansial-stok terkait selesai ujung-ke-ujung.

---

## 10. Matriks acceptance test minimum
| ID | Skenario | Hasil yang wajib |
|---|---|---|
| AT-01 | User tanpa hak mengakses endpoint harga pokok | HTTP 403; data HPP tidak bocor |
| AT-02 | 2 kasir membeli stok terakhir bersamaan | Hanya transaksi yang masih tersedia berhasil; tidak minus |
| AT-03 | Checkout dikirim ulang karena jaringan timeout | Struk sama, tidak ada penjualan/stok keluar kedua |
| AT-04 | Barang diterima PO parsial | Saldo bertambah sesuai qty diterima; HPP dihitung benar |
| AT-05 | Transfer sudah dikirim tapi belum diterima | Hanya dihitung di transit; asal/destinasi tidak ganda |
| AT-06 | Pembelian emergency tanpa foto | Ditolak; tidak ada stok berubah |
| AT-07 | Harga checkout di bawah floor | Ditolak server, meski UI dimanipulasi |
| AT-08 | Kredit melebihi limit atau ada overdue | Tempo ditolak; pembayaran non-tempo tetap dapat ditawarkan |
| AT-09 | Dua pickup scan token bersamaan | Hanya satu penyerahan berhasil; pemotongan stok sekali |
| AT-10 | Opname gagal sebelum commit | Stok/ledger/opname tetap konsisten, tidak setengah final |
| AT-11 | Staf blind count | Tidak menerima `system_qty` di payload respons, bukan hanya disembunyikan CSS |
| AT-12 | Staf toko membuka data toko lain via URL | Ditolak policy/location scope |
| AT-13 | POS refund/void | Ada reversal, alasan, aktor, dan warna anomali merah |
| AT-14 | Owner membuka dashboard | Hanya melihat; tidak punya tombol maupun endpoint mutasi |
| AT-15 | Ubah ID/EN | Label di modul inti berpindah bahasa, tanpa layout pecah |
| AT-16 | HPP produk berubah setelah jual | COGS pada struk lama tetap sama |

---

## 11. Hal yang harus diputuskan sebelum migration final
**Kritis (harus diputuskan):**
1. Apakah tersedia **satu gudang dan satu toko saja**, atau sudah ada multi-toko? Rancangan ini siap multi-lokasi.
2. Apakah semua barang stoknya boleh dihitung dalam satuan dasar, atau barang kemasan utuh (karung/karton) dan curah harus dibedakan sebagai SKU fisik?
3. Floor markup dihitung **% dari HPP** atau **target gross margin % dari harga jual**? Keduanya tidak sama.
4. Apakah kredit diterbitkan menjadi invoice saat **order dibuat** atau **barang diserahkan**? Rekomendasi v2: saat penyerahan.
5. Bila barcode offline / pembayaran QRIS belum terkonfirmasi, apakah kasir boleh menunda penyelesaian transaksi? Rekomendasi: `PAYMENT_PENDING` dan jangan klaim lunas.
6. Apa izin tepat untuk void, retur, koreksi harga, dan emergency; serta limit nominal operasi per role?
7. Pada opname, apakah SKU dapat dihentikan transaksinya selama hitung atau harus mengikuti metode **rolling count** dengan deteksi aktivitas saat penghitungan?
8. Apakah nilai uang dibukukan murni IDR, pajak/diskon berapa tingkat, dan bagaimana pembulatan eceran?

**Detail integrasi:** isi `desain.md`, jenis template UI (Blade/Livewire/Inertia/Vue/React), komponen dan aset eksisting, skema database yang sudah berjalan, serta standar deploy/auth saat ini belum tersedia. Jangan membuat ulang app tanpa audit ini.

---

## 12. Deliverables teknis terkait
1. **`Blueprint_Laravel12_MySQL.md`** — arsitektur aplikasi, struktur folder, daftar tabel relasional, relasi, state machine, transaksi aman, API/use cases, kontrak UI, prioritas migration dan test.
2. **Template UI existing + `desain.md`** — dibutuhkan untuk menghasilkan mapping komponen nyata; tidak tercakup dalam file yang dilampirkan.

---

## 13. Catatan perubahan dari PRD 1.0
| Area | PRD 1.0 | Perbaikan v2 |
|---|---|---|
| Lokasi | `GUDANG/TOKO` tipe saja | `locations` dan scoped balances, transfer transit |
| Role | ENUM + JSON pada users | RBAC relasional + policy + lokasi |
| Stok | tabel inventories dan movement umum | saldo on hand, reservasi, ledger dan reversal atomik |
| UOM | konversi dapat menambah stok dasar | konversi tidak boleh menciptakan stok fiktif |
| HPP | `current_hpp` di products | cost per lokasi + snapshot per item transaksi |
| Harga | rentang + floor | effective minimum tervalidasi backend, tier/channel |
| Transaksi | tabel generik POS & B2B | sales, orders, payment, invoice, pickup dipisah |
| Piutang | `current_debt` di members | derived dari invoice + pembayaran + komitmen aktif |
| QR pickup | token dan status di transaksi | token sekali pakai, terikat lokasi/order, aman concurrency |
| Opname | snapshot statis dan finalisasi | aman terhadap transaksi saat sesi berjalan dan posting ganda |
| UI | referensi ke desain.md | UI adapter, komponen reusable, responsif, ID/EN, state lengkap |

**Dokumen acuan:** PRD 1.0 yang dilampirkan pengguna. Usulan teknis mengacu pada kemampuan Laravel 12 dan prinsip transaksi MySQL InnoDB; semua angka target dan kebijakan tambahan perlu divalidasi saat grooming.
