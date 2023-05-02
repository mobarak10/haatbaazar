<?php

namespace App\Models;

use App\Scopes\ShowroomScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SaleReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'return_no',
        'user_id',
        'party_id',
        'showroom_id',
        'date',
        'subtotal',
        'discount',
        'paid',
        'due',
        'paid_from',
        'previous_balance',
        'cash_id',
        'bank_account_id',
        'note',
    ];
    protected $appends = [
        'return_grand_total',
        'return_paid',
        'return_due'
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
     * get sale return details
     * @return HasMany
     */
    public function details(): HasMany
    {
        return $this->hasMany(SaleReturnDetails::class);
    }

    /**
     * get showroom details
     * @return BelongsTo
     */
    public function showroom()
    {
        return $this->belongsTo(Showroom::class);
    }

    /**
     * calculate sale return grand total
     * @return void
     */
    public function getReturnGrandTotalAttribute()
    {
        return ($this->subtotal - $this->discount);
    }

    /**
     * calculate sale return grand total
     * @return void
     */
    public function getReturnPaidAttribute()
    {
        return $this->paid;
    }

    public function getReturnDueAttribute()
    {
        if ($this->return_grand_total >= $this->paid){
            return $this->return_grand_total - $this->paid;
        }else{
            return 0;
        }
    }

    /**
     * Customer details
     * @return BelongsTo
     */
    public function party()
    {
        return $this->belongsTo(Party::class);
    }

    /**
     * get cash details
     * @return BelongsTo
     */
    public function cash()
    {
        return $this->belongsTo(Cash::class);
    }

    /**
     * get bank account details
     * @return BelongsTo
     */
    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }

    /*=== scope start === */
    /**
     * Add total purchase price for sale return
     * @param Builder $query
     * @return Builder
     */
    public function scopeAddTotalPurchasePrice(Builder $query): Builder
    {
        return $query->addSelect([
            'total_purchase_price' => SaleReturnDetails::selectRaw("IF (ISNULL(SUM(purchase_price * quantity_for_price)), 0, SUM(purchase_price * quantity_for_price))")
                ->whereColumn('sale_return_id', 'sale_returns.id')
        ]);
    }

    /**
     * get sale return party name
     * @param Builder $query
     * @return Builder
     */
    public function scopeAddPartyName(Builder $query) :Builder
    {
        return $query->addSelect([
            'party_name' => Party::select('name')
                ->whereColumn('parties.id', 'sale_returns.party_id')
        ]);
    }
    /*=== scope end === */
}
