<?php

namespace App\Http\Controllers;

use App\Models\EmergencyPurchase;
use App\Models\InventoryBalance;
use App\Models\Location;
use App\Models\Product;
use App\Models\StockTransfer;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StoreController extends Controller
{
    /**
     * Display Store & Retail POS Operations view
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $activeLocation = $user->getActiveLocation() ?? Location::where('code', 'STR-PASAR-1')->first();
        $primaryRole = $user->primaryRole();

        // 1. Incoming transfers heading to this store
        $incomingTransfers = StockTransfer::where('to_location_id', $activeLocation?->id)
            ->whereIn('status', ['DISPATCHED', 'PARTIALLY_RECEIVED'])
            ->with(['fromLocation', 'creator', 'items.product'])
            ->orderByDesc('id')
            ->get();

        // 2. Recent emergency purchases made at this store
        $recentEmergencies = EmergencyPurchase::where('location_id', $activeLocation?->id)
            ->with(['product.baseUom', 'productUom.uom', 'creator'])
            ->orderByDesc('id')
            ->take(5)
            ->get();

        // 3. Store shelf on-hand balances
        $balances = InventoryBalance::where('location_id', $activeLocation?->id)
            ->with(['product.baseUom'])
            ->orderByDesc('on_hand_qty')
            ->take(8)
            ->get();

        $canViewHpp = $user->hasPermission('inventory.hpp_view') || $user->isSuperAdmin() || $user->isOwner();

        $stats = [
            'total_products' => Product::where('is_active', true)->count(),
            'active_location_name' => $activeLocation?->name ?? 'Toko Pasar Raya Blok A',
            'active_location_code' => $activeLocation?->code ?? 'STR-PASAR-1',
            'incoming_transfers_count' => $incomingTransfers->count(),
            'emergency_purchases_count' => $recentEmergencies->count(),
        ];

        return view('store.index', compact(
            'user',
            'activeLocation',
            'primaryRole',
            'stats',
            'incomingTransfers',
            'recentEmergencies',
            'balances',
            'canViewHpp'
        ));
    }
}
