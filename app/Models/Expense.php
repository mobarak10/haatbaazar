<?php

namespace App\Models;

use App\Scopes\ShowroomScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'cost_subcategory_id',
        'showroom_id',
        'date',
        'amount',
        'description',
        'transactionable_type',
        'transactionable_id',
    ];
    protected $dates = ['date'];

    /**
     * add showroom scope for user
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new ShowroomScope);
    }

    /* ==== Local Scope Start ==== */

    /**
     * Add payment type formatted column
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeAddPaymentMethod(Builder $query): Builder
    {
        return $query->selectSub("IF(
                    expenses.transactionable_type = 'App\\\Models\\\Cash',
                    'cash',
                    IF(
                        expenses.transactionable_type = 'App\\\Models\\\BankAccount',
                        'bank_account',
                        'unknown'
                    )
                )", 'payment_method');
    }

    /* ==== Local Scope End==== */

    /* ==== Relationship Start ==== */

    /**
     * Get associated cost subcategory
     *
     * @return BelongsTo
     */
    public function costSubcategory(): BelongsTo
    {
        return $this->belongsTo(CostSubcategory::class);
    }

    /**
     * Get transaction
     *
     * @return MorphTo
     */
    public function transactionable(): MorphTo
    {
        return $this->morphTo();
    }

    /* ==== Relationship End ==== */
}
