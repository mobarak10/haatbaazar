<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DamageReturnDetails extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $casts = ['quantity_in_unit' => 'array'];

    /**
     * get product details
     *
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * get purchase return data
     *
     * @return BelongsTo
     */
    public function damageReturn(): BelongsTo
    {
        return $this->belongsTo(DamageReturnDetails::class);
    }
}
