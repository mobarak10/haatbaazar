<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncomeRecord extends Model
{
    use HasFactory;
    protected $fillable = [
        'income_sector_id',
        'user_id',
        'cash_id',
        'showroom_id',
        'bank_account_id',
        'date',
        'amount',
        'income_by',
        'description',
    ];
    protected $dates = ['date'];

    /**
     * get income sector details
     * @return BelongsTo
     */
    public function incomeSector(): BelongsTo
    {
        return $this->belongsTo(IncomeSector::class);
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
     * get cash details
     * @return BelongsTo
     */
    public function cash(): BelongsTo
    {
        return $this->belongsTo(Cash::class);
    }
}
