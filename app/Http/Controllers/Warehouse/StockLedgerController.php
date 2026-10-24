<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\InventoryBalance;
use App\Models\Location;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockLedgerController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        if (!$user->hasPermission('inventory.view') && !$user->isSuperAdmin() && !$user->isOwner()) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk melihat Kartu Stok & Saldo Inventaris.');
        }

        $activeLocation = $user->getActiveLocation();
        $canViewHpp = $user->hasPermission('inventory.hpp_view') || $user->isSuperAdmin() || $user->isOwner();

        // 1. Fetch Balances
        $balanceQuery = InventoryBalance::with(['product.baseUom', 'location']);

        if ($request->filled('location_id')) {
            $balanceQuery->where('location_id', $request->location_id);
        } elseif ($activeLocation && !$user->isSuperAdmin() && !$user->isOwner()) {
            $balanceQuery->where('location_id', $activeLocation->id);
        }

        if ($request->filled('product_id')) {
            $balanceQuery->where('product_id', $request->product_id);
        }

        $balances = $balanceQuery->orderBy('product_id')->get();

        // 2. Fetch Movements Ledger
        $ledgerQuery = StockMovement::with(['product.baseUom', 'location', 'operation.actor']);

        if ($request->filled('location_id')) {
            $ledgerQuery->where('location_id', $request->location_id);
        } elseif ($activeLocation && !$user->isSuperAdmin() && !$user->isOwner()) {
            $ledgerQuery->where('location_id', $activeLocation->id);
        }

        if ($request->filled('product_id')) {
            $ledgerQuery->where('product_id', $request->product_id);
        }

        if ($request->filled('semantic_status')) {
            $ledgerQuery->where('semantic_status', $request->semantic_status);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $ledgerQuery->whereHas('product', function ($p) use ($search) {
                $p->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $movements = $ledgerQuery->orderByDesc('id')->paginate(15)->withQueryString();

        // 3. Stats & Dropdowns
        $locations = Location::where('is_active', true)->orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();

        $totalOnHandSku = $balances->where('on_hand_qty', '>', 0)->count();
        $totalValuation = $balances->sum(fn($b) => ((float) $b->on_hand_qty) * ((float) $b->avg_unit_cost));

        return view('warehouse.ledger.index', compact(
            'balances',
            'movements',
            'locations',
            'products',
            'activeLocation',
            'user',
            'canViewHpp',
            'totalOnHandSku',
            'totalValuation'
        ));
    }
}
