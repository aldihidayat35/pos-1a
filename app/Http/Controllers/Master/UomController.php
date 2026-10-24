<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Uom;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UomController extends Controller
{
    public function index(): View
    {
        $uoms = Uom::withCount('baseProducts')->orderBy('dimension')->orderBy('name')->paginate(15);
        return view('master.uoms.index', compact('uoms'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:30', 'unique:uoms,code'],
            'name' => ['required', 'string', 'max:100'],
            'symbol' => ['required', 'string', 'max:15'],
            'dimension' => ['required', 'in:COUNT,WEIGHT,VOLUME'],
            'precision' => ['required', 'integer', 'min:0', 'max:4'],
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));
        $uom = Uom::create($validated);

        return back()->with('success', "Satuan [{$uom->name}] ({$uom->symbol}) berhasil dibuat!");
    }

    public function update(Request $request, Uom $uom): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:30', 'unique:uoms,code,' . $uom->id],
            'name' => ['required', 'string', 'max:100'],
            'symbol' => ['required', 'string', 'max:15'],
            'dimension' => ['required', 'in:COUNT,WEIGHT,VOLUME'],
            'precision' => ['required', 'integer', 'min:0', 'max:4'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));
        $uom->update($validated);

        return back()->with('success', "Satuan [{$uom->name}] berhasil diperbarui!");
    }

    public function destroy(Uom $uom): RedirectResponse
    {
        if ($uom->baseProducts()->count() > 0 || $uom->productUoms()->count() > 0) {
            $uom->update(['is_active' => false]);
            return back()->with('success', "Satuan [{$uom->name}] dinonaktifkan karena terikat pada produk.");
        }

        $uom->delete();
        return back()->with('success', "Satuan [{$uom->name}] berhasil dihapus.");
    }
}
