<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaleStoreRequest extends FormRequest
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
            'payment_type' => 'required|string',
            'showroom_id' => 'required|integer',
            'cash_id' => 'nullable|integer',
            'bank_account_id' => 'nullable|integer',
            'date' => 'required|date',
            'subtotal' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'transport_cost' => 'nullable|numeric',
            'labour_cost' => 'nullable|numeric',
            'due' => 'nullable|numeric',
            'change' => 'nullable|numeric',
            'discount_type' => 'nullable|string',
            'paid' => 'nullable|numeric',
            'previous_balance' => 'nullable|numeric',
            'note' => 'nullable|string',
        ];
    }
}
