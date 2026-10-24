<?php

namespace App\Http\Controllers\Master;

use App\Domain\Catalog\Services\ProductCatalogService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Master\StoreProductRequest;
use App\Http\Requests\Master\UpdateProductRequest;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Uom;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        protected ProductCatalogService $catalogService
    ) {}

    /**
     * Display a listing of products with search and category filtering
     */
    public function index(Request $request): View
    {
        $query = Product::with(['category', 'baseUom', 'productUoms.uom']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhereHas('productUoms', function ($uq) use ($search) {
                      $uq->where('barcode', 'like', "%{$search}%");
                  });
            });
        }

        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        if ($request->has('status')) {
            if ($request->input('status') === 'active') {
                $query->where('is_active', true);
            } elseif ($request->input('status') === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $perPage = min(max((int) $request->input('per_page', 12), 5), 100);
        $products = $query->orderBy('name')->paginate($perPage)->withQueryString();
        $categories = ProductCategory::active()->orderBy('name')->get();
        $uoms = Uom::active()->orderBy('name')->get();

        return view('master.products.index', compact('products', 'categories', 'uoms'));
    }

    /**
     * Show form for creating a new product
     */
    public function create(): View
    {
        $categories = ProductCategory::active()->orderBy('name')->get();
        $uoms = Uom::active()->orderBy('name')->get();

        return view('master.products.create', compact('categories', 'uoms'));
    }

    /**
     * Store newly created product
     */
    public function store(StoreProductRequest $request): RedirectResponse
    {
        try {
            $data = $request->validated();
            if ($request->hasFile('image')) {
                $data['image_path'] = $request->file('image')->store('products', 'public');
            }

            $product = $this->catalogService->createProduct($data);

            return redirect()->route('master.products.index')
                ->with('success', "Produk [{$product->name}] dengan SKU {$product->sku} berhasil ditambahkan!");
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Show form for editing product
     */
    public function edit(Product $product): View
    {
        $product->load(['category', 'baseUom', 'productUoms.uom']);
        $categories = ProductCategory::active()->orderBy('name')->get();
        $uoms = Uom::active()->orderBy('name')->get();

        return view('master.products.edit', compact('product', 'categories', 'uoms'));
    }

    /**
     * Update product
     */
    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        try {
            $data = $request->validated();

            if ($request->boolean('remove_image')) {
                if ($product->image_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($product->image_path)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($product->image_path);
                }
                $data['image_path'] = null;
            } elseif ($request->hasFile('image')) {
                if ($product->image_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($product->image_path)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($product->image_path);
                }
                $data['image_path'] = $request->file('image')->store('products', 'public');
            }

            $this->catalogService->updateProduct($product, $data);

            return redirect()->route('master.products.index')
                ->with('success', "Produk [{$product->name}] berhasil diperbarui!");
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Soft deactivation for product
     */
    public function destroy(Product $product): RedirectResponse
    {
        $this->catalogService->deactivateProduct($product);

        return redirect()->route('master.products.index')
            ->with('success', "Produk [{$product->name}] berhasil dinonaktifkan.");
    }

    /**
     * API for real-time fuzzy duplicate similarity checking (PRD 4.2 >= 75%)
     */
    public function checkDuplicate(Request $request): JsonResponse
    {
        $name = (string) $request->query('name', '');
        $excludeId = $request->query('exclude_id') ? (int) $request->query('exclude_id') : null;

        $duplicates = $this->catalogService->checkFuzzySimilarity($name, $excludeId);

        return response()->json([
            'duplicates' => $duplicates,
            'count' => count($duplicates),
            'has_warning' => count($duplicates) > 0,
        ]);
    }
}
