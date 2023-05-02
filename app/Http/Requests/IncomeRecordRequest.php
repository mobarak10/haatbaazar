<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IncomeRecordRequest extends FormRequest
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
            'income_sector_id' => 'required|integer',
            'showroom_id' => 'required|integer|exists:showrooms,id',
            'cash_id' => 'nullable|integer',
            'bank_account_id' => 'nullable|integer',
            'date' => 'required|date',
            'amount' => 'required|numeric',
            'income_by' => 'nullable|string',
            'description' => 'nullable|string',
        ];
    }
}
