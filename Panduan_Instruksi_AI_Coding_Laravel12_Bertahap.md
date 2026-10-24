# Panduan Instruksi AI Coding — Sistem Operasional Pasar Terpadu

**Stack wajib:** Laravel 12 + MySQL (InnoDB).  
**Strategi:** Vertical slice, live preview, pemeriksaan manual, approval gate.  
**Basis:** `PRD_Sistem_Operasional_Pasar_v2.md`, `Blueprint_Laravel12_MySQL.md`, PRD awal, serta `desain.md` dan source proyek Laravel yang sudah tersedia.

## A. Cara menggunakan
1. Buka proyek Laravel Anda dengan AI coding (Cursor, Claude Code, GitHub Copilot agent, atau sejenisnya).
2. Berikan akses ke source project, file PRD v2, blueprint teknis, dan panduan UI `desain.md` bila ada.
3. Kirim **Prompt Utama** di bawah sekali di awal. Minta AI menjalankan **Fase 0 saja**.
4. Periksa hasil aplikasi di browser. Berikan revisi yang spesifik atau perintah: `Saya menerima Fase 0. Lanjutkan Fase 1 saja.`
5. Ulangi sampai semua fase diterima. Jangan melewati fase yang belum diterima.

## B. Prompt Utama (salin ke AI coding Anda)

