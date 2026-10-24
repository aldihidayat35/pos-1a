<?php

namespace App\Http\Requests\Master;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product') instanceof \App\Models\Product
            ? $this->route('product')->id
            : $this->route('product');

        $baseProductUomId = null;
        if ($productId) {
            $baseProductUomId = \App\Models\ProductUom::where('product_id', $productId)->where('is_base', true)->value('id');
        }

        return [
            'sku' => ['required', 'string', 'max:60', Rule::unique('products', 'sku')->ignore($productId)],
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:product_categories,id'],
            'base_uom_id' => ['required', 'exists:uoms,id'],
            'base_barcode' => ['nullable', 'string', 'max:80', Rule::unique('product_uoms', 'barcode')->ignore($baseProductUomId)],
            'description' => ['nullable', 'string', 'max:1000'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'remove_image' => ['nullable', 'boolean'],
            'track_stock' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'conversions' => ['nullable', 'array'],
            'conversions.*.uom_id' => ['required_with:conversions', 'exists:uoms,id'],
            'conversions.*.factor_to_base' => ['required_with:conversions', 'numeric', 'gt:0'],
            'conversions.*.barcode' => ['nullable', 'string', 'max:80'],
            'conversions.*.is_purchase' => ['nullable', 'boolean'],
            'conversions.*.is_sale' => ['nullable', 'boolean'],
        ];
    }
}
