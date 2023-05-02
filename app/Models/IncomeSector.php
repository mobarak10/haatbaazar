<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IncomeSector extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description'
    ];

    /**
     * income records details
     * @return HasMany
     */
    public function incomeRecords(): HasMany
    {
        return $this->hasMany(IncomeRecord::class);
    }
}
