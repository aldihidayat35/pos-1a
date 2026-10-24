<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\InventoryBalance;
use App\Models\Location;
use App\Models\Member;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Sale;
use App\Services\PosService;
use App\Services\PricingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PosController extends Controller
{
    public function __construct(
        protected PosService $posService,
        protected PricingService $pricingService
    ) {}

    /**
     * Display POS Terminal Cashier Interface
     */
    public function terminal(Request $request): View
    {
        $user = $request->user();
        if (!$user->hasPermission('pos.access') && !$user->isSuperAdmin() && !$user->isOwner()) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk mengoperasikan kasir POS.');
        }

        $activeLocation = $user->getActiveLocation() ?? Location::where('type', 'STORE')->first();
        $activeShift = $this->posService->getActiveShift($user, $activeLocation);

        $categories = ProductCategory::orderBy('name')->get();
        $members = Member::where('is_active', true)->orderBy('name')->get();

        // Load ready products with stock at active store
        $storeBalances = InventoryBalance::where('location_id', $activeLocation->id)
            ->where('on_hand_qty', '>', 0)
            ->with(['product.category', 'product.baseUom', 'product.productUoms.uom'])
            ->get();

        $catalog = [];
        foreach ($storeBalances as $bal) {
            $prod = $bal->product;
            if (!$prod || !$prod->is_active) continue;

            $limits = $this->pricingService->calculatePricingLimits($prod, $activeLocation, 'RETAIL');

            $catalog[] = [
                'id' => $prod->id,
                'name' => $prod->name,
                'sku' => $prod->sku,
                'category_id' => $prod->category_id,
                'category_name' => $prod->category?->name ?? 'Umum',
                'on_hand_qty' => (float) $bal->on_hand_qty,
                'base_uom_symbol' => $prod->baseUom?->symbol ?? 'Pcs',
                'floor_price' => $limits['effective_min_price'],
                'list_price' => $limits['list_price'],
                'uoms' => $prod->productUoms->map(function ($pu) use ($limits) {
                    $factor = (float) $pu->factor_to_base ?: 1.0;
                    return [
                        'id' => $pu->id,
                        'name' => $pu->uom?->name ?? 'Satuan',
                        'symbol' => $pu->uom?->symbol ?? 'Pcs',
                        'factor' => $factor,
                        'barcode' => $pu->barcode,
                        'is_base' => (bool) $pu->is_base,
                        'calculated_price' => round($limits['list_price'] * $factor, 2),
                        'calculated_floor' => round($limits['effective_min_price'] * $factor, 2),
                    ];
                })->values()->toArray(),
            ];
        }

        return view('store.pos.terminal', compact(
            'user',
            'activeLocation',
            'activeShift',
            'categories',
            'members',
            'catalog'
        ));
    }

    /**
     * Fast JSON search API for barcode scanners and live product search
     */
    public function searchProducts(Request $request): JsonResponse
    {
        $user = $request->user();
        $activeLocation = $user->getActiveLocation() ?? Location::where('type', 'STORE')->first();
        $query = trim($request->input('q', ''));

        $products = Product::where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('sku', 'LIKE', "%{$query}%")
                  ->orWhereHas('productUoms', function ($uq) use ($query) {
                      $uq->where('barcode', $query);
                  });
            })
            ->with(['baseUom', 'productUoms.uom', 'category'])
            ->take(15)
            ->get();

        $results = [];
        foreach ($products as $prod) {
            $bal = InventoryBalance::where('location_id', $activeLocation->id)
                ->where('product_id', $prod->id)
                ->first();

            $limits = $this->pricingService->calculatePricingLimits($prod, $activeLocation, 'RETAIL');

            $results[] = [
                'id' => $prod->id,
                'name' => $prod->name,
                'sku' => $prod->sku,
                'category' => $prod->category?->name,
                'on_hand_qty' => $bal ? (float) $bal->on_hand_qty : 0.0,
                'base_uom_symbol' => $prod->baseUom?->symbol,
                'floor_price' => $limits['effective_min_price'],
                'list_price' => $limits['list_price'],
                'uoms' => $prod->productUoms->map(function ($pu) use ($limits) {
                    $factor = (float) $pu->factor_to_base ?: 1.0;
                    return [
                        'id' => $pu->id,
                        'name' => $pu->uom?->name,
                        'symbol' => $pu->uom?->symbol,
                        'factor' => $factor,
                        'barcode' => $pu->barcode,
                        'price' => round($limits['list_price'] * $factor, 2),
                        'floor_price' => round($limits['effective_min_price'] * $factor, 2),
                    ];
                })->values()->toArray(),
            ];
        }

        return response()->json($results);
    }

    /**
     * Process POS Checkout
     */
    public function checkout(Request $request): JsonResponse|RedirectResponse
    {
        $user = $request->user();
        if (!$user->hasPermission('pos.access') && !$user->isSuperAdmin()) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin transaksi kasir POS.');
        }

        $validated = $request->validate([
            'location_id' => ['required', 'exists:locations,id'],
            'payment_method' => ['required', 'in:CASH,QRIS,TRANSFER,DEBIT'],
            'paid_amount' => ['required', 'numeric', 'min:0'],
            'idempotency_key' => ['nullable', 'string', 'max:64'],
            'customer_name' => ['nullable', 'string', 'max:100'],
            'member_id' => ['nullable', 'exists:members,id'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'tax_amount' => ['nullable', 'numeric', 'min:0'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.product_uom_id' => ['required', 'exists:product_uoms,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.001'],
            'items.*.unit_price' => ['required', 'numeric', 'min:1'],
        ]);

        try {
            $sale = $this->posService->checkout($validated, $user);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Pembayaran berhasil! Struk {$sale->sale_number} diterbitkan.",
                    'sale' => $sale,
                    'receipt_url' => route('store.pos.receipt', $sale->id),
                ]);
            }

            return redirect()->route('store.pos.receipt', $sale->id)
                ->with('success', "Transaksi {$sale->sale_number} berhasil dibukukan!");
        } catch (\DomainException $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Display Thermal Receipt for Printing & Verification
     */
    public function receipt(Sale $sale, Request $request): View
    {
        $user = $request->user();
        if (!$user->hasPermission('pos.access') && !$user->hasPermission('inventory.view') && !$user->isSuperAdmin() && !$user->isOwner()) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk melihat struk penjualan.');
        }

        $sale->load([
            'items.product.baseUom',
            'items.productUom.uom',
            'payments.receiver',
            'location',
            'cashier',
            'member',
            'voidedByUser',
        ]);

        return view('store.pos.receipt', compact('sale', 'user'));
    }

    /**
     * List POS Transactions History
     */
    public function history(Request $request): View
    {
        $user = $request->user();
        if (!$user->hasPermission('pos.access') && !$user->hasPermission('inventory.view') && !$user->isSuperAdmin() && !$user->isOwner()) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk melihat riwayat penjualan.');
        }

        $activeLocation = $user->getActiveLocation() ?? Location::where('type', 'STORE')->first();

        $query = Sale::with(['cashier', 'member', 'items.product', 'payments']);

        if (!$user->isSuperAdmin() && !$user->isOwner()) {
            $query->where('location_id', $activeLocation->id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('sale_number', 'LIKE', "%{$search}%")
                  ->orWhere('customer_name', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        $sales = $query->orderByDesc('id')->paginate(12)->withQueryString();

        $canVoid = $user->hasPermission('pos.void') || $user->isSuperAdmin();

        return view('store.pos.history', compact('sales', 'user', 'activeLocation', 'canVoid'));
    }

    /**
     * Void / Reverse a POS Transaction
     */
    public function void(Sale $sale, Request $request): RedirectResponse
    {
        $user = $request->user();
        if (!$user->hasPermission('pos.void') && !$user->isSuperAdmin()) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk membatalkan (void) transaksi kasir.');
        }

        $validated = $request->validate([
            'void_reason' => ['required', 'string', 'min:5', 'max:500'],
        ]);

        try {
            $this->posService->voidSale($sale, $user, $validated['void_reason']);

            return back()->with('success', "Struk {$sale->sale_number} berhasil dibatalkan (Void). Seluruh stok telah dikembalikan ke rak dan audit merah diterbitkan.");
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
