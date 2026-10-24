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
     * Display the contextual role-based dashboard
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $activeLocation = $user->getActiveLocation();
        $primaryRole = $user->primaryRole();
        $roleName = $primaryRole?->name ?? 'unknown';

        // Determine 1 of 4 focused dashboard profiles
        $dashboardProfile = match (true) {
            in_array($roleName, ['super_admin', 'owner']) => 'executive',
            in_array($roleName, ['kepala_toko', 'staf_toko']) => 'store',
            in_array($roleName, ['kepala_gudang', 'staf_gudang']) => 'warehouse',
            in_array($roleName, ['sales', 'b2b_mitra']) => 'b2b',
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

        if ($dashboardProfile === 'store') {
            $storeLocId = $activeLocation?->id;
            
            // Active shift for cashier or store
            $activeShift = CashShift::where('location_id', $storeLocId)
                ->where('status', CashShift::STATUS_OPEN)
                ->where(function ($q) use ($user, $roleName) {
                    if ($roleName === 'staf_toko') {
                        $q->where('cashier_user_id', $user->id);
                    }
                })
                ->latest('opened_at')
                ->first();

            // Today's retail sales
            $todaySalesQuery = Sale::where('location_id', $storeLocId)
                ->where('status', Sale::STATUS_COMPLETED)
                ->whereDate('posted_at', Carbon::today());

            $todaySalesTotal = (float) $todaySalesQuery->sum('total_amount');
            $todaySalesCount = (int) $todaySalesQuery->count();

            // Emergency purchases this month
            $emergencySpendQuery = EmergencyPurchase::where('location_id', $storeLocId)
                ->whereMonth('created_at', Carbon::today()->month)
                ->whereYear('created_at', Carbon::today()->year);

            $emergencyTotalSpent = (float) $emergencySpendQuery->sum('total_amount');
            $emergencyCount = (int) $emergencySpendQuery->count();

            // Inbound stock transfers in transit
            $inboundTransfersCount = StockTransfer::where('to_location_id', $storeLocId)
                ->where('status', 'DISPATCHED')
                ->count();

            // Recent 5 sales
            $recentSales = Sale::where('location_id', $storeLocId)
                ->with('cashier')
                ->latest('posted_at')
                ->take(5)
                ->get();

            $profileData = [
                'active_shift' => $activeShift,
                'today_sales_total' => $todaySalesTotal,
                'today_sales_count' => $todaySalesCount,
                'emergency_total_spent' => $emergencyTotalSpent,
                'emergency_count' => $emergencyCount,
                'inbound_transfers_count' => $inboundTransfersCount,
                'recent_sales' => $recentSales,
            ];
        } elseif ($dashboardProfile === 'warehouse') {
            $whLocId = $activeLocation?->id;

            // Pending PO receipts (Submitted or partially received)
            $pendingPosCount = PurchaseOrder::whereIn('status', ['SUBMITTED', 'PARTIALLY_RECEIVED'])->count();
            $recentPos = PurchaseOrder::with('supplier')
                ->whereIn('status', ['SUBMITTED', 'PARTIALLY_RECEIVED'])
                ->latest('ordered_at')
                ->take(5)
                ->get();

            // Outbound transfers waiting for dispatch
            $draftTransfersCount = StockTransfer::where('from_location_id', $whLocId)
                ->where('status', 'DRAFT')
                ->count();

            // Low stock SKU count at this warehouse
            $lowStockCount = InventoryBalance::where('location_id', $whLocId)
                ->where('on_hand_qty', '<=', 10)
                ->count();

            // Active stock opname session
            $activeOpname = StockOpname::where('location_id', $whLocId)
                ->whereIn('status', [StockOpname::STATUS_OPEN, StockOpname::STATUS_COUNTING, StockOpname::STATUS_RECONCILIATION])
                ->first();

            // Recent 5 stock movements
            $recentMovements = StockMovement::where('location_id', $whLocId)
                ->with(['product', 'operation.actor'])
                ->latest('occurred_at')
                ->take(5)
                ->get();

            $profileData = [
                'pending_pos_count' => $pendingPosCount,
                'recent_pos' => $recentPos,
                'draft_transfers_count' => $draftTransfersCount,
                'low_stock_count' => $lowStockCount,
                'active_opname' => $activeOpname,
                'recent_movements' => $recentMovements,
            ];
        } elseif ($dashboardProfile === 'b2b') {
            // Reserved active orders
            $activeOrdersCount = B2bOrder::where('status', B2bOrder::STATUS_RESERVED)->count();
            
            // Pickup tickets waiting for customer redemption
            $pendingTicketsCount = PickupTicket::where('status', PickupTicket::STATUS_ISSUED)->count();

            // Total active B2B partners
            $totalPartnersCount = Member::where('type', Member::TYPE_B2B)
                ->where('is_active', true)
                ->count();

            // Invoices overdue
            $overdueInvoicesCount = Invoice::whereIn('status', [Invoice::STATUS_OPEN, Invoice::STATUS_PARTIALLY_PAID, Invoice::STATUS_OVERDUE])
                ->where('due_date', '<', Carbon::today())
                ->count();

            // Recent 5 B2B Orders
            $recentOrders = B2bOrder::with(['member', 'pickupTicket'])
                ->latest('created_at')
                ->take(5)
                ->get();

            $profileData = [
                'active_orders_count' => $activeOrdersCount,
                'pending_tickets_count' => $pendingTicketsCount,
                'total_partners_count' => $totalPartnersCount,
                'overdue_invoices_count' => $overdueInvoicesCount,
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
