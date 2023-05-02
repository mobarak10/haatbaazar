<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DamageReturnRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'paid_to' => 'required|string',
            'party_id' => 'required|integer|exists:App\Models\Party,id',
            'date' => 'required|date',
            'paid' => 'nullable|numeric',
            'showroom_id' => 'required|integer|exists:App\Models\Showroom,id',
            'cash_id' => 'nullable|integer|exists:App\Models\Cash,id',
            'subtotal' => 'required|numeric',
            'bank_account_id' => 'nullable|integer|exists:App\Models\BankAccount,id',
            'discount' => 'nullable|numeric',
            'note' => 'nullable|string',
            'previous_balance' => 'nullable|numeric',
        ];
    }
}
