<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmergencyPurchaseRequest;
use App\Models\EmergencyPurchase;
use App\Models\Location;
use App\Models\Product;
use App\Services\EmergencyPurchaseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmergencyPurchaseController extends Controller
{
    public function __construct(
        protected EmergencyPurchaseService $emergencyService
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        if (!$user->hasPermission('emergency.input') && !$user->hasPermission('inventory.view') && !$user->isSuperAdmin() && !$user->isOwner()) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk melihat data emergency.');
        }

        $activeLocation = $user->getActiveLocation();
        $query = EmergencyPurchase::with(['product.baseUom', 'productUom.uom', 'creator', 'location']);

        if ($activeLocation && !$user->isSuperAdmin() && !$user->isOwner()) {
            $query->where('location_id', $activeLocation->id);
        }

        $purchases = $query->orderByDesc('id')->paginate(10);

        return view('store.emergency.index', compact('purchases', 'user', 'activeLocation'));
    }

    public function create(Request $request): View
    {
        $user = $request->user();
        if (!$user->hasPermission('emergency.input') && !$user->isSuperAdmin()) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk mencatat pembelian emergency toko.');
        }

        $activeLocation = $user->getActiveLocation() ?? Location::where('type', 'STORE')->first();
        $products = Product::where('is_active', true)
            ->with(['baseUom', 'productUoms.uom'])
            ->orderBy('name')
            ->get();

        return view('store.emergency.create', compact('activeLocation', 'products', 'user'));
    }

    public function store(StoreEmergencyPurchaseRequest $request): RedirectResponse
    {
        try {
            $emergencyPurchase = $this->emergencyService->recordEmergencyPurchase(
                $request->validated(),
                $request->file('receipt_photo'),
                $request->user()
            );

            return redirect()->route('store.emergency.show', $emergencyPurchase->id)
                ->with('success', "Pembelian emergency {$emergencyPurchase->purchase_number} berhasil dibukukan! Stok toko langsung bertambah dan notifikasi audit Oranye telah dipublikasikan.");
        } catch (\DomainException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(EmergencyPurchase $emergencyPurchase, Request $request): View
    {
        $user = $request->user();
        if (!$user->hasPermission('emergency.input') && !$user->hasPermission('inventory.view') && !$user->isSuperAdmin() && !$user->isOwner()) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk melihat detail emergency.');
        }

        $emergencyPurchase->load(['location', 'product.baseUom', 'productUom.uom', 'creator']);

        return view('store.emergency.show', compact('emergencyPurchase', 'user'));
    }
}
