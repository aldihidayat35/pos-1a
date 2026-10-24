<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmergencyPurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && ($user->hasPermission('emergency.input') || $user->isSuperAdmin());
    }

    public function rules(): array
    {
        return [
            'location_id' => ['required', 'exists:locations,id'],
            'product_id' => ['required', 'exists:products,id'],
            'product_uom_id' => ['required', 'exists:product_uoms,id'],
            'qty_input' => ['required', 'numeric', 'min:0.001'],
            'unit_cost_input' => ['required', 'numeric', 'min:1'],
            'merchant_name' => ['required', 'string', 'max:150'],
            'receipt_photo' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'], // Mandatory photo proof
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'merchant_name.required' => 'Nama pedagang / kios tempat pembelian emergency wajib diisi.',
            'receipt_photo.required' => 'Foto bukti nota / struk belanja emergency WAJIB diunggah sebagai dokumen audit.',
            'receipt_photo.image' => 'File bukti nota harus berupa format gambar (JPG/PNG/WEBP).',
            'receipt_photo.max' => 'Ukuran file foto bukti maksimal 5 MB.',
            'qty_input.min' => 'Jumlah barang dibeli harus lebih besar dari 0.',
            'unit_cost_input.min' => 'Harga beli barang harus lebih besar dari 0.',
        ];
    }
}
