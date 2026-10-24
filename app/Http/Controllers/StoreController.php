<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Product;
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
        $activeLocation = $user->getActiveLocation();
        $primaryRole = $user->primaryRole();

        $stats = [
            'total_products' => Product::where('is_active', true)->count(),
            'active_location_name' => $activeLocation?->name ?? 'Toko Pasar Raya',
            'active_location_code' => $activeLocation?->code ?? 'STR-PASAR-1',
        ];

        return view('store.index', compact('user', 'activeLocation', 'primaryRole', 'stats'));
    }
}
