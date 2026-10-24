<?php

namespace App\Http\Requests\Master;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sku' => ['required', 'string', 'max:60', 'unique:products,sku'],
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:product_categories,id'],
            'base_uom_id' => ['required', 'exists:uoms,id'],
            'base_barcode' => ['nullable', 'string', 'max:80', 'unique:product_uoms,barcode'],
            'description' => ['nullable', 'string', 'max:1000'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'track_stock' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'conversions' => ['nullable', 'array'],
            'conversions.*.uom_id' => ['required_with:conversions', 'exists:uoms,id'],
            'conversions.*.factor_to_base' => ['required_with:conversions', 'numeric', 'gt:0'],
            'conversions.*.barcode' => ['nullable', 'string', 'max:80', 'unique:product_uoms,barcode'],
            'conversions.*.is_purchase' => ['nullable', 'boolean'],
            'conversions.*.is_sale' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'sku.required' => 'SKU produk wajib diisi.',
            'sku.unique' => 'SKU ini sudah digunakan oleh produk lain.',
            'name.required' => 'Nama produk wajib diisi.',
            'category_id.required' => 'Kategori produk wajib dipilih.',
            'base_uom_id.required' => 'Satuan dasar (Base UOM) wajib dipilih.',
            'base_barcode.unique' => 'Barcode satuan dasar ini sudah terdaftar di sistem.',
            'conversions.*.factor_to_base.gt' => 'Faktor konversi harus lebih besar dari 0.',
            'conversions.*.barcode.unique' => 'Barcode satuan konversi ini sudah terdaftar di sistem.',
        ];
    }
}
