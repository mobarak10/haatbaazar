<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DueMangeRequest extends FormRequest
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
            'party_id' => 'required|integer|exists:App\Models\Party,id',
            'cash_id' => 'nullable|integer|exists:App\Models\Cash,id',
            'bank_account_id' => 'nullable|integer|exists:App\Models\BankAccount,id',
            'showroom_id' => 'required|integer|exists:App\Models\Showroom,id',
            'date' => 'required|date',
            'type' => 'required|in:supplier,customer',
            'amount' => 'required|numeric',
            'adjustment' => 'nullable|numeric',
            'check_number' => 'nullable|numeric',
            'note' => 'nullable|string',
        ];
    }
}
