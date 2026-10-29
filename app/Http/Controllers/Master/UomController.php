<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Uom;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UomController extends Controller
{
    public function index(Request $request): View
    {
        $perPage = min(max((int) $request->input('per_page', 15), 5), 100);
        $search = trim($request->input('search', ''));

        $query = Uom::withCount('baseProducts');
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('symbol', 'like', "%{$search}%");
            });
        }

        $uoms = $query->orderBy('dimension')->orderBy('name')->paginate($perPage)->withQueryString();
        return view('master.uoms.index', compact('uoms'));
    }

    public function create(): View
    {
        return view('master.uoms.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:30', 'unique:uoms,code'],
            'name' => ['required', 'string', 'max:100'],
            'symbol' => ['required', 'string', 'max:15'],
            'dimension' => ['required', 'in:COUNT,WEIGHT,VOLUME'],
            'precision' => ['required', 'integer', 'min:0', 'max:4'],
        ], [
            'code.required' => 'Kode satuan wajib diisi.',
            'code.unique' => 'Kode satuan ini sudah digunakan oleh data lain.',
            'name.required' => 'Nama lengkap satuan wajib diisi.',
            'symbol.required' => 'Simbol satuan wajib diisi.',
            'dimension.required' => 'Dimensi fisik satuan wajib dipilih.',
            'precision.required' => 'Presisi desimal wajib diisi.',
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));
        $uom = Uom::create($validated);

        return redirect()->route('master.uoms.index')->with('success', "Satuan [{$uom->name}] ({$uom->symbol}) berhasil dibuat!");
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
        ], [
            'code.required' => 'Kode satuan wajib diisi.',
            'code.unique' => 'Kode satuan ini sudah digunakan oleh data lain.',
            'name.required' => 'Nama lengkap satuan wajib diisi.',
            'symbol.required' => 'Simbol satuan wajib diisi.',
            'dimension.required' => 'Dimensi fisik satuan wajib dipilih.',
            'precision.required' => 'Presisi desimal wajib diisi.',
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
