<?php

namespace App\Models;

use App\Scopes\ShowroomScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseReturn extends Model
{
    use HasFactory;
    protected $fillable = [
        'return_no',
        'user_id',
        'party_id',
        'cash_id',
        'bank_account_id',
        'date',
        'subtotal',
        'discount',
        'previous_balance',
        'paid',
        'paid_from',
        'showroom_id',
        'note',
    ];

    protected $appends = [
        'return_grand_total',
        'return_paid',
        'return_due',
    ];

    /**
     * add showroom scope for user
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new ShowroomScope);
    }

    /**
     * get purchase return details
     * @return HasMany
     */
    public function details(): HasMany
    {
        return $this->hasMany(PurchaseReturnDetail::class);
    }

    /**
     * get party details
     * @return BelongsTo
     */
    public function party(): BelongsTo
    {
        return $this->belongsTo(Party::class);
    }

    /**
     * get party details
     * @return BelongsTo
     */
    public function showroom(): BelongsTo
    {
        return $this->belongsTo(Showroom::class);
    }

    /**
     * get purchase return cash details
     * @return BelongsTo
     */
    public function cash()
    {
        return $this->belongsTo(Cash::class);
    }

    /**
     * get purchase return bank account details
     * @return BelongsTo
     */
    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }

    /**
     * calculate return grand total
     * @return void
     */
    public function getReturnGrandTotalAttribute()
    {
        return $this->subtotal - $this->discount;
    }

    /**
     * calculate return grand total
     * @return void
     */
    public function getReturnPaidAttribute()
    {
        return $this->paid;
    }

    /**
     * return due
     * @return int
     */
    public function getReturnDueAttribute()
    {
        if (($this->return_grand_total - $this->paid) > 0) {
            return $this->return_grand_total - $this->paid;
        }else{
            return 0;
        }
    }

    /**
     * get purchase return party name
     * @param Builder $query
     * @return Builder
     */
    public function scopeAddPartyName(Builder $query) :Builder
    {
        return $query->addSelect([
            'party_name' => Party::select('name')
                ->whereColumn('parties.id', 'purchase_returns.party_id')
        ]);
    }
    /*=== scope end === */
}
