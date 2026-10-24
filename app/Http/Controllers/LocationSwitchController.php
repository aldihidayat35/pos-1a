<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LocationSwitchController extends Controller
{
    /**
     * Switch active operational location for authenticated user
     */
    public function switch(Request $request): RedirectResponse
    {
        $request->validate([
            'location_id' => ['required', 'exists:locations,id'],
        ]);

        $user = $request->user();
        $targetLocation = Location::active()->findOrFail($request->integer('location_id'));

        if (! $user->canAccessLocation($targetLocation->id)) {
            return redirect()->back()
                ->with('error', 'Akses Ditolak: Anda tidak ditugaskan pada lokasi ' . $targetLocation->name . '.');
        }

        Session::put('active_location_id', $targetLocation->id);

        return redirect()->back()
            ->with('success', 'Lokasi aktif berhasil dialihkan ke ' . $targetLocation->name . ' (' . $targetLocation->code . ').');
    }
}
