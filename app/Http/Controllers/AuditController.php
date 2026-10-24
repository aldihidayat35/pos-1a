<?php

namespace App\Http\Controllers;

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
        $activeLocation = $user->getActiveLocation();
        $primaryRole = $user->primaryRole();

        return view('audit.index', compact('user', 'activeLocation', 'primaryRole'));
    }
}
