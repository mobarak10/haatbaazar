<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BankAccount extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'bank_id',
        'account_name',
        'account_number',
        'branch',
        'balance',
        'note',
    ];

    /* ==== Relationship Start ==== */

    /**
     * @return BelongsTo
     */
    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }

    /**
     * Get related expenses
     *
     * @return MorphMany
     */
    public function expenses(): MorphMany
    {
        return $this->morphMany(Expense::class, 'transactionable');
    }

    /**
     * Get related loans
     *
     * @return MorphMany
     */
    public function loans(): MorphMany
    {
        return $this->morphMany(Loan::class, 'transactionable');
    }

    /**
     * Get related loans installments
     *
     * @return MorphMany
     */
    public function loanInstallments(): MorphMany
    {
        return $this->morphMany(LoanInstallment::class, 'transactionable');
    }
    /* ==== Relationship End ==== */
}
