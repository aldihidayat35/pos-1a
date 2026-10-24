<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReceiveGoodsReceiptRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && ($user->hasPermission('purchasing.receive') || $user->isSuperAdmin());
    }

    public function rules(): array
    {
        return [
            'supplier_do_number' => ['required', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.purchase_order_item_id' => ['required', 'exists:purchase_order_items,id'],
            'items.*.qty_input' => ['nullable', 'numeric', 'min:0'],
            'items.*.unit_cost_input' => ['nullable', 'numeric', 'min:0'],
            'items.*.batch_number' => ['nullable', 'string', 'max:100'],
            'items.*.expiry_date' => ['nullable', 'date'],
            'items.*.notes' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'supplier_do_number.required' => 'Nomor Surat Jalan (DO) fisik dari vendor / suplayer wajib diisi.',
            'items.required' => 'Daftar barang penerimaan tidak boleh kosong.',
            'items.*.qty_input.min' => 'Jumlah barang diterima tidak boleh negatif.',
        ];
    }
}
