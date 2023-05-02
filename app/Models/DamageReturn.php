<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DamageReturn extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $appends = ['grand_total'];

    /**
     * get damage party details
     * @return BelongsTo
     */
    public function party(): BelongsTo
    {
        return $this->belongsTo(Party::class);
    }

    /**
     * get damage return details
     *
     * @return HasMany
     */
    public function details(): HasMany
    {
        return $this->hasMany(DamageReturnDetails::class);
    }

    /**
     * get party details
     *
     * @return BelongsTo
     */
    public function showroom(): BelongsTo
    {
        return $this->belongsTo(Showroom::class);
    }

    /**
     * get purchase return cash details
     *
     * @return BelongsTo
     */
    public function cash(): BelongsTo
    {
        return $this->belongsTo(Cash::class);
    }

    /**
     * get purchase return bank account details
     *
     * @return BelongsTo
     */
    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    /*==== accessor start ====*/
    public function getGrandTotalAttribute()
    {
        return ($this->subtotal + $this->discount);
    }
    /*==== accessor end ====*/
}
