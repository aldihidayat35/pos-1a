<?php

namespace App\Http\Controllers;

use App\Models\AnomalyEvent;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditController extends Controller
{
    /**
     * Display 5-Color Movement Stream & Anomaly Audit view
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        if (!$user->hasPermission('dashboard.audit') && !$user->isSuperAdmin() && !$user->isOwner()) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk melihat Accountability Board.');
        }

        $activeLocation = $user->getActiveLocation();
        $primaryRole = $user->primaryRole();

        $query = AnomalyEvent::with(['location', 'actor']);

        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        $anomalies = $query->orderByDesc('id')->paginate(12)->withQueryString();

        $counts = [
            'ALL' => AnomalyEvent::count(),
            'ORANGE' => AnomalyEvent::where('severity', 'ORANGE')->count(),
            'RED' => AnomalyEvent::where('severity', 'RED')->count(),
        ];

        return view('audit.index', compact('user', 'activeLocation', 'primaryRole', 'anomalies', 'counts'));
    }
}
