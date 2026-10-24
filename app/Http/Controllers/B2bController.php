<?php

namespace App\Http\Controllers;

use App\Models\B2bOrder;
use App\Models\Invoice;
use App\Models\Member;
use App\Services\CreditPolicyService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class B2bController extends Controller
{
    public function __construct(
        protected CreditPolicyService $creditPolicyService
    ) {}

    /**
     * Display B2B Operational Command Center view
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $activeLocation = $user->getActiveLocation();
        $primaryRole = $user->primaryRole();

        $b2bMembers = Member::where('type', Member::TYPE_B2B)
            ->with(['tier', 'invoices'])
            ->orderBy('name')
            ->get();

        // Calculate real-time credit exposure for each partner
        $memberExposures = [];
        $totalOutstandingDebt = 0.00;
        $totalOverdueDebt = 0.00;
        $overdueCount = 0;

        foreach ($b2bMembers as $m) {
            $exp = $this->creditPolicyService->getMemberCreditExposure($m);
            $memberExposures[$m->id] = $exp;
            $totalOutstandingDebt += $exp['invoiced_outstanding'];
            $totalOverdueDebt += $exp['overdue_amount'];
            $overdueCount += $exp['overdue_count'];
        }

        $activeOrders = B2bOrder::whereIn('status', [B2bOrder::STATUS_PLACED, B2bOrder::STATUS_RESERVED, B2bOrder::STATUS_READY_FOR_PICKUP])
            ->count();

        $recentOrders = B2bOrder::with(['member.tier', 'fulfillmentLocation', 'pickupTicket'])
            ->latest()
            ->take(6)
            ->get();

        $stats = [
            'total_b2b_members' => $b2bMembers->where('is_active', true)->count(),
            'total_credit_limit' => (float) $b2bMembers->where('is_active', true)->sum('credit_limit'),
            'total_outstanding_debt' => $totalOutstandingDebt,
            'total_overdue_debt' => $totalOverdueDebt,
            'overdue_count' => $overdueCount,
            'active_orders_count' => $activeOrders,
            'active_location_name' => $activeLocation?->name ?? 'Semua Lokasi',
            'active_location_code' => $activeLocation?->code ?? 'ALL',
        ];

        return view('b2b.index', compact('user', 'activeLocation', 'primaryRole', 'b2bMembers', 'memberExposures', 'recentOrders', 'stats'));
    }
}
