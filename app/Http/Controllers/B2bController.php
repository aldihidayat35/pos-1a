<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\View\View;

class B2bController extends Controller
{
    /**
     * Display B2B Partnership & Sales view
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $activeLocation = $user->getActiveLocation();
        $primaryRole = $user->primaryRole();

        $b2bMembers = Member::where('type', Member::TYPE_B2B)
            ->with('tier')
            ->orderBy('name')
            ->get();

        $stats = [
            'total_b2b_members' => $b2bMembers->where('is_active', true)->count(),
            'total_credit_limit' => $b2bMembers->where('is_active', true)->sum('credit_limit'),
            'active_location_name' => $activeLocation?->name ?? 'Semua Lokasi',
            'active_location_code' => $activeLocation?->code ?? 'ALL',
        ];

        return view('b2b.index', compact('user', 'activeLocation', 'primaryRole', 'b2bMembers', 'stats'));
    }
}
