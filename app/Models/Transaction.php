<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;
    protected $fillable = [
        'date',
        'showroom_id',
        'user_id',
        'transaction_from',
        'transaction_from_id',
        'transaction_to',
        'transaction_to_id',
        'amount',
        'note',
    ];
    protected $appends = ['from_transaction', 'to_transaction'];

    /**
     * transaction from details // cash/bank account
     */
    public function getFromTransactionAttribute()
    {
        if ($this->transaction_from == 'cash'){
            return $this->belongsTo(Cash::class, 'transaction_from_id', 'id')->first();
        }else{
            return $this->belongsTo(BankAccount::class, 'transaction_from_id', 'id')->first();
        }
    }

    /**
     * transaction to details // cash/bank account
     */
    public function getToTransactionAttribute()
    {
        if ($this->transaction_to == 'cash'){
            return $this->belongsTo(Cash::class, 'transaction_to_id', 'id')->first();
        }else{
            return $this->belongsTo(BankAccount::class, 'transaction_to_id', 'id')->with('bank')->first();
        }
    }
}
