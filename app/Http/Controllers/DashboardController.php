<?php

namespace App\Http\Controllers;

use App\Models\B2bOrder;
use App\Models\CashShift;
use App\Models\EmergencyPurchase;
use App\Models\InventoryBalance;
use App\Models\Invoice;
use App\Models\Location;
use App\Models\Member;
use App\Models\PickupTicket;
use App\Models\PurchaseOrder;
use App\Models\Sale;
use App\Models\StockMovement;
use App\Models\StockOpname;
use App\Models\StockOpnameItem;
use App\Models\StockOperation;
use App\Models\StockTransfer;
use App\Models\User;
use App\Services\FinancialReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected FinancialReportService $reportService
    ) {}

    /**
     * Display the contextual role-based dashboard for 7 dedicated profiles
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $activeLocation = $user->getActiveLocation();
        $primaryRole = $user->primaryRole();
        $roleName = $primaryRole?->name ?? 'unknown';

        // Determine 1 of 7 specific dashboard profiles
        $dashboardProfile = match (true) {
            in_array($roleName, ['super_admin', 'owner']) => 'executive',
            $roleName === 'kepala_toko' => 'store_head',
            $roleName === 'staf_toko' => 'store_staff',
            $roleName === 'kepala_gudang' => 'warehouse_head',
            $roleName === 'staf_gudang' => 'warehouse_staff',
            $roleName === 'sales' => 'sales',
            $roleName === 'b2b_mitra' => 'b2b_partner',
            default => 'executive',
        };

        // Baseline environment stats
        $stats = [
            'total_users' => User::where('is_active', true)->count(),
            'total_locations' => Location::active()->count(),
            'assigned_locations_count' => $user->isSuperAdmin() || $user->isOwner()
                ? Location::active()->where('type', '!=', Location::TYPE_TRANSIT)->count()
                : $user->locations()->where('is_active', true)->count(),
            'user_role_label' => $primaryRole?->label ?? 'User',
            'user_role_code' => $roleName,
            'active_location_name' => $activeLocation?->name ?? 'Semua Lokasi Terpusat',
            'active_location_code' => $activeLocation?->code ?? 'ALL',
            'active_location_type' => $activeLocation?->type ?? 'CENTRAL',
        ];

        // 1. Executive Profile Financials
        $financialSummary = null;
        if ($dashboardProfile === 'executive' || $user->hasPermission('dashboard.financial')) {
            $financialSummary = $this->reportService->getExecutiveSummary($activeLocation, Carbon::today(), Carbon::today());
        }

        // 2. Profile-Specific Operational Data Payload
        $profileData = [];

        // Helper: 7-day date range for Chart.js
        $last7Days = collect(range(6, 0))->map(fn($d) => Carbon::today()->subDays($d));
        $chartLabels = $last7Days->map(fn($d) => $d->format('D, d M'))->toArray();

        if ($dashboardProfile === 'executive') {
            // 7-day revenue and profit trend for Chart.js
            $chartRevenue = $last7Days->map(function ($d) {
                $pos = Sale::where('status', Sale::STATUS_COMPLETED)->whereDate('posted_at', $d)->sum('total_amount');
                $b2b = B2bOrder::where('status', B2bOrder::STATUS_HANDED_OVER)->whereDate('handed_over_at', $d)->sum('total_amount');
                return (float) ($pos + $b2b);
            })->toArray();

            $chartProfit = $last7Days->map(function ($d) {
                $pos = Sale::where('status', Sale::STATUS_COMPLETED)->whereDate('posted_at', $d)->sum('total_amount');
                return (float) ($pos * 0.18); // ~18% estimated gross profit
            })->toArray();

            $recentMovements = StockMovement::with(['product', 'operation.actor', 'location'])
                ->latest('occurred_at')
                ->take(5)
                ->get();

            $profileData = [
                'chart_labels' => $chartLabels,
                'chart_revenue' => $chartRevenue,
                'chart_profit' => $chartProfit,
                'recent_movements' => $recentMovements,
            ];
        } elseif ($dashboardProfile === 'store_head') {
            $storeLocId = $activeLocation?->id;

            // Today's retail sales
            $todaySalesQuery = Sale::where('location_id', $storeLocId)
                ->where('status', Sale::STATUS_COMPLETED)
                ->whereDate('posted_at', Carbon::today());
            $todaySalesTotal = (float) $todaySalesQuery->sum('total_amount');
            $todaySalesCount = (int) $todaySalesQuery->count();

            // Active cashiers on duty
            $activeCashiersCount = CashShift::where('location_id', $storeLocId)
                ->where('status', CashShift::STATUS_OPEN)
                ->count();

            // Emergency purchases this month
            $emergencyMonthTotal = (float) EmergencyPurchase::where('location_id', $storeLocId)
                ->whereMonth('created_at', Carbon::today()->month)
                ->whereYear('created_at', Carbon::today()->year)
                ->sum('total_amount');

            // Inbound stock transfers in transit
            $inboundTransfersCount = StockTransfer::where('to_location_id', $storeLocId)
                ->where('status', 'DISPATCHED')
                ->count();

            // Low stock SKU count at this store
            $lowStockCount = InventoryBalance::where('location_id', $storeLocId)
                ->where('on_hand_qty', '<=', 10)
                ->count();

            // 7-day sales trend for Store Head Chart.js
            $chartStoreSales = $last7Days->map(function ($d) use ($storeLocId) {
                return (float) Sale::where('location_id', $storeLocId)
                    ->where('status', Sale::STATUS_COMPLETED)
                    ->whereDate('posted_at', $d)
                    ->sum('total_amount');
            })->toArray();

            // Recent 5 sales
            $recentSales = Sale::where('location_id', $storeLocId)
                ->with('cashier')
                ->latest('posted_at')
                ->take(5)
                ->get();

            $profileData = [
                'today_sales_total' => $todaySalesTotal,
                'today_sales_count' => $todaySalesCount,
                'active_cashiers_count' => $activeCashiersCount,
                'emergency_month_total' => $emergencyMonthTotal,
                'inbound_transfers_count' => $inboundTransfersCount,
                'low_stock_count' => $lowStockCount,
                'chart_labels' => $chartLabels,
                'chart_store_sales' => $chartStoreSales,
                'recent_sales' => $recentSales,
            ];
        } elseif ($dashboardProfile === 'store_staff') {
            $storeLocId = $activeLocation?->id;

            // Active shift for this cashier
            $activeShift = CashShift::where('cashier_user_id', $user->id)
                ->where('status', CashShift::STATUS_OPEN)
                ->latest('opened_at')
                ->first();

            // Attendance: Active shift days attended this month
            $shiftsAttendedCount = CashShift::where('cashier_user_id', $user->id)
                ->whereMonth('opened_at', Carbon::today()->month)
                ->whereYear('opened_at', Carbon::today()->year)
                ->count();

            $targetWorkDays = 24;
            $attendanceProgress = min(100, (int) round(($shiftsAttendedCount / $targetWorkDays) * 100));

            // Monthly sales handled by this cashier
            $monthlySalesQuery = Sale::where('cashier_user_id', $user->id)
                ->where('status', Sale::STATUS_COMPLETED)
                ->whereMonth('posted_at', Carbon::today()->month)
                ->whereYear('posted_at', Carbon::today()->year);

            $monthlySalesTotal = (float) $monthlySalesQuery->sum('total_amount');
            $monthlySalesCount = (int) $monthlySalesQuery->count();

            // Cashier Monthly Sales Target (e.g., Rp 20.000.000)
            $targetSales = 20000000;
            $salesProgress = min(100, (int) round(($monthlySalesTotal / $targetSales) * 100));

            $avgBasketSize = $monthlySalesCount > 0 ? (int) round($monthlySalesTotal / $monthlySalesCount) : 0;

            // Recent 5 sales completed by this cashier
            $recentSales = Sale::where('cashier_user_id', $user->id)
                ->latest('posted_at')
                ->take(5)
                ->get();

            $profileData = [
                'active_shift' => $activeShift,
                'shifts_attended_count' => $shiftsAttendedCount,
                'target_work_days' => $targetWorkDays,
                'attendance_progress' => $attendanceProgress,
                'monthly_sales_total' => $monthlySalesTotal,
                'monthly_sales_count' => $monthlySalesCount,
                'target_sales' => $targetSales,
                'sales_progress' => $salesProgress,
                'avg_basket_size' => $avgBasketSize,
                'recent_sales' => $recentSales,
            ];
        } elseif ($dashboardProfile === 'warehouse_head') {
            $whLocId = $activeLocation?->id;

            $pendingPosCount = PurchaseOrder::whereIn('status', ['SUBMITTED', 'PARTIALLY_RECEIVED'])->count();
            $draftTransfersCount = StockTransfer::where('from_location_id', $whLocId)
                ->where('status', 'DRAFT')
                ->count();
            $lowStockCount = InventoryBalance::where('location_id', $whLocId)
                ->where('on_hand_qty', '<=', 10)
                ->count();
            $activeOpname = StockOpname::where('location_id', $whLocId)
                ->whereIn('status', [StockOpname::STATUS_OPEN, StockOpname::STATUS_COUNTING, StockOpname::STATUS_RECONCILIATION])
                ->first();

            // 7-day logistics throughput for Chart.js (Inbound PO qty vs Outbound Transfer qty)
            $chartInboundQty = $last7Days->map(function ($d) use ($whLocId) {
                return (int) StockMovement::where('location_id', $whLocId)
                    ->where('movement_type', 'PO_RECEIPT')
                    ->whereDate('occurred_at', $d)
                    ->sum('delta_qty_base');
            })->toArray();

            $chartOutboundQty = $last7Days->map(function ($d) use ($whLocId) {
                return (int) abs(StockMovement::where('location_id', $whLocId)
                    ->where('movement_type', 'TRANSFER_OUT')
                    ->whereDate('occurred_at', $d)
                    ->sum('delta_qty_base'));
            })->toArray();

            $recentMovements = StockMovement::where('location_id', $whLocId)
                ->with(['product', 'operation.actor'])
                ->latest('occurred_at')
                ->take(5)
                ->get();

            $profileData = [
                'pending_pos_count' => $pendingPosCount,
                'draft_transfers_count' => $draftTransfersCount,
                'low_stock_count' => $lowStockCount,
                'active_opname' => $activeOpname,
                'chart_labels' => $chartLabels,
                'chart_inbound_qty' => $chartInboundQty,
                'chart_outbound_qty' => $chartOutboundQty,
                'recent_movements' => $recentMovements,
            ];
        } elseif ($dashboardProfile === 'warehouse_staff') {
            $whLocId = $activeLocation?->id;

            // Tasks executed by this warehouse operator this month
            $monthOps = StockOperation::where('actor_user_id', $user->id)
                ->whereMonth('posted_at', Carbon::today()->month)
                ->whereYear('posted_at', Carbon::today()->year);

            $poReceivedCount = (clone $monthOps)->where('operation_type', 'LIKE', '%PO%')->count();
            $transfersDispatchedCount = (clone $monthOps)->where('operation_type', 'LIKE', '%TRANSFER%')->count();
            $totalOpsCount = (clone $monthOps)->count();

            // Blind count opname items counted by this operator
            $blindCountItemsCount = StockOpnameItem::where('counter_user_id', $user->id)
                ->whereMonth('created_at', Carbon::today()->month)
                ->count();

            $targetOps = 40;
            $opsProgress = min(100, (int) round(($totalOpsCount / $targetOps) * 100));

            $recentMovements = StockMovement::where('location_id', $whLocId)
                ->whereHas('operation', fn($q) => $q->where('actor_user_id', $user->id))
                ->with(['product', 'operation'])
                ->latest('occurred_at')
                ->take(5)
                ->get();

            $profileData = [
                'po_received_count' => $poReceivedCount,
                'transfers_dispatched_count' => $transfersDispatchedCount,
                'blind_count_items_count' => $blindCountItemsCount,
                'total_ops_count' => $totalOpsCount,
                'target_ops' => $targetOps,
                'ops_progress' => $opsProgress,
                'recent_movements' => $recentMovements,
            ];
        } elseif ($dashboardProfile === 'sales') {
            // Sales individual performance
            $salesOrdersQuery = B2bOrder::where('creator_user_id', $user->id)
                ->whereMonth('created_at', Carbon::today()->month)
                ->whereYear('created_at', Carbon::today()->year);

            $monthlySalesOmzet = (float) $salesOrdersQuery->sum('total_amount');
            $monthlyOrdersCount = (int) $salesOrdersQuery->count();

            // Sales monthly omzet target (e.g. Rp 50.000.000)
            $targetSalesOmzet = 50000000;
            $omzetProgress = min(100, (int) round(($monthlySalesOmzet / $targetSalesOmzet) * 100));

            $totalPartnersCount = Member::where('type', Member::TYPE_B2B)
                ->where('is_active', true)
                ->count();

            $recentOrders = B2bOrder::where('creator_user_id', $user->id)
                ->with(['member', 'pickupTicket'])
                ->latest('created_at')
                ->take(5)
                ->get();

            $profileData = [
                'monthly_sales_omzet' => $monthlySalesOmzet,
                'monthly_orders_count' => $monthlyOrdersCount,
                'target_sales_omzet' => $targetSalesOmzet,
                'omzet_progress' => $omzetProgress,
                'total_partners_count' => $totalPartnersCount,
                'recent_orders' => $recentOrders,
            ];
        } elseif ($dashboardProfile === 'b2b_partner') {
            // Customer Portal for B2B Partner
            $member = Member::where('email', $user->email)->first()
                ?? Member::where('type', Member::TYPE_B2B)->first();

            $creditLimit = (float) ($member?->credit_limit ?? 50000000);
            
            // Used credit from open/overdue invoices
            $usedCredit = $member ? (float) Invoice::where('member_id', $member->id)
                ->whereIn('status', [Invoice::STATUS_OPEN, Invoice::STATUS_PARTIALLY_PAID, Invoice::STATUS_OVERDUE])
                ->sum('outstanding_amount') : 0;

            $remainingCredit = max(0, $creditLimit - $usedCredit);

            // Active Pickup Tickets ready for pickup
            $activeTickets = $member ? PickupTicket::whereHas('order', fn($q) => $q->where('member_id', $member->id))
                ->where('status', PickupTicket::STATUS_ISSUED)
                ->with('order')
                ->get() : collect();

            $monthlySpend = $member ? (float) B2bOrder::where('member_id', $member->id)
                ->whereMonth('created_at', Carbon::today()->month)
                ->whereYear('created_at', Carbon::today()->year)
                ->sum('total_amount') : 0;

            $recentOrders = $member ? B2bOrder::where('member_id', $member->id)
                ->with(['pickupTicket', 'fulfillmentLocation'])
                ->latest('created_at')
                ->take(5)
                ->get() : collect();

            $profileData = [
                'member' => $member,
                'credit_limit' => $creditLimit,
                'used_credit' => $usedCredit,
                'remaining_credit' => $remainingCredit,
                'active_tickets' => $activeTickets,
                'monthly_spend' => $monthlySpend,
                'recent_orders' => $recentOrders,
            ];
        }

        return view('dashboard.index', compact(
            'user',
            'stats',
            'activeLocation',
            'primaryRole',
            'financialSummary',
            'dashboardProfile',
            'profileData'
        ));
    }
}
