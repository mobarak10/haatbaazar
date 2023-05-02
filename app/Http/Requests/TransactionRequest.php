<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
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
            'date' => 'required|date',
            'showroom_id' => 'required|integer|exists:App\Models\Showroom,id',
            'transaction_from' => 'required|string',
            'transaction_from_id' => 'required|integer|'.($this->transaction_from == 'cash') ? 'exists:cashes,id' : 'exists:bank_accounts,id',
            'transaction_to' => 'required|string',
            'transaction_to_id' => 'required|integer|'.($this->transaction_to == 'cash') ? 'exists:cashes,id' : 'exists:bank_accounts,id',
            'amount' => 'required|numeric',
            'note' => 'nullable|string',
        ];
    }

    /**
     *
     * @return string[]
     */
    public function attributes()
    {
        return [
            'showroom_id' => 'showroom',
        ];
    }
}
