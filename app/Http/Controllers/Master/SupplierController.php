<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function index(Request $request): View
    {
        $query = Supplier::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('supplier_code', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $perPage = min(max((int) $request->input('per_page', 15), 5), 100);
        $suppliers = $query->orderBy('name')->paginate($perPage)->withQueryString();

        return view('master.suppliers.index', compact('suppliers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'supplier_code' => ['required', 'string', 'max:50', 'unique:suppliers,supplier_code'],
            'name' => ['required', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:35'],
            'email' => ['nullable', 'email', 'max:100'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        $validated['supplier_code'] = strtoupper(trim($validated['supplier_code']));
        $supplier = Supplier::create($validated);

        return back()->with('success', "Suplayer [{$supplier->name}] ({$supplier->supplier_code}) berhasil ditambahkan!");
    }

    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        $validated = $request->validate([
            'supplier_code' => ['required', 'string', 'max:50', 'unique:suppliers,supplier_code,' . $supplier->id],
            'name' => ['required', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:35'],
            'email' => ['nullable', 'email', 'max:100'],
            'address' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['supplier_code'] = strtoupper(trim($validated['supplier_code']));
        $supplier->update($validated);

        return back()->with('success', "Data suplayer [{$supplier->name}] berhasil diperbarui!");
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        $supplier->update(['is_active' => false]);
        return back()->with('success', "Suplayer [{$supplier->name}] berhasil dinonaktifkan.");
    }
}
