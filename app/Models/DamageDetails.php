<?php

namespace App\Models;

use App\Helpers\Converter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DamageDetails extends Model
{
    use HasFactory;
    protected $fillable = [
        'damage_id',
        'product_id',
        'showroom_id',
        'date',
        'quantity',
        'quantity_in_unit',
        'quantity_for_price',
        'purchase_price',
    ];
    protected $casts = ['quantity_in_unit' => 'array'];
//    protected $appends = ['total_quantity'];

    /**
     * @return BelongsTo
     * get products details
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * get damage data
     * @return BelongsTo
     */
    public function damage(): BelongsTo
    {
        return $this->belongsTo(Damage::class);
    }

    /**
     * get damage product quantity in unit
     * @return mixed
     */
    public function getTotalQuantityAttribute()
    {
        return Converter::convert($this->quantity, $this->product->unit_code, 'd')['display'];
    }
}
