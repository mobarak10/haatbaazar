<?php

namespace App\Models;

use App\Scopes\ShowroomScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cash extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'title',
        'slug',
        'showroom_id',
        'balance',
    ];

    /**
     * add showroom scope for user
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new ShowroomScope);
    }

    /* ==== Relationship Start ==== */

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
