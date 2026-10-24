<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Product;
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
        $activeLocation = $user->getActiveLocation();
        $primaryRole = $user->primaryRole();

        $stats = [
            'total_sku' => Product::where('is_active', true)->count(),
            'active_location_name' => $activeLocation?->name ?? 'Gudang Pusat Sentral',
            'active_location_code' => $activeLocation?->code ?? 'WH-CENTRAL',
        ];

        return view('warehouse.index', compact('user', 'activeLocation', 'primaryRole', 'stats'));
    }
}
