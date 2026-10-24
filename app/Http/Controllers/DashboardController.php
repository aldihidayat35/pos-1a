<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the main shell dashboard
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $activeLocation = $user->getActiveLocation();
        $primaryRole = $user->primaryRole();

        // Real operational baseline stats (honest values, no fake financial calculations)
        $stats = [
            'total_users' => User::where('is_active', true)->count(),
            'total_locations' => Location::active()->count(),
            'assigned_locations_count' => $user->isSuperAdmin() || $user->isOwner()
                ? Location::active()->where('type', '!=', Location::TYPE_TRANSIT)->count()
                : $user->locations()->where('is_active', true)->count(),
            'user_role_label' => $primaryRole?->label ?? 'User',
            'user_role_code' => $primaryRole?->name ?? 'unknown',
            'active_location_name' => $activeLocation?->name ?? 'Semua Lokasi Terpusat',
            'active_location_code' => $activeLocation?->code ?? 'ALL',
            'active_location_type' => $activeLocation?->type ?? 'CENTRAL',
        ];

        return view('dashboard.index', compact('user', 'stats', 'activeLocation', 'primaryRole'));
    }
}
