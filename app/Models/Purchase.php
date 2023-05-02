<?php

namespace App\Models;

use App\Scopes\ShowroomScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Purchase extends Model
{
    use HasFactory;
    protected $fillable = [
        'party_id',
        'payment_type',
        'cash_id',
        'showroom_id',
        'bank_account_id',
        'voucher_no',
        'date',
        'subtotal',
        'discount',
        'discount_type',
        'paid',
        'previous_balance',
        'note',
    ];

    protected $dates = ['date'];
    protected $appends = ['grand_total'];

    /**
     * add showroom scope for user
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new ShowroomScope);
    }

    /**
     * Purchase details
     * @return HasMany
     */
    public function details()
    {
        return $this->hasMany(PurchaseDetails::class);
    }

    /**
     * get showroom group details
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
    public function cash(): BelongsTo
    {
        return $this->belongsTo(Cash::class);
    }

    /**
     * get bank account details
     * @return BelongsTo
     */
    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    /**
     * purchase cost details
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function purchaseCost()
    {
        return $this->hasOne(PurchaseCost::class);
    }

    public function getGrandTotalAttribute(){
        return $this->subtotal - $this->discount;
    }


    /**
     * Supplier
     * @return BelongsTo
     */
    public function party()
    {
        return $this->belongsTo(Party::class);
    }

    /*=== Scope Start */
    /**
     * get purchase party name
     * @param Builder $query
     * @return Builder
     */
    public function scopeAddPartyName(Builder $query) :Builder
    {
        return $query->addSelect([
            'party_name' => Party::select('name')
                ->whereColumn('party_id', 'parties.id')
        ]);
    }
    /*=== Scope End */
}
