<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string',
            'unit_id' => 'required|integer',
            'category_id' => 'required|integer',
            'brand_id' => 'required|integer',
            'sale_price' => 'required|numeric',
            'purchase_price' => 'required|numeric',
            'price_type' => 'required|numeric',
            'stock_alert' => 'nullable|numeric',
            'quantity_in_unit' => 'nullable|array',
            'type' => 'required|string',
            'barcode' => 'nullable|string',
            'status' => 'required',
        ];
    }
}
