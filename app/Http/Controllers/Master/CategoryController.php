<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $perPage = min(max((int) $request->input('per_page', 15), 5), 100);
        $search = trim($request->input('search', ''));

        $query = ProductCategory::with('parent')->withCount('products');
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $categories = $query->orderBy('name')->paginate($perPage)->withQueryString();
        $parentCandidates = ProductCategory::active()->get();

        return view('master.categories.index', compact('categories', 'parentCandidates'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:product_categories,code'],
            'name' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'exists:product_categories,id'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $category = ProductCategory::create($validated);

        return back()->with('success', "Kategori [{$category->name}] berhasil dibuat!");
    }

    public function update(Request $request, ProductCategory $category): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:product_categories,code,' . $category->id],
            'name' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'exists:product_categories,id', 'different:id'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $category->update($validated);

        return back()->with('success', "Kategori [{$category->name}] berhasil diperbarui!");
    }

    public function destroy(ProductCategory $category): RedirectResponse
    {
        if ($category->products()->count() > 0) {
            $category->update(['is_active' => false]);
            return back()->with('success', "Kategori [{$category->name}] dinonaktifkan karena memiliki produk terhubung.");
        }

        $category->delete();
        return back()->with('success', "Kategori [{$category->name}] berhasil dihapus.");
    }
}
