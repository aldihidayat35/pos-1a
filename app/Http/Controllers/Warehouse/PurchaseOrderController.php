<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePurchaseOrderRequest;
use App\Models\Location;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Services\PurchasingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PurchaseOrderController extends Controller
{
    public function __construct(
        protected PurchasingService $purchasingService
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        if (!$user->hasPermission('inventory.view') && !$user->hasPermission('purchasing.po') && !$user->isSuperAdmin() && !$user->isOwner()) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk melihat data PO.');
        }

        $activeLocation = $user->getActiveLocation();
        $query = PurchaseOrder::with(['supplier', 'destinationLocation', 'creator', 'items.product']);

        if ($request->filled('location_id')) {
            $query->where('destination_location_id', $request->location_id);
        } elseif ($activeLocation && !$user->isSuperAdmin() && !$user->isOwner()) {
            $query->where('destination_location_id', $activeLocation->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('po_number', 'like', "%{$search}%")
                  ->orWhereHas('supplier', fn($s) => $s->where('name', 'like', "%{$search}%"));
            });
        }

        $perPage = min(max((int) $request->input('per_page', 12), 5), 100);
        $orders = $query->orderByDesc('id')->paginate($perPage)->withQueryString();

        $statusCounts = [
            'ALL' => PurchaseOrder::count(),
            'ISSUED' => PurchaseOrder::where('status', 'ISSUED')->count(),
            'PARTIALLY_RECEIVED' => PurchaseOrder::where('status', 'PARTIALLY_RECEIVED')->count(),
            'RECEIVED' => PurchaseOrder::where('status', 'RECEIVED')->count(),
        ];

        return view('warehouse.po.index', compact('orders', 'user', 'activeLocation', 'statusCounts'));
    }

    public function create(Request $request): View
    {
        $user = $request->user();
        if (!$user->hasPermission('purchasing.po') && !$user->hasPermission('inventory.manage') && !$user->isSuperAdmin()) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk membuat Purchase Order.');
        }

        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $locations = Location::where('is_active', true)->orderBy('name')->get();
        $products = Product::where('is_active', true)
            ->with(['baseUom', 'productUoms.uom'])
            ->orderBy('name')
            ->get();
        $activeLocation = $user->getActiveLocation();

        return view('warehouse.po.create', compact('suppliers', 'locations', 'products', 'activeLocation', 'user'));
    }

    public function store(StorePurchaseOrderRequest $request): RedirectResponse
    {
        $po = $this->purchasingService->createPurchaseOrder($request->validated(), $request->user());

        return redirect()->route('warehouse.po.show', $po->id)
            ->with('success', "Purchase Order {$po->po_number} berhasil diterbitkan.");
    }

    public function show(PurchaseOrder $purchaseOrder, Request $request): View
    {
        $user = $request->user();
        if (!$user->hasPermission('inventory.view') && !$user->hasPermission('purchasing.po') && !$user->hasPermission('purchasing.receive') && !$user->isSuperAdmin() && !$user->isOwner()) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk melihat Purchase Order ini.');
        }

        $purchaseOrder->load([
            'supplier',
            'destinationLocation',
            'creator',
            'approver',
            'items.product.baseUom',
            'items.productUom.uom',
            'goodsReceipts.creator',
            'goodsReceipts.items.product',
            'goodsReceipts.items.productUom.uom',
        ]);

        $canReceive = ($user->hasPermission('purchasing.receive') || $user->isSuperAdmin())
            && in_array($purchaseOrder->status, ['ISSUED', 'PARTIALLY_RECEIVED']);

        $canViewHpp = $user->hasPermission('inventory.hpp_view') || $user->isSuperAdmin() || $user->isOwner();

        return view('warehouse.po.show', compact('purchaseOrder', 'user', 'canReceive', 'canViewHpp'));
    }
}