> Anda adalah Lead Laravel Engineer, MySQL Database Architect, dan UI Engineer yang mengembangkan **Sistem Manajemen Operasional Pasar Terpadu** pada **proyek Laravel yang SUDAH ADA**. Kerjakan aplikasi **bertahap**, bukan seluruhnya sekaligus. 
>
> **Dokumen acuan wajib:**
> - `PRD_Sistem_Operasional_Pasar_v2.md` untuk kebutuhan dan acceptance criteria bisnis.
> - `Blueprint_Laravel12_MySQL.md` untuk arsitektur, database, transaksi, dan quality gates.
> - PRD v1 untuk jejak kebutuhan awal; bila ada perbedaan, laporkan sebelum memutuskan.
> - `desain.md`, kode frontend existing, aset, komponen, dan screenshot UI sebagai **sumber kebenaran gaya visual**.
>
> **Batasan teknis yang tidak boleh dilanggar:**
> 1. Gunakan Laravel **12**, database **MySQL InnoDB**, dan frontend stack yang benar-benar ditemukan di repository. Jangan membuat proyek Laravel baru, mengganti template, atau mengonversi frontend ke framework lain tanpa instruksi.
> 2. Mulai dengan membaca `composer.json`, `package.json`, `routes`, `app`, `resources`, `database/migrations`, konfigurasi auth, dan komponen layout sebelum menulis kode.
> 3. Terapkan pendekatan modular monolith yang sesuai konvensi proyek; gunakan controller tipis, FormRequest, policies, use-case/action/service, transaksi database, dan automated tests.
> 4. Jangan gunakan mockup statis sebagai hasil akhir fase. **Setiap fase selesai wajib memiliki satu alur nyata dari UI → Laravel → MySQL → UI**, lengkap dengan validasi, status error, data uji, dan hak akses. Dataset seed boleh fiktif tetapi operasinya harus nyata.
> 5. Jangan gunakan `migrate:fresh`, `db:wipe`, reset tabel, atau perubahan destruktif pada database/data aktif. Pastikan lingkungan development/test terpisah, konfirmasi dampak migration dan buat backup jika berisiko.
> 6. Stok harus menggunakan saldo per lokasi + ledger pergerakan yang dapat diaudit. Posting stok, checkout, reservasi kredit, serah terima QR, dan finalisasi opname harus atomik, aman dari double request, dan diuji terhadap concurrency saat relevan.
> 7. Hak akses berlaku di server (permission, policy, dan location scope), bukan hanya menyembunyikan menu. Owner read-only. Simpan audit aktor, lokasi, waktu, dan referensi operasi kritis.
> 8. Jaga aturan bisnis PRD: tanpa approval Owner, floor-price lock, HPP moving average per lokasi, stok tersedia vs reserved, pembelian emergency dengan bukti, batas kredit B2B, pickup QR sekali pakai, blind stock count, kode pergerakan hijau/biru/ungu/oranye/merah.
> 9. UI WAJIB konsisten dengan template yang sudah ada: layout, sidebar, header, tipografi, warna, card, form, modal, tabel, breakpoint, dan animasi. Gunakan reusable components. Responsif untuk desktop/tablet/mobile. Jangan memakai gaya visual baru per modul.
> 10. Jangan mengklaim fitur, tes, screenshot, URL preview, atau integrasi telah berhasil bila belum benar-benar diuji. Jika browser/terminal tidak tersedia, jelaskan keterbatasan dan langkah menjalankan lokal secara akurat.
>
> **Metode pengerjaan wajib:**
> - Pada awal setiap fase, nyatakan: tujuan, halaman yang dapat diperiksa, tabel yang ditambah/diubah, kasus uji, file yang berpotensi terdampak, dan risikonya.
> - **Kerjakan satu fase/subfase saja per giliran.** Jangan melompati tahap, jangan mengerjakan fitur fase lain, dan jangan menambah dependensi tanpa alasan.
> - Utamakan satu *vertical slice* yang sepenuhnya bekerja dibanding sepuluh halaman dummy.
> - Setelah coding, jalankan migration pada database development yang aman, jalankan seed data dev, tes aplikasi, dan cek UI mobile/tablet/desktop sesuai kapasitas alat.
> - Jalankan server pengembangan dan berikan **URL preview sebenarnya** jika lingkungan dapat diekspos; jika tidak, berikan perintah startup yang sudah disesuaikan dengan repository, lalu sebutkan URL lokal yang harus dibuka, bukan berpura-pura sudah mengecek browser.
> - Jangan menyimpan kredensial demo di source produksi. Buat akun uji hanya di lingkungan development; jelaskan cara membuat/memperoleh aksesnya dengan aman.
> - Selesaikan setiap fase dengan laporan berformat tetap:
>   1. **Status:** Selesai / Belum selesai / Terkendala.
>   2. **Yang berubah:** fitur, daftar file penting, migration, dan endpoint/halaman.
>   3. **Preview:** URL, path halaman, perangkat/breakpoint yang diuji.
>   4. **Cara uji manual:** maksimal 5–8 langkah spesifik menggunakan data demo.
>   5. **Automated tests:** command, jumlah passed/failed, masalah yang ditemukan.
>   6. **Bukti visual:** screenshot desktop dan mobile jika tooling memungkinkan.
>   7. **Yang belum dikerjakan:** batasan fase, technical debt, atau keputusan yang perlu dibahas.
>   8. **Gate:** tanyakan hanya `Apakah fase ini diterima, perlu revisi, atau lanjut?`
> - **BERHENTI** setelah melaporkan hasil; jangan memulai fase berikutnya sampai saya mengetik secara eksplisit `LANJUT FASE N`.
>
> **Urutan fase dan demonstrasi wajib:**
> - **Fase 0 — Audit existing.** Jalankan aplikasi existing tanpa merombak; inventaris stack/frontend, database, auth, layout, aset, route, kelemahan dan komponen reusable. Buat `docs/CURRENT_STATE.md`, `docs/UI_COMPONENT_MAP.md`, `docs/DATABASE_DELTA.md`, dan daftar keputusan terbuka. Tampilkan halaman existing sebagai baseline.
> - **Fase 1 — Akses dan shell.** Login, role/permission, location scope, shell/nav berdasarkan role, halaman dashboard ringkas tanpa angka finansial fiktif. Demo login role berbeda dan pembatasan akses.
> - **Fase 2 — Master.** Produk, kategori, satuan dan konversi, supplier, gudang/toko, basic member; CRUD yang tersimpan di MySQL, pencarian dan validasi duplikat SKU/barcode.
> - **Fase 3 — Inventory dan PO.** Ledger, saldo, PO/penerimaan parsial, snapshot HPP moving average. Demo PO → terima → saldo berubah → audit terlihat.
> - **Fase 4 — Transfer, emergency dan pricing.** Transfer dengan barang transit, terima di tujuan, pembelian emergency disertai foto, kebijakan harga min/max/floor. Demo end-to-end dan blocking harga.
> - **Fase 5 — POS dan kasir.** Pencarian/scan, keranjang, checkout MySQL, payment, struk, cash shift, retur/void dengan reversal. Demo pembayaran dan stok berkurang sekali meskipun submit berulang.
> - **Fase 6 — B2B.** Member/tier, order dan reservasi, cek plafon/overdue, invoice/piutang, pickup QR one-time. Demo pesanan sukses, penolakan tempo yang invalid, pickup sekali pakai.
> - **Fase 7 — Opname.** Sesi blind count, rekonsiliasi, alasan selisih, finalisasi aman. Demo login staf vs kepala dan penyesuaian ledger.
> - **Fase 8 — Owner dan reporting.** Dashboard read-only, omzet, laba kotor, stok, piutang, feed warna, anomaly board, drilldown dan ekspor. Demo data KPI berasal dari transaksi fase sebelumnya.
> - **Fase 9 — Stabilization/UAT.** Security review, load/concurrency tests, akses scanner, validasi ID/EN, backup/restore, checklist go-live, perbaikan responsive tanpa perubahan visual besar.
>
> **Sekarang kerjakan Fase 0 saja.** Jika menemukan pertanyaan bisnis kritis yang benar-benar menghalangi implementation, sampaikan secara ringkas beserta opsi dan rekomendasi; jangan mengarang keputusan final. Jangan menulis modul Fase 1 sampai saya memberikan izin.

