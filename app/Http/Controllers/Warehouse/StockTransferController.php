<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStockTransferRequest;
use App\Models\InventoryBalance;
use App\Models\Location;
use App\Models\Product;
use App\Models\StockTransfer;
use App\Services\TransferService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockTransferController extends Controller
{
    public function __construct(
        protected TransferService $transferService
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        if (!$user->hasPermission('inventory.view') && !$user->hasPermission('transfer.dispatch') && !$user->isSuperAdmin() && !$user->isOwner()) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk melihat data transfer.');
        }

        $activeLocation = $user->getActiveLocation();
        $query = StockTransfer::with(['fromLocation', 'toLocation', 'transitLocation', 'creator', 'items.product']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('transfer_number', 'like', "%{$search}%")
                  ->orWhereHas('toLocation', fn($l) => $l->where('name', 'like', "%{$search}%"));
            });
        }

        $transfers = $query->orderByDesc('id')->paginate(12)->withQueryString();

        $statusCounts = [
            'ALL' => StockTransfer::count(),
            'DISPATCHED' => StockTransfer::where('status', 'DISPATCHED')->count(),
            'RECEIVED' => StockTransfer::where('status', 'RECEIVED')->count(),
        ];

        return view('warehouse.transfers.index', compact('transfers', 'user', 'activeLocation', 'statusCounts'));
    }

    public function create(Request $request): View
    {
        $user = $request->user();
        if (!$user->hasPermission('transfer.dispatch') && !$user->hasPermission('inventory.manage') && !$user->isSuperAdmin()) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk mengirim transfer barang.');
        }

        $activeLocation = $user->getActiveLocation() ?? Location::where('code', 'WH-CENTRAL')->first();
        $destinations = Location::where('id', '!=', $activeLocation?->id)
            ->where('type', '!=', 'TRANSIT')
            ->where('is_active', true)
            ->get();

        // Only products that have positive on-hand balance at origin location
        $availableBalances = InventoryBalance::where('location_id', $activeLocation?->id)
            ->where('on_hand_qty', '>', 0)
            ->with(['product.baseUom', 'product.productUoms.uom'])
            ->get();

        return view('warehouse.transfers.create', compact('activeLocation', 'destinations', 'availableBalances', 'user'));
    }

    public function store(StoreStockTransferRequest $request): RedirectResponse
    {
        try {
            $transfer = $this->transferService->createTransfer($request->validated(), $request->user());

            // Automatically dispatch upon submission
            $this->transferService->dispatchTransfer($transfer, $request->user());

            return redirect()->route('warehouse.transfers.show', $transfer->id)
                ->with('success', "Transfer {$transfer->transfer_number} berhasil dikirim (Status: Dalam Perjalanan). Stok asal dipotong dan berpindah ke Transit.");
        } catch (\DomainException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(StockTransfer $stockTransfer, Request $request): View
    {
        $user = $request->user();
        if (!$user->hasPermission('inventory.view') && !$user->hasPermission('transfer.dispatch') && !$user->hasPermission('transfer.receive') && !$user->isSuperAdmin() && !$user->isOwner()) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk melihat detail transfer.');
        }

        $stockTransfer->load([
            'fromLocation',
            'toLocation',
            'transitLocation',
            'creator',
            'receiver',
            'items.product.baseUom',
            'items.productUom.uom',
        ]);

        $canViewHpp = $user->hasPermission('inventory.hpp_view') || $user->isSuperAdmin() || $user->isOwner();

        return view('warehouse.transfers.show', compact('stockTransfer', 'user', 'canViewHpp'));
    }
}
