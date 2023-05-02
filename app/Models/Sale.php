<?php

namespace App\Models;

use App\Scopes\ShowroomScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Sale extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $appends = ['grand_total', 'total_paid', 'sale_amount'];

    /**
     * add showroom scope for user
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new ShowroomScope);
    }

    /**
     * get sale details data
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function details()
    {
        return $this->hasMany(SaleDetails::class);
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

    /**
     * Customer details
     * @return BelongsTo
     */
    public function party()
    {
        return $this->belongsTo(Party::class);
    }

    /**
     * @return mixed|string
     */
    public function getGrandTotalAttribute(){
        return ($this->subtotal + $this->labour_cost + $this->transport_cost) - $this->discount;
    }

    /**
     * @return mixed|string
     */
    public function getSaleAmountAttribute(){
        return $this->subtotal - $this->discount;
    }

    /**
     * @return mixed|string
     */
    public function getTotalPaidAttribute(){
        return $this->paid;
    }

    /**=== Scope Start ===*/
    /**
     * Add total purchase price
     * @param Builder $query
     * @return Builder
     */
    public function scopeAddTotalPurchasePrice(Builder $query): Builder
    {
        return $query->addSelect([
            'total_purchase_price' => SaleDetails::selectRaw("IF (ISNULL(SUM(purchase_price * quantity_for_price)), 0, SUM(purchase_price * quantity_for_price))")
                ->whereColumn('sale_id', 'sales.id')
        ]);
    }

    public function scopeAddPartyName(Builder $query) :Builder
    {
        return $query->addSelect([
            'party_name' => Party::select('name')
                ->whereColumn('id', 'sales.party_id')
        ]);
    }
    /**=== Scope Start ===*/
}
