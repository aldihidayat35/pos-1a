# Analisis Delta Database & Rencana Skema (DATABASE_DELTA.md)
**Sistem Manajemen Operasional Pasar Terpadu (Gudang, Toko, & B2B)**  
**Versi:** 1.0 (Baseline Audit)  
**Database Mesin:** MySQL 8.x InnoDB (`posagy`)  
**Acuan Teknis:** `Blueprint_Laravel12_MySQL.md` & `PRD_Sistem_Operasional_Pasar_v2.md`  

---

## 1. Status Tabel Database Saat Ini (Kondisi Awal)

Database `posagy` baru saja diinisiasi dengan migrasi dasar framework Laravel 12:

| Nama Tabel | Peran Sistem | Jumlah Kolom | Status Migrasi |
|---|---|---|---|
| `migrations` | Riwayat eksekusi migration Laravel | 3 kolom | Terpasang (Batch 1) |
| `users` | Akun pengguna dasar (`id`, `name`, `email`, `password`, `timestamps`) | 7 kolom | Terpasang (Batch 1) |
| `password_reset_tokens` | Token pemulihan kata sandi (`email`, `token`, `created_at`) | 3 kolom | Terpasang (Batch 1) |
| `sessions` | Pengelola sesi aktif web (`id`, `user_id`, `ip_address`, `payload`, `last_activity`) | 6 kolom | Terpasang (Batch 1) |
| `cache` & `cache_locks` | Penyimpanan cache berbasis MySQL | Masing-masing 3 kolom | Terpasang (Batch 1) |
| `jobs` & `job_batches` | Antrian background tasks berbasis database | Masing-masing 4–7 kolom | Terpasang (Batch 1) |
| `failed_jobs` | Pencatat task antrian yang gagal | 6 kolom | Terpasang (Batch 1) |

> **Catatan Kepatuhan:** Tabel operasional bisnis (inventaris, produk, pesanan, pergerakan stok, toko, member) **belum ada** sama sekali.

---

## 2. Analisis Perbedaan (Delta) Antara PRDv1 vs PRDv2 & Blueprint

Terdapat penyempurnaan arsitektural krusial antara draf awal (PRDv1) dan rancangan arsitektur matang (PRD v2 & Blueprint):

| Aspek Entitas | Desain Awal (PRDv1) | Desain Disempurnakan (PRD v2 & Blueprint) | Rationale & Pencegahan Resiko |
|---|---|---|---|
| **Role & Akses** | Kolom `role` ENUM (8 role) di tabel `users`. | Tabel relasional `roles`, `permissions`, `user_roles`, `user_permissions`. | Skalabilitas izin granular, prinsip least privilege, audit trail jelas. |
| **Cakupan Lokasi** | Kolom `location_id` tunggal di `users`. | Tabel `locations` (Warehouse, Store, Transit) + pivot `user_locations`. | Staf dapat bertugas di multi-cabang/lokasi dengan default active scope. |
| **Harga Pokok (HPP)** | Kolom `current_hpp` global di tabel `products`. | HPP moving average **per lokasi & per produk** pada `inventory_balances` / `stock_movements`. | Biaya perolehan di toko belanja darurat berbeda dari gudang sentral; HPP global menimbulkan distorsi laba. |
| **Saldo vs Ledger Stok** | Hanya ada tabel `inventories` dengan kuantitas tunggal. | Pemisahan: Saldo saat ini (`inventory_balances`) + Ledger append-only (`stock_movements`). | Traceability audit mutlak. Saldo dihitung atomik dengan locking, ledger tidak boleh dimodifikasi retroaktif. |
| **Lokasi Transit** | Belum ada lokasi transit eksplisit. | Lokasi virtual bertipe `TRANSIT` pada tabel `locations`. | Menjamin barang yang dikirim gudang ke toko tidak hilang atau terhitung ganda di perjalanan. |
| **Piutang B2B** | Kolom `current_debt` bebas diedit di tabel `members`. | Eksposur piutang dihitung dari `invoices` belum lunas + order tempo aktif. | Mencegah kecurangan manipulasi manual piutang mitra. |
| **Konversi Satuan** | Tabel konversi statis biasa. | `product_uoms` dengan `base_uom` baku dan snapshot faktor konversi pada transaksi. | Mencegah perubahan historis transaksi saat faktor konversi produk diubah di masa depan. |

