<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdvancedSalaryRequest extends FormRequest
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
            'user_id' => 'required|integer|exists:App\Models\User,id',
            'date' => 'required|date',
            'amount' => 'required|numeric',
            'cash_id' => 'nullable|integer|exists:App\Models\Cash,id',
            'bank_account_id' => 'nullable|integer|exists:App\Models\BankAccount,id',
            'showroom_id' => 'nullable|integer|exists:App\Models\Showroom,id',
            'note' => 'nullable|string',
        ];
    }
}
