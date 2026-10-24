<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReceiveGoodsReceiptRequest;
use App\Models\GoodsReceipt;
use App\Models\PurchaseOrder;
use App\Services\PurchasingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GoodsReceiptController extends Controller
{
    public function __construct(
        protected PurchasingService $purchasingService
    ) {}

    public function create(PurchaseOrder $purchaseOrder, Request $request): View
    {
        $user = $request->user();
        if (!$user->hasPermission('purchasing.receive') && !$user->isSuperAdmin()) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk mencatat penerimaan fisik barang.');
        }

        if (!in_array($purchaseOrder->status, ['ISSUED', 'PARTIALLY_RECEIVED'])) {
            return redirect()->route('warehouse.po.show', $purchaseOrder->id)
                ->with('error', "Purchase Order {$purchaseOrder->po_number} berstatus {$purchaseOrder->status} dan tidak dapat menerima barang lagi.");
        }

        $purchaseOrder->load(['supplier', 'destinationLocation', 'items.product.baseUom', 'items.productUom.uom']);
        $canViewHpp = $user->hasPermission('inventory.hpp_view') || $user->isSuperAdmin() || $user->isOwner();

        return view('warehouse.receipts.create', compact('purchaseOrder', 'user', 'canViewHpp'));
    }

    public function store(ReceiveGoodsReceiptRequest $request, PurchaseOrder $purchaseOrder): RedirectResponse
    {
        try {
            $goodsReceipt = $this->purchasingService->receiveGoodsReceipt(
                $purchaseOrder,
                $request->validated(),
                $request->user()
            );

            return redirect()->route('warehouse.receipts.show', $goodsReceipt->id)
                ->with('success', "Penerimaan fisik barang {$goodsReceipt->receipt_number} berhasil dibukukan ke saldo gudang dan kartu stok!");
        } catch (\DomainException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(GoodsReceipt $goodsReceipt, Request $request): View
    {
        $user = $request->user();
        if (!$user->hasPermission('inventory.view') && !$user->hasPermission('purchasing.receive') && !$user->isSuperAdmin() && !$user->isOwner()) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk melihat bukti penerimaan barang.');
        }

        $goodsReceipt->load([
            'purchaseOrder',
            'supplier',
            'location',
            'creator',
            'items.product.baseUom',
            'items.productUom.uom',
        ]);

        $canViewHpp = $user->hasPermission('inventory.hpp_view') || $user->isSuperAdmin() || $user->isOwner();

        return view('warehouse.receipts.show', compact('goodsReceipt', 'user', 'canViewHpp'));
    }
}
