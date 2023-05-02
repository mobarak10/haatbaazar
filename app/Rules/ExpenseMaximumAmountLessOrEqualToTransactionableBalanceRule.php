<?php

namespace App\Rules;

use App\Models\Cash;
use App\Models\BankAccount;
use Exception;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Log;

class ExpenseMaximumAmountLessOrEqualToTransactionableBalanceRule implements Rule
{

    protected $message;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->setMessageDefault();
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $trasactionable = null;

        // identify payment method
        try {
            if (request('payment_method') == 'cash') {
                $trasactionable = Cash::findOrFail(request()['transactionable']['id']);
            } else {
                $trasactionable = BankAccount::findOrFail(request()['transactionable']['id']);
            }
        } catch (Exception $exception) {
            $this->setMessageForNoPaymentMethodFound();
            Log::error($exception->getMessage(), [
                'File name' => get_class($this),
            ]);
        }

        if ($trasactionable == null) {
            $this->setMessageForNoPaymentMethodFound();
        }

        return $value <= ($trasactionable->balance ?? 0);
    }

    protected function setMessageForNoPaymentMethodFound()
    {
        $this->message = 'No payment method found.';
    }

    protected function setMessageDefault()
    {
        $this->message = 'The amount is insufficient.';
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }
}
