<?php

namespace App\Http\Controllers;

use App\Models\GoodsReceipt;
use App\Models\InventoryBalance;
use App\Models\Location;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\StockOpname;
use App\Models\StockTransfer;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WarehouseController extends Controller
{
    /**
     * Display the Central Warehouse Operations view
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $activeLocation = $user->getActiveLocation() ?? Location::where('code', 'WH-CENTRAL')->first();
        $primaryRole = $user->primaryRole();

        $canViewHpp = $user->hasPermission('inventory.hpp_view') || $user->isSuperAdmin() || $user->isOwner();

        // 1. Pending & Active POs
        $poQuery = PurchaseOrder::with(['supplier', 'destinationLocation', 'items.product']);
        if ($activeLocation && !$user->isSuperAdmin() && !$user->isOwner()) {
            $poQuery->where('destination_location_id', $activeLocation->id);
        }
        $pendingPos = (clone $poQuery)->whereIn('status', ['ISSUED', 'PARTIALLY_RECEIVED'])->orderByDesc('id')->take(5)->get();

        // 2. Recent Goods Receipts (Inbound)
        $grnQuery = GoodsReceipt::with(['purchaseOrder', 'supplier', 'creator', 'items.product']);
        if ($activeLocation && !$user->isSuperAdmin() && !$user->isOwner()) {
            $grnQuery->where('location_id', $activeLocation->id);
        }
        $recentReceipts = $grnQuery->orderByDesc('id')->take(5)->get();

        // 3. Current Stock Balances in Active Location
        $balanceQuery = InventoryBalance::with(['product.baseUom', 'location']);
        if ($activeLocation) {
            $balanceQuery->where('location_id', $activeLocation->id);
        }
        $balances = $balanceQuery->orderByDesc('on_hand_qty')->take(8)->get();

        $totalValuation = $balances->sum(fn($b) => ((float) $b->on_hand_qty) * ((float) $b->avg_unit_cost));

        $stats = [
            'total_sku' => Product::where('is_active', true)->count(),
            'active_location_name' => $activeLocation?->name ?? 'Gudang Pusat Sentral',
            'active_location_code' => $activeLocation?->code ?? 'WH-CENTRAL',
            'pending_po_count' => (clone $poQuery)->whereIn('status', ['ISSUED', 'PARTIALLY_RECEIVED'])->count(),
            'total_receipts_count' => GoodsReceipt::count(),
            'total_valuation' => $totalValuation,
            'low_stock_count' => InventoryBalance::when($activeLocation, fn($q) => $q->where('location_id', $activeLocation->id))->where('on_hand_qty', '<=', 10)->count(),
            'draft_transfers_count' => StockTransfer::where('from_location_id', $activeLocation?->id)->where('status', 'DRAFT')->count(),
            'active_opnames_count' => StockOpname::where('location_id', $activeLocation?->id)->whereIn('status', ['OPEN', 'COUNTING', 'RECONCILIATION'])->count(),
        ];

        return view('warehouse.index', compact(
            'user',
            'activeLocation',
            'primaryRole',
            'stats',
            'pendingPos',
            'recentReceipts',
            'balances',
            'canViewHpp'
        ));
    }
}
