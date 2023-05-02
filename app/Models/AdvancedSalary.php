<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdvancedSalary extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'showroom_id',
        'cash_id',
        'salary_id',
        'bank_account_id',
        'date',
        'amount',
        'note',
    ];

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
}
