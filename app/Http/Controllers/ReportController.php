<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Services\FinancialReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function __construct(
        protected FinancialReportService $reportService
    ) {}

    /**
     * Resolve date range filter from request preset or custom inputs
     */
    protected function resolveDateRange(Request $request): array
    {
        $preset = $request->input('preset', 'today');

        switch ($preset) {
            case 'this_week':
                $start = Carbon::now()->startOfWeek();
                $end = Carbon::now()->endOfWeek();
                break;
            case 'this_month':
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth();
                break;
            case 'last_month':
                $start = Carbon::now()->subMonth()->startOfMonth();
                $end = Carbon::now()->subMonth()->endOfMonth();
                break;
            case 'custom':
                $start = $request->filled('start_date') ? Carbon::parse($request->start_date)->startOfDay() : Carbon::today()->startOfDay();
                $end = $request->filled('end_date') ? Carbon::parse($request->end_date)->endOfDay() : Carbon::today()->endOfDay();
                break;
            case 'today':
            default:
                $preset = 'today';
                $start = Carbon::today()->startOfDay();
                $end = Carbon::today()->endOfDay();
                break;
        }

        return [$start, $end, $preset];
    }

    /**
     * Executive Financial & Management KPI Dashboard (Owner Mode)
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        if (!$user->hasPermission('dashboard.financial') && !$user->hasPermission('reports.export') && !$user->isOwner() && !$user->isSuperAdmin()) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk melihat Laporan Finansial Eksekutif.');
        }

        [$startDate, $endDate, $preset] = $this->resolveDateRange($request);

        $locationId = $request->filled('location_id') ? $request->location_id : null;
        $location = $locationId ? Location::find($locationId) : null;

        $summary = $this->reportService->getExecutiveSummary($location, $startDate, $endDate);
        $locations = Location::where('is_active', true)->whereIn('type', ['WAREHOUSE', 'STORE'])->get();

        return view('reports.index', compact('user', 'summary', 'location', 'locations', 'preset', 'startDate', 'endDate'));
    }

    /**
     * Realized Sales & Gross Profit Margin Drilldown (POS & B2B)
     */
    public function sales(Request $request): View
    {
        $user = $request->user();
        if (!$user->hasPermission('dashboard.financial') && !$user->isOwner() && !$user->isSuperAdmin()) {
            abort(403, 'Akses Ditolak.');
        }

        [$startDate, $endDate, $preset] = $this->resolveDateRange($request);
        $location = $request->filled('location_id') ? Location::find($request->location_id) : null;

        $sales = $this->reportService->getDetailedSales($location, $startDate, $endDate);
        $locations = Location::where('is_active', true)->whereIn('type', ['WAREHOUSE', 'STORE'])->get();

        return view('reports.sales', compact('user', 'sales', 'location', 'locations', 'preset', 'startDate', 'endDate'));
    }

    /**
     * Inventory Valuation Report
     */
    public function inventory(Request $request): View
    {
        $user = $request->user();
        if (!$user->hasPermission('inventory.hpp_view') && !$user->hasPermission('inventory.view') && !$user->isOwner() && !$user->isSuperAdmin()) {
            abort(403, 'Akses Ditolak.');
        }

        $location = $request->filled('location_id') ? Location::find($request->location_id) : null;
        $items = $this->reportService->getInventoryValuationReport($location);
        $locations = Location::where('is_active', true)->whereIn('type', ['WAREHOUSE', 'STORE'])->get();

        $totalAssetValue = $items->sum('total_asset_value');
        $totalItemsCount = $items->count();

        return view('reports.inventory', compact('user', 'items', 'location', 'locations', 'totalAssetValue', 'totalItemsCount'));
    }

    /**
     * Receivables Aging Report (B2B Invoices & Overdue)
     */
    public function receivables(Request $request): View
    {
        $user = $request->user();
        if (!$user->hasPermission('dashboard.financial') && !$user->isOwner() && !$user->isSuperAdmin()) {
            abort(403, 'Akses Ditolak.');
        }

        $receivables = $this->reportService->getReceivablesAgingReport();

        $totalOutstanding = $receivables->sum('outstanding_amount');
        $overdueCount = $receivables->where('is_overdue', true)->count();
        $overdueAmount = $receivables->where('is_overdue', true)->sum('outstanding_amount');

        return view('reports.receivables', compact('user', 'receivables', 'totalOutstanding', 'overdueCount', 'overdueAmount'));
    }

    /**
     * Download Financial Summary CSV
     */
    public function exportFinancial(Request $request): StreamedResponse
    {
        $user = $request->user();
        if (!$user->hasPermission('reports.export') && !$user->isOwner() && !$user->isSuperAdmin()) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin ekspor laporan.');
        }

        [$startDate, $endDate] = $this->resolveDateRange($request);
        $location = $request->filled('location_id') ? Location::find($request->location_id) : null;

        $csv = $this->reportService->exportFinancialCsv($location, $startDate, $endDate);
        $filename = 'Laporan_Finansial_Eksekutif_' . date('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($csv) {
            echo $csv;
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Download Realized Sales Drilldown CSV
     */
    public function exportSales(Request $request): StreamedResponse
    {
        $user = $request->user();
        if (!$user->hasPermission('reports.export') && !$user->isOwner() && !$user->isSuperAdmin()) {
            abort(403, 'Akses Ditolak.');
        }

        [$startDate, $endDate] = $this->resolveDateRange($request);
        $location = $request->filled('location_id') ? Location::find($request->location_id) : null;

        $csv = $this->reportService->exportSalesCsv($location, $startDate, $endDate);
        $filename = 'Laporan_Penjualan_Omzet_' . date('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($csv) {
            echo $csv;
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Download Inventory Valuation CSV
     */
    public function exportInventory(Request $request): StreamedResponse
    {
        $user = $request->user();
        if (!$user->hasPermission('reports.export') && !$user->isOwner() && !$user->isSuperAdmin()) {
            abort(403, 'Akses Ditolak.');
        }

        $location = $request->filled('location_id') ? Location::find($request->location_id) : null;

        $csv = $this->reportService->exportInventoryCsv($location);
        $filename = 'Laporan_Valuasi_Stok_' . date('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($csv) {
            echo $csv;
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