---

## 3. Rencana Eksekusi Skema Database Per Fase

Sesuai aturan larangan perubahan destruktif (`no migrate:fresh`, `no db:wipe`), setiap fase akan menambah migration baru yang modular dan aman.

### Fase 1: Identity, Role, & Multi-Lokasi
- `users`: Penambahan kolom `username`, `phone`, `preferred_locale`, `is_active`.
- `roles`: `id`, `name`, `label`, `is_system`.
- `permissions`: `id`, `name`, `label`, `module`.
- `user_roles`: `user_id`, `role_id` (Composite PK).
- `role_permissions`: `role_id`, `permission_id` (Composite PK).
- `locations`: `id`, `code`, `name`, `type` (`WAREHOUSE`, `STORE`, `TRANSIT`), `address`, `is_active`.
- `user_locations`: `user_id`, `location_id`, `is_default` (Composite Unique).

### Fase 2: Master Data
- `product_categories`: `id`, `parent_id`, `name`, `code`, `is_active`.
- `uoms`: `id`, `code`, `name`, `symbol`, `dimension` (`COUNT`, `WEIGHT`, `VOLUME`), `precision`.
- `products`: `id`, `sku`, `name`, `normalized_name`, `category_id`, `base_uom_id`, `track_stock`, `is_active`.
- `product_uoms`: `id`, `product_id`, `uom_id`, `factor_to_base`, `barcode`, `is_purchase`, `is_sale`, `is_base`.
- `suppliers`: `id`, `code`, `name`, `contact_person`, `phone`, `address`, `is_active`.
- `member_tiers`: `id`, `code`, `name`, `priority`, `is_active`.
- `members`: `id`, `member_code`, `name`, `type` (`RETAIL`, `B2B`), `tier_id`, `credit_limit`, `phone`, `address`, `is_active`.
- `member_users`: `member_id`, `user_id` (Pivot portal B2B).

### Fase 3: Inventory Balances, Ledger, & Purchasing (PO)
- `inventory_balances`: `id`, `location_id`, `product_id`, `on_hand_qty`, `reserved_qty`, `current_unit_cost`, `updated_at`.
- `stock_movements`: `id`, `movement_number`, `location_id`, `product_id`, `delta_qty_base`, `unit_cost_snapshot`, `balance_after_qty`, `movement_type` (`IN_PO`, `IN_EMERGENCY`, `TRANSFER_DISPATCH`, `TRANSFER_RECEIVE`, `SALE_POS`, `SALE_B2B`, `OPNAME_ADJ`, `CONVERSION`), `color_code` (`GREEN`, `BLUE`, `PURPLE`, `ORANGE`, `RED`), `reference_type`, `reference_id`, `creator_id`, `created_at`.
- `purchase_orders`: `id`, `po_number`, `supplier_id`, `warehouse_id`, `order_date`, `expected_delivery_date`, `status` (`DRAFT`, `ISSUED`, `PARTIAL`, `RECEIVED`, `CLOSED`, `CANCELLED`), `notes`, `creator_id`.
- `purchase_order_items`: `id`, `po_id`, `product_id`, `uom_id`, `qty_ordered`, `qty_received`, `unit_price`, `subtotal`.
- `goods_receipts`: `id`, `receipt_number`, `po_id`, `location_id`, `received_date`, `status`, `creator_id`.
- `goods_receipt_items`: `id`, `receipt_id`, `po_item_id`, `product_id`, `qty_received_base`, `unit_cost`.

### Fase 4: Transfer, Emergency Purchase, & Pricing Rules
- `stock_transfers`: `id`, `transfer_number`, `source_location_id`, `destination_location_id`, `status` (`DRAFT`, `DISPATCHED`, `RECEIVED`, `CANCELLED`), `dispatched_by_id`, `dispatched_at`, `received_by_id`, `received_at`.
- `stock_transfer_items`: `id`, `transfer_id`, `product_id`, `qty_dispatched_base`, `qty_received_base`, `unit_cost_snapshot`.
- `emergency_purchases`: `id`, `purchase_number`, `location_id`, `merchant_name`, `product_id`, `qty_base`, `unit_price`, `total_amount`, `receipt_photo_path`, `creator_id`, `created_at`.
- `product_price_rules`: `id`, `product_id`, `location_id`, `floor_price_margin_type` (`PERCENT`, `NOMINAL`), `floor_price_margin_val`, `min_market_price`, `max_market_price`, `updated_by_id`.

