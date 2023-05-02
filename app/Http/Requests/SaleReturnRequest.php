<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaleReturnRequest extends FormRequest
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
            'showroom_id' => 'required|integer',
            'cash_id' => 'nullable|integer',
            'bank_account_id' => 'nullable|integer',
            'date' => 'required|date',
            'subtotal' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'party_id' => 'required|integer',
            'due' => 'nullable|numeric',
            'paid' => 'nullable|numeric',
            'previous_balance' => 'nullable|numeric',
            'note' => 'nullable|string',
        ];
    }
}
