<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStockTransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && ($user->hasPermission('transfer.dispatch') || $user->hasPermission('inventory.manage') || $user->isSuperAdmin());
    }

    public function rules(): array
    {
        return [
            'from_location_id' => ['required', 'exists:locations,id'],
            'to_location_id' => ['required', 'exists:locations,id', 'different:from_location_id'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.product_uom_id' => ['required', 'exists:product_uoms,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.001'],
            'items.*.notes' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'to_location_id.different' => 'Lokasi tujuan transfer tidak boleh sama dengan lokasi asal.',
            'items.required' => 'Minimal satu barang harus disertakan dalam pengiriman transfer.',
            'items.*.quantity.min' => 'Jumlah barang transfer harus lebih besar dari 0.',
        ];
    }
}
