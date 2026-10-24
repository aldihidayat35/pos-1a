<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\PriceRule;
use App\Models\PricingPolicy;
use App\Models\Product;
use App\Services\PricingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PricingController extends Controller
{
    public function __construct(
        protected PricingService $pricingService
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        if (!$user->hasPermission('inventory.view') && !$user->isSuperAdmin() && !$user->isOwner()) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk melihat kebijakan harga.');
        }

        $activeLocation = $user->getActiveLocation() ?? Location::where('code', 'WH-CENTRAL')->first();
        $policies = PricingPolicy::with('location')->get();
        $products = Product::where('is_active', true)->with('baseUom')->get();

        // Calculate pricing matrix for each product
        $pricingMatrix = [];
        foreach ($products as $p) {
            $pricingMatrix[] = [
                'product' => $p,
                'limits' => $this->pricingService->calculatePricingLimits($p, $activeLocation),
            ];
        }

        $canManage = $user->hasPermission('system.settings') || $user->isSuperAdmin();

        return view('master.pricing.index', compact('policies', 'pricingMatrix', 'activeLocation', 'user', 'canManage'));
    }

    public function check(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'offered_price' => ['required', 'numeric'],
            'channel' => ['nullable', 'string'],
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $location = $request->user()->getActiveLocation() ?? Location::first();
        $channel = $validated['channel'] ?? 'RETAIL';
        $offeredPrice = (float) $validated['offered_price'];

        $limits = $this->pricingService->calculatePricingLimits($product, $location, $channel);

        if ($offeredPrice < $limits['effective_min_price']) {
            return response()->json([
                'valid' => false,
                'message' => "Harga tawar Rp " . number_format($offeredPrice, 0, ',', '.') . " DITOLAK! Berada di bawah batas minimum (Floor Price: Rp " . number_format($limits['effective_min_price'], 0, ',', '.') . "). Transaksi diblokir oleh server.",
                'limits' => $limits,
            ], 422);
        }

        return response()->json([
            'valid' => true,
            'message' => "Harga tawar Rp " . number_format($offeredPrice, 0, ',', '.') . " VALID (Memenuhi batas Floor Price Rp " . number_format($limits['effective_min_price'], 0, ',', '.') . ").",
            'limits' => $limits,
        ]);
    }

    public function updatePolicy(Request $request): RedirectResponse
    {
        $user = $request->user();
        if (!$user->hasPermission('system.settings') && !$user->isSuperAdmin()) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk mengubah kebijakan harga global.');
        }

        $validated = $request->validate([
            'policy_id' => ['required', 'exists:pricing_policies,id'],
            'min_markup_type' => ['required', 'in:PERCENTAGE,FIXED'],
            'min_markup_value' => ['required', 'numeric', 'min:0'],
        ]);

        $policy = PricingPolicy::findOrFail($validated['policy_id']);
        $policy->update([
            'min_markup_type' => $validated['min_markup_type'],
            'min_markup_value' => $validated['min_markup_value'],
        ]);

        return back()->with('success', "Kebijakan Markup Floor Price untuk channel {$policy->channel} berhasil diperbarui.");
    }
}
