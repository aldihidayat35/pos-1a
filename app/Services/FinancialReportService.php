<?php

namespace App\Services;

use App\Models\AnomalyEvent;
use App\Models\B2bOrder;
use App\Models\EmergencyPurchase;
use App\Models\InventoryBalance;
use App\Models\Invoice;
use App\Models\Location;
use App\Models\ProductCategory;
use App\Models\Sale;
use App\Models\StockOpname;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class FinancialReportService
{
    /**
     * Calculate Executive Financial Summary KPIs for Owner & Management
     */
    public function getExecutiveSummary(?Location $location = null, ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $startDate = $startDate ? $startDate->copy()->startOfDay() : Carbon::today()->startOfDay();
        $endDate = $endDate ? $endDate->copy()->endOfDay() : Carbon::today()->endOfDay();

        // 1. Realized Retail Sales (POS)
        $posQuery = Sale::where('status', Sale::STATUS_COMPLETED ?? 'COMPLETED')
            ->whereBetween('posted_at', [$startDate, $endDate]);

        if ($location) {
            $posQuery->where('location_id', $location->id);
        }

        $posRevenue = (float) (clone $posQuery)->sum('total_amount');
        $posCogs = (float) (clone $posQuery)->sum('cogs_total');
        $posCount = (clone $posQuery)->count();

        // 2. Realized B2B Wholesale Sales (Only HANDED_OVER orders where goods left the warehouse)
        $b2bQuery = B2bOrder::where('status', B2bOrder::STATUS_HANDED_OVER)
            ->whereBetween('handed_over_at', [$startDate, $endDate]);

        if ($location) {
            $b2bQuery->where('fulfillment_location_id', $location->id);
        }

        $b2bRevenue = (float) (clone $b2bQuery)->sum('total_amount');
        $b2bCount = (clone $b2bQuery)->count();

        // Calculate B2B COGS based on order items
        $b2bOrderIds = (clone $b2bQuery)->pluck('id');
        $b2bCogs = 0.00;
        if ($b2bOrderIds->isNotEmpty()) {
            $b2bCogs = (float) DB::table('b2b_order_items')
                ->whereIn('b2b_order_id', $b2bOrderIds)
                ->sum(DB::raw('quantity_base * cogs_snapshot'));
        }

        // Total Realized Revenue & Gross Profit
        $totalRevenue = $posRevenue + $b2bRevenue;
        $totalCogs = $posCogs + $b2bCogs;
        $grossProfit = $totalRevenue - $totalCogs;
        $grossMarginPercent = $totalRevenue > 0 ? round(($grossProfit / $totalRevenue) * 100, 1) : 0.0;

        // 3. Real-Time Inventory Asset Valuation
        $invQuery = InventoryBalance::with(['product', 'location'])
            ->whereHas('product', fn($q) => $q->where('is_active', true));

        if ($location) {
            $invQuery->where('location_id', $location->id);
        }

        $inventoryBalances = $invQuery->get();
        $totalInventoryValue = 0.00;
        $warehouseInventoryValue = 0.00;
        $storeInventoryValue = 0.00;

        foreach ($inventoryBalances as $bal) {
            $val = (float) $bal->on_hand_qty * (float) $bal->avg_unit_cost;
            $totalInventoryValue += $val;
            if ($bal->location?->isWarehouse()) {
                $warehouseInventoryValue += $val;
            } elseif ($bal->location?->isStore()) {
                $storeInventoryValue += $val;
            }
        }

        // 4. Accounts Receivable & Overdue Invoices (B2B Credit Exposure)
        $invoiceQuery = Invoice::whereIn('status', [Invoice::STATUS_OPEN, Invoice::STATUS_PARTIAL, Invoice::STATUS_OVERDUE]);
        $totalReceivables = (float) (clone $invoiceQuery)->sum('outstanding_amount');

        $now = Carbon::now();
        $overdueQuery = (clone $invoiceQuery)->where('due_date', '<', $now);
        $overdueTotal = (float) (clone $overdueQuery)->sum('outstanding_amount');
        $overdueCount = (clone $overdueQuery)->count();

        // Aging breakdown
        $agingCurrent = (float) (clone $invoiceQuery)->where('due_date', '>=', $now)->sum('outstanding_amount');
        $aging1to7 = (float) (clone $invoiceQuery)->whereBetween('due_date', [$now->copy()->subDays(7), $now->copy()->subSecond()])->sum('outstanding_amount');
        $aging8to14 = (float) (clone $invoiceQuery)->whereBetween('due_date', [$now->copy()->subDays(14), $now->copy()->subDays(8)])->sum('outstanding_amount');
        $agingOver14 = (float) (clone $invoiceQuery)->where('due_date', '<', $now->copy()->subDays(14))->sum('outstanding_amount');

        // 5. Operational Discrepancies & Capital Outflow (PRD 5.9.3)
        $opnameLossQuery = StockOpname::where('status', StockOpname::STATUS_FINALIZED)
            ->whereBetween('finalized_at', [$startDate, $endDate]);
        if ($location) {
            $opnameLossQuery->where('location_id', $location->id);
        }
        $totalOpnameLoss = (float) $opnameLossQuery->sum('total_loss_value');

        $emergencyQuery = EmergencyPurchase::whereBetween('created_at', [$startDate, $endDate]);
        if ($location) {
            $emergencyQuery->where('location_id', $location->id);
        }
        $totalEmergencyPurchases = (float) $emergencyQuery->sum('total_amount');

        $voidQuery = Sale::where('status', Sale::STATUS_VOIDED ?? 'VOIDED')
            ->whereBetween('voided_at', [$startDate, $endDate]);
        if ($location) {
            $voidQuery->where('location_id', $location->id);
        }
        $totalVoidAmount = (float) (clone $voidQuery)->sum('total_amount');
        $totalVoidCount = (clone $voidQuery)->count();

        // Active Anomaly Count
        $anomalyCount = AnomalyEvent::whereBetween('occurred_at', [$startDate, $endDate])->count();
        $redAnomalyCount = AnomalyEvent::where('severity', 'RED')->whereBetween('occurred_at', [$startDate, $endDate])->count();
        $orangeAnomalyCount = AnomalyEvent::where('severity', 'ORANGE')->whereBetween('occurred_at', [$startDate, $endDate])->count();

        return [
            'period' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'is_today' => $startDate->isToday() && $endDate->isToday(),
            ],
            'revenue' => [
                'total' => $totalRevenue,
                'pos' => $posRevenue,
                'pos_count' => $posCount,
                'b2b' => $b2bRevenue,
                'b2b_count' => $b2bCount,
            ],
            'cogs' => [
                'total' => $totalCogs,
                'pos' => $posCogs,
                'b2b' => $b2bCogs,
            ],
            'profit' => [
                'gross_profit' => $grossProfit,
                'margin_percent' => $grossMarginPercent,
            ],
            'inventory' => [
                'total_value' => $totalInventoryValue,
                'warehouse_value' => $warehouseInventoryValue,
                'store_value' => $storeInventoryValue,
                'warehouse_percent' => $totalInventoryValue > 0 ? round(($warehouseInventoryValue / $totalInventoryValue) * 100, 1) : 0,
                'store_percent' => $totalInventoryValue > 0 ? round(($storeInventoryValue / $totalInventoryValue) * 100, 1) : 0,
            ],
            'receivables' => [
                'total' => $totalReceivables,
                'overdue_total' => $overdueTotal,
                'overdue_count' => $overdueCount,
                'aging' => [
                    'current' => $agingCurrent,
                    '1_to_7' => $aging1to7,
                    '8_to_14' => $aging8to14,
                    'over_14' => $agingOver14,
                ],
            ],
            'abnormal_outflows' => [
                'opname_loss' => $totalOpnameLoss,
                'emergency_purchases' => $totalEmergencyPurchases,
                'void_amount' => $totalVoidAmount,
                'void_count' => $totalVoidCount,
                'total_anomalies' => $anomalyCount,
                'red_anomalies' => $redAnomalyCount,
                'orange_anomalies' => $orangeAnomalyCount,
            ],
        ];
    }

    /**
     * Get realized sales drilldown collection combining POS and B2B orders
     */
    public function getDetailedSales(?Location $location = null, ?Carbon $startDate = null, ?Carbon $endDate = null): Collection
    {
        $startDate = $startDate ? $startDate->copy()->startOfDay() : Carbon::today()->startOfDay();
        $endDate = $endDate ? $endDate->copy()->endOfDay() : Carbon::today()->endOfDay();

        // 1. POS Completed Sales
        $posQuery = Sale::with(['location', 'cashier'])
            ->where('status', 'COMPLETED')
            ->whereBetween('posted_at', [$startDate, $endDate]);

        if ($location) {
            $posQuery->where('location_id', $location->id);
        }

        $posSales = $posQuery->orderByDesc('posted_at')->get()->map(function ($sale) {
            $revenue = (float) $sale->total_amount;
            $cogs = (float) $sale->cogs_total;
            $profit = $revenue - $cogs;
            return [
                'type' => 'POS',
                'type_label' => 'Ritel POS Toko',
                'reference_number' => $sale->sale_number,
                'date' => $sale->posted_at,
                'location_name' => $sale->location?->name ?? 'Toko',
                'customer' => $sale->customer_name ?: 'Pelanggan Umum',
                'payment_method' => $sale->payment_method,
                'revenue' => $revenue,
                'cogs' => $cogs,
                'gross_profit' => $profit,
                'margin_percent' => $revenue > 0 ? round(($profit / $revenue) * 100, 1) : 0.0,
                'actor' => $sale->cashier?->name ?? 'Kasir',
            ];
        });

        // 2. B2B Handed Over Orders
        $b2bQuery = B2bOrder::with(['fulfillmentLocation', 'member', 'creator', 'items'])
            ->where('status', B2bOrder::STATUS_HANDED_OVER)
            ->whereBetween('handed_over_at', [$startDate, $endDate]);

        if ($location) {
            $b2bQuery->where('fulfillment_location_id', $location->id);
        }

        $b2bSales = $b2bQuery->orderByDesc('handed_over_at')->get()->map(function ($order) {
            $revenue = (float) $order->total_amount;
            $cogs = (float) $order->items->sum(fn($i) => (float) $i->quantity_base * (float) $i->cogs_snapshot);
            $profit = $revenue - $cogs;
            return [
                'type' => 'B2B',
                'type_label' => 'Grosir B2B Tempo/Cash',
                'reference_number' => $order->order_number,
                'date' => $order->handed_over_at,
                'location_name' => $order->fulfillmentLocation?->name ?? 'Gudang',
                'customer' => $order->member?->name ?? 'Mitra B2B',
                'payment_method' => $order->payment_term,
                'revenue' => $revenue,
                'cogs' => $cogs,
                'gross_profit' => $profit,
                'margin_percent' => $revenue > 0 ? round(($profit / $revenue) * 100, 1) : 0.0,
                'actor' => $order->creator?->name ?? 'Sales',
            ];
        });

        return $posSales->concat($b2bSales)->sortByDesc('date')->values();
    }

    /**
     * Get inventory valuation breakdown per product and location
     */
    public function getInventoryValuationReport(?Location $location = null): Collection
    {
        $query = InventoryBalance::with(['product.category', 'product.baseUom', 'location'])
            ->whereHas('product', fn($q) => $q->where('is_active', true));

        if ($location) {
            $query->where('location_id', $location->id);
        }

        return $query->orderBy('location_id')
            ->orderBy('product_id')
            ->get()
            ->map(function ($bal) {
                $onHand = (float) $bal->on_hand_qty;
                $reserved = (float) $bal->reserved_qty;
                $available = max(0.00, $onHand - $reserved);
                $avgCost = (float) $bal->avg_unit_cost;
                $totalVal = round($onHand * $avgCost, 2);

                return [
                    'location_name' => $bal->location?->name ?? '-',
                    'location_type' => $bal->location?->type ?? '-',
                    'product_sku' => $bal->product?->sku ?? '-',
                    'product_name' => $bal->product?->name ?? '-',
                    'category_name' => $bal->product?->category?->name ?? 'Umum',
                    'uom' => $bal->product?->baseUom?->code ?? 'PCS',
                    'on_hand_qty' => $onHand,
                    'reserved_qty' => $reserved,
                    'available_qty' => $available,
                    'avg_unit_cost' => $avgCost,
                    'total_asset_value' => $totalVal,
                ];
            });
    }

    /**
     * Get receivables aging report
     */
    public function getReceivablesAgingReport(): Collection
    {
        $now = Carbon::now();

        return Invoice::with(['member.tier', 'b2bOrder'])
            ->whereIn('status', [Invoice::STATUS_OPEN, Invoice::STATUS_PARTIAL, Invoice::STATUS_OVERDUE])
            ->orderBy('due_date', 'asc')
            ->get()
            ->map(function ($inv) use ($now) {
                $dueDate = Carbon::parse($inv->due_date);
                $isOverdue = $dueDate->isPast();
                $overdueDays = $isOverdue ? $dueDate->diffInDays($now) : 0;

                $bucket = 'CURRENT';
                if ($isOverdue) {
                    if ($overdueDays <= 7) {
                        $bucket = '1_TO_7';
                    } elseif ($overdueDays <= 14) {
                        $bucket = '8_TO_14';
                    } else {
                        $bucket = 'OVER_14';
                    }
                }

                return [
                    'invoice_number' => $inv->invoice_number,
                    'order_number' => $inv->b2bOrder?->order_number ?? '-',
                    'member_code' => $inv->member?->member_code ?? '-',
                    'member_name' => $inv->member?->name ?? '-',
                    'tier' => $inv->member?->tier?->name ?? 'Retail',
                    'phone' => $inv->member?->phone ?? '-',
                    'credit_limit' => (float) ($inv->member?->credit_limit ?? 0.00),
                    'invoice_date' => $inv->invoice_date,
                    'due_date' => $inv->due_date,
                    'total_amount' => (float) $inv->total_amount,
                    'paid_amount' => (float) $inv->paid_amount,
                    'outstanding_amount' => (float) $inv->outstanding_amount,
                    'is_overdue' => $isOverdue,
                    'overdue_days' => $overdueDays,
                    'aging_bucket' => $bucket,
                ];
            });
    }

    /**
     * Export Financial Summary CSV
     */
    public function exportFinancialCsv(?Location $location = null, ?Carbon $startDate = null, ?Carbon $endDate = null): string
    {
        $summary = $this->getExecutiveSummary($location, $startDate, $endDate);

        $out = fopen('php://temp', 'r+');
        // UTF-8 BOM for Excel
        fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));

        fputcsv($out, ['LAPORAN KESEHATAN FINANSIAL & EKSEKUTIF PASARHUB']);
        fputcsv($out, ['Periode', $summary['period']['start_date'] . ' s/d ' . $summary['period']['end_date']]);
        fputcsv($out, ['Lokasi', $location ? $location->name : 'Semua Lokasi Terpusat']);
        fputcsv($out, []);

        fputcsv($out, ['INDIKATOR METRIK', 'NILAI (IDR)', 'KETERANGAN']);
        fputcsv($out, ['Omzet Penjualan Ritel (POS)', number_format($summary['revenue']['pos'], 2, '.', ''), "{$summary['revenue']['pos_count']} Transaksi Struk"]);
        fputcsv($out, ['Omzet Grosir Kemitraan (B2B)', number_format($summary['revenue']['b2b'], 2, '.', ''), "{$summary['revenue']['b2b_count']} Pesanan Handed Over"]);
        fputcsv($out, ['TOTAL OMZET TEREALISASI', number_format($summary['revenue']['total'], 2, '.', ''), 'Pendapatan Bersih Barang Diserahkan']);
        fputcsv($out, ['Beban Pokok Pendapatan (COGS)', number_format($summary['cogs']['total'], 2, '.', ''), 'Moving Average HPP']);
        fputcsv($out, ['ESTIMASI LABA KOTOR', number_format($summary['profit']['gross_profit'], 2, '.', ''), "Margin {$summary['profit']['margin_percent']}%"]);
        fputcsv($out, []);

        fputcsv($out, ['VALUASI ASET & PIUTANG', 'NILAI (IDR)', 'KETERANGAN']);
        fputcsv($out, ['Nilai Aset Stok Gudang', number_format($summary['inventory']['warehouse_value'], 2, '.', ''), "{$summary['inventory']['warehouse_percent']}% dari total stok"]);
        fputcsv($out, ['Nilai Aset Stok Toko', number_format($summary['inventory']['store_value'], 2, '.', ''), "{$summary['inventory']['store_percent']}% dari total stok"]);
        fputcsv($out, ['TOTAL ASET PERSEDIAAN', number_format($summary['inventory']['total_value'], 2, '.', ''), 'Berdasarkan on-hand fisik riil']);
        fputcsv($out, ['Total Piutang Berjalan B2B', number_format($summary['receivables']['total'], 2, '.', ''), 'Faktur Open/Partial']);
        fputcsv($out, ['Piutang Jatuh Tempo (Overdue)', number_format($summary['receivables']['overdue_total'], 2, '.', ''), "{$summary['receivables']['overdue_count']} Faktur Menunggak"]);
        fputcsv($out, []);

        fputcsv($out, ['PENGELUARAN ABNORMAL & SUSUT', 'NILAI (IDR)', 'KETERANGAN']);
        fputcsv($out, ['Susut & Selisih Kurang Opname (Merah)', number_format($summary['abnormal_outflows']['opname_loss'], 2, '.', ''), 'Kerugian Fisik Final']);
        fputcsv($out, ['Belanja Stok Darurat (Oranye)', number_format($summary['abnormal_outflows']['emergency_purchases'], 2, '.', ''), 'Pembelian Toko Non-PO']);
        fputcsv($out, ['Pembatalan Transaksi / Void Struk', number_format($summary['abnormal_outflows']['void_amount'], 2, '.', ''), "{$summary['abnormal_outflows']['void_count']} Transaksi Void"]);

        rewind($out);
        $csv = stream_get_contents($out);
        fclose($out);

        return $csv;
    }

    /**
     * Export Realized Sales Drilldown CSV
     */
    public function exportSalesCsv(?Location $location = null, ?Carbon $startDate = null, ?Carbon $endDate = null): string
    {
        $sales = $this->getDetailedSales($location, $startDate, $endDate);

        $out = fopen('php://temp', 'r+');
        fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));

        fputcsv($out, [
            'No',
            'Jenis Transaksi',
            'No. Referensi / Struk',
            'Waktu Transaksi',
            'Lokasi',
            'Pelanggan / Mitra',
            'Metode / Termin',
            'Omzet Penjualan (IDR)',
            'HPP Pokok (COGS IDR)',
            'Laba Kotor (IDR)',
            'Margin (%)',
            'Petugas / Kasir',
        ]);

        foreach ($sales as $idx => $s) {
            fputcsv($out, [
                $idx + 1,
                $s['type_label'],
                $s['reference_number'],
                $s['date'] ? Carbon::parse($s['date'])->format('Y-m-d H:i:s') : '-',
                $s['location_name'],
                $s['customer'],
                $s['payment_method'],
                number_format($s['revenue'], 2, '.', ''),
                number_format($s['cogs'], 2, '.', ''),
                number_format($s['gross_profit'], 2, '.', ''),
                $s['margin_percent'] . '%',
                $s['actor'],
            ]);
        }

        rewind($out);
        $csv = stream_get_contents($out);
        fclose($out);

        return $csv;
    }

    /**
     * Export Inventory Valuation CSV
     */
    public function exportInventoryCsv(?Location $location = null): string
    {
        $items = $this->getInventoryValuationReport($location);

        $out = fopen('php://temp', 'r+');
        fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));

        fputcsv($out, [
            'No',
            'Lokasi',
            'Tipe Lokasi',
            'SKU',
            'Nama Produk',
            'Kategori',
            'Satuan',
            'Stok Fisik (On-Hand)',
            'Stok Teralokasi (Reserved)',
            'Stok Bebas (Available)',
            'Rata-rata HPP (IDR)',
            'Total Nilai Aset (IDR)',
        ]);

        foreach ($items as $idx => $it) {
            fputcsv($out, [
                $idx + 1,
                $it['location_name'],
                $it['location_type'],
                $it['product_sku'],
                $it['product_name'],
                $it['category_name'],
                $it['uom'],
                number_format($it['on_hand_qty'], 3, '.', ''),
                number_format($it['reserved_qty'], 3, '.', ''),
                number_format($it['available_qty'], 3, '.', ''),
                number_format($it['avg_unit_cost'], 2, '.', ''),
                number_format($it['total_asset_value'], 2, '.', ''),
            ]);
        }

        rewind($out);
        $csv = stream_get_contents($out);
        fclose($out);

        return $csv;
    }
}
