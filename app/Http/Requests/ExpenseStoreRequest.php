<?php

namespace App\Http\Requests;

use App\Rules\ExpenseMaximumAmountLessOrEqualToTransactionableBalanceRule;
use Illuminate\Foundation\Http\FormRequest;

class ExpenseStoreRequest extends FormRequest
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
            'cost_subcategory_id' => 'required|integer|exists:cost_subcategories,id',
            'showroom_id' => 'nullable|integer|exists:showrooms,id',
            'date' => 'required|date',
            'description' => 'nullable',
            'payment_method' => 'in:cash,bank_account',
            // 'cash.id' => 'exclude_if:payment_method,bank_account|required|integer|exists:cashes,id',
            // 'bank_account.id' => 'exclude_if:payment_method,cash|required|integer|exists:bank_accounts,id',
            'transactionable.id' => 'required|integer|' . ($this->payment_method == 'cash') ? 'exists:cashes,id' : 'exists:bank_accounts,id',
            'amount' => ['required', 'numeric', 'min:1', new ExpenseMaximumAmountLessOrEqualToTransactionableBalanceRule],
        ];
    }
}
