<?php

namespace App\Http\Requests;

use App\Rules\LoanAmountLessOrEqualToTransactionableBalanceRule;
use Illuminate\Foundation\Http\FormRequest;

class LoanStoreRequest extends FormRequest
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
            'loan_account_id' => 'required|integer|exists:App\Models\LoanAccount,id',
            'showroom_id' => 'required|integer|exists:App\Models\Showroom,id',
            'date' => 'required|date',
            'expired_date' => 'required|date',
            'note' => 'nullable|string',
            'transactionable.id' => 'required|integer|' . ($this->payment_method == 'cash') ? 'exists:cashes,id' : 'exists:bank_accounts,id',
            'amount' => ['required', 'numeric', new LoanAmountLessOrEqualToTransactionableBalanceRule],
        ];
    }
}
