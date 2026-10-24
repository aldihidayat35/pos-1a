<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LocationMasterController extends Controller
{
    public function index(): View
    {
        $locations = Location::withCount('userLocations')->orderBy('type')->orderBy('name')->paginate(15);
        return view('master.locations.index', compact('locations'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:locations,code'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:WAREHOUSE,STORE,TRANSIT'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));
        $location = Location::create($validated);

        return back()->with('success', "Lokasi [{$location->name}] ({$location->code}) berhasil ditambahkan!");
    }

    public function update(Request $request, Location $location): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:locations,code,' . $location->id],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:WAREHOUSE,STORE,TRANSIT'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));
        $location->update($validated);

        return back()->with('success', "Lokasi [{$location->name}] berhasil diperbarui!");
    }

    public function destroy(Location $location): RedirectResponse
    {
        $location->update(['is_active' => false]);
        return back()->with('success', "Lokasi [{$location->name}] berhasil dinonaktifkan.");
    }
}
