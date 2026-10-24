<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\StockTransfer;
use App\Services\TransferService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StoreTransferController extends Controller
{
    public function __construct(
        protected TransferService $transferService
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        if (!$user->hasPermission('transfer.receive') && !$user->hasPermission('inventory.view') && !$user->isSuperAdmin() && !$user->isOwner()) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk melihat kiriman transfer toko.');
        }

        $activeLocation = $user->getActiveLocation();
        $transfers = StockTransfer::with(['fromLocation', 'toLocation', 'creator', 'items.product'])
            ->where('to_location_id', $activeLocation?->id)
            ->orderByDesc('id')
            ->paginate(10);

        return view('store.transfers.index', compact('transfers', 'user', 'activeLocation'));
    }

    public function show(StockTransfer $stockTransfer, Request $request): View
    {
        $user = $request->user();
        if (!$user->hasPermission('transfer.receive') && !$user->isSuperAdmin() && !$user->isOwner()) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk menerima kiriman barang.');
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

        $canReceive = ($user->hasPermission('transfer.receive') || $user->isSuperAdmin())
            && in_array($stockTransfer->status, ['DISPATCHED', 'PARTIALLY_RECEIVED']);

        return view('store.transfers.receive', compact('stockTransfer', 'user', 'canReceive'));
    }

    public function receive(Request $request, StockTransfer $stockTransfer): RedirectResponse
    {
        $user = $request->user();
        if (!$user->hasPermission('transfer.receive') && !$user->isSuperAdmin()) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk menerima kiriman barang.');
        }

        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_id' => ['required', 'exists:stock_transfer_items,id'],
            'items.*.qty_received' => ['required', 'numeric', 'min:0'],
        ]);

        try {
            $this->transferService->receiveTransfer($stockTransfer, $validated['items'], $user);

            return redirect()->route('store.transfers.show', $stockTransfer->id)
                ->with('success', "Kiriman transfer {$stockTransfer->transfer_number} berhasil diterima fisik di toko! Saldo toko bertambah dan HPP lokal diperbarui.");
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
