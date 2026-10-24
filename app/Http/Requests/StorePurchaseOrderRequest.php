<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && ($user->hasPermission('purchasing.po') || $user->hasPermission('inventory.manage') || $user->isSuperAdmin());
    }

    public function rules(): array
    {
        return [
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'destination_location_id' => ['required', 'exists:locations,id'],
            'ordered_at' => ['nullable', 'date'],
            'expected_delivery_at' => ['nullable', 'date', 'after_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.product_uom_id' => ['required', 'exists:product_uoms,id'],
            'items.*.quantity_ordered' => ['required', 'numeric', 'min:0.001'],
            'items.*.unit_purchase_price' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'supplier_id.required' => 'Pemasok / suplayer wajib dipilih.',
            'destination_location_id.required' => 'Gudang tujuan penerimaan wajib dipilih.',
            'items.required' => 'Minimal satu barang harus dipesan pada PO.',
            'items.min' => 'Minimal satu barang harus dipesan pada PO.',
            'items.*.product_id.required' => 'Produk wajib dipilih.',
            'items.*.product_uom_id.required' => 'Satuan UOM wajib dipilih.',
            'items.*.quantity_ordered.min' => 'Jumlah pesanan harus lebih besar dari 0.',
            'items.*.unit_purchase_price.min' => 'Harga beli satuan tidak boleh bernilai negatif.',
        ];
    }
}