### Fase 5: POS Ritel & Cash Shift
- `cash_shifts`: `id`, `shift_number`, `store_id`, `cashier_user_id`, `opened_at`, `closed_at`, `starting_cash`, `system_expected_cash`, `actual_ending_cash`, `discrepancy_amount`, `validated_by_id`.
- `sales_orders`: `id`, `invoice_number`, `store_id`, `cashier_user_id`, `member_id`, `subtotal`, `tax`, `discount`, `total_amount`, `payment_method` (`CASH`, `QRIS`, `TRANSFER`, `SPLIT`), `payment_status`, `created_at`.
- `sales_order_items`: `id`, `order_id`, `product_id`, `uom_id`, `qty`, `qty_base`, `selling_price`, `unit_cost_snapshot`, `cogs_total`, `subtotal`.
- `sales_refunds`: `id`, `refund_number`, `order_id`, `cashier_id`, `supervisor_id`, `reason`, `refund_amount`, `created_at`.

### Fase 6: B2B Orders, Credit Control, & One-Time QR Pickup
- `b2b_orders`: `id`, `order_number`, `member_id`, `creator_user_id`, `pickup_location_id`, `status` (`DRAFT`, `RESERVED`, `READY_FOR_PICKUP`, `COMPLETED`, `CANCELLED`), `payment_term` (`CASH`, `TEMPO`), `due_date`, `total_amount`.
- `b2b_order_items`: `id`, `b2b_order_id`, `product_id`, `qty_base`, `negotiated_price`, `subtotal`.
- `invoices`: `id`, `invoice_number`, `b2b_order_id`, `member_id`, `invoice_date`, `due_date`, `total_amount`, `paid_amount`, `status` (`UNPAID`, `PARTIAL`, `PAID`, `OVERDUE`).
- `invoice_payments`: `id`, `invoice_id`, `amount`, `payment_method`, `reference_number`, `recorded_by_id`, `paid_at`.
- `pickup_tickets`: `id`, `ticket_number`, `b2b_order_id`, `qr_token_hash`, `status` (`ACTIVE`, `REDEEMED`, `EXPIRED`), `location_id`, `redeemed_by_id`, `redeemed_at`.

### Fase 7: Stok Opname Mandiri
- `stock_opnames`: `id`, `opname_number`, `location_id`, `scope_type` (`ALL`, `CATEGORY`), `category_id`, `status` (`OPEN_BLIND_COUNT`, `RECONCILIATION`, `FINALIZED`, `CANCELLED`), `opened_by_id`, `finalized_by_id`, `total_loss_value`, `finalized_at`.
- `stock_opname_items`: `id`, `opname_id`, `product_id`, `system_qty_snapshot`, `physical_qty_counted`, `diff_qty`, `reason_category` (`NATURAL_SHRINK`, `DAMAGED_EXPIRED`, `LOST`, `INPUT_ERROR`), `loss_value`, `counted_by_id`, `notes`.

### Fase 8: Audit & Anomali
- `audit_logs`: `id`, `actor_id`, `role_name`, `location_id`, `action`, `entity_type`, `entity_id`, `before_state`, `after_state`, `ip_address`, `created_at`.

---

## 4. Konvensi Tipe Data & Presisi

1. **Kuantitas Barang:** `DECIMAL(18, 3)` (Mendukung timbangan beras, daging, bumbu sampai 0.001 kg atau gram tanpa distorsi floating point).
2. **Uang & Harga:** `DECIMAL(20, 2)` (IDR nilai transaksi).
3. **Biaya Pokok (HPP) & Faktor Konversi:** `DECIMAL(20, 6)` (Presisi tinggi untuk moving average dan pembagian satuan).
4. **Foreign Key Constraint:** Seluruh tabel operasional dan transaksi menggunakan `ON DELETE RESTRICT` untuk menjamin integritas riwayat. Penghapusan data master menggunakan flag `is_active = FALSE`.
