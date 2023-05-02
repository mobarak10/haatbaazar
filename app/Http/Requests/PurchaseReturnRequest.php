<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseReturnRequest extends FormRequest
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
            'paid_from' => 'required|string',
            'party_id' => 'required|integer',
            'date' => 'required|date',
            'paid' => 'nullable|numeric',
            'showroom_id' => 'required|integer',
            'cash_id' => 'nullable|integer',
            'subtotal' => 'required|numeric',
            'bank_account_id' => 'nullable|integer',
            'discount' => 'nullable|numeric',
            'note' => 'nullable|string',
            'previous_balance' => 'nullable|numeric'
        ];
    }
}
