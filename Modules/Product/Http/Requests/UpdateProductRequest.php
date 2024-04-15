<?php

namespace Modules\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'product_name' => ['required', 'string', 'max:255'],
            'product_code' => ['nullable', 'string', 'max:255', 'unique:products,product_code,' . $this->product->id],
            'product_barcode_symbology' => ['nullable', 'string', 'max:255'],
            'product_unit' => ['nullable', 'string', 'max:255'],
            'product_quantity' => ['nullable', 'integer', 'min:1'],
            'product_cost' => ['required', 'numeric', 'max:2147483647'],
            'product_price' => ['required', 'numeric', 'max:2147483647'],
            'product_stock_alert' => ['nullable', 'integer', 'min:0'],
            'product_order_tax' => ['nullable', 'integer', 'min:0', 'max:100'],
            'product_tax_type' => ['nullable', 'integer'],
            'product_note' => ['nullable', 'string', 'max:1000'],
            'category_id' => ['required', 'integer'],
            'product_is_discount' => ['nullable', 'integer', 'max:2147483647'],
            'product_discount_percentage' => ['nullable', 'integer', 'min:0', 'max:100'],
            'product_discount_amount' => ['nullable', 'numeric', 'max:2147483647'],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows('edit_products');
    }
}