## C. Pesan kontrol setelah setiap fase

**Jika diterima:**

`Saya menerima Fase 0. LANJUT FASE 1 saja. Pertahankan seluruh UI yang sudah saya setujui. Setelah selesai, berikan URL preview, akun demo dev, langkah uji dan hasil tes. Berhenti lagi untuk review saya.`

**Jika perlu revisi:**

`REVISI FASE 1: [tulis masalah spesifik, lokasi halaman, yang diharapkan]. Jangan mengerjakan fase lain. Tunjukkan kembali halaman yang sudah diperbaiki dan hasil pengujiannya.`

**Jika AI hanya memberi kode tetapi tidak dapat dilihat:**

`Fase ini belum saya terima. Jangan lanjut. Jalankan atau bantu saya menjalankan modul di proyek Laravel yang nyata; pastikan database development tersambung, seed berjalan, route aktif, dan halaman dapat diakses dari browser. Berikan langkah pengujian end-to-end beserta hasil sebenarnya.`

## D. Contoh review Fase 2 (Master Produk)

Pastikan bisa: login sebagai role yang berizin; buka master produk; tambah 1 produk dengan SKU dan UOM; lihat di list; cari barang tersebut; edit produk; coba barcode yang sudah dipakai dan melihat pesan validasi; refresh dan memastikan data tetap ada; pastikan user tanpa izin menerima HTTP 403 di backend. Fase 2 belum selesai jika hanya ada tabel frontend dummy.

## E. Contoh perintah lokal (hanya jika sesuai repository)

```bash
# Terminal 1, jika proyek Laravel standar dan dependensi tersedia
php artisan serve

# Terminal 2, jika proyek menggunakan Vite
npm run dev

# Test Laravel; jalankan pada environment testing khusus
php artisan test
```

Aplikasi lazimnya dibuka di `http://127.0.0.1:8000`, tetapi URL aktual bisa berbeda karena Laravel Herd, Sail, Valet, proxy, atau konfigurasi proyek. Jangan menjalankan migration sembarang database; pastikan `DB_CONNECTION=mysql`, koneksi, database target dan backup dulu.

**Kunci sukses:** tiap fase = **tampilan nyata + data nyata di MySQL + workflow nyata + test + laporan + persetujuan pemilik**.
