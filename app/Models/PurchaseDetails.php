<?php

namespace App\Models;

use App\Helpers\Converter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseDetails extends Model
{
    use HasFactory;
    protected $fillable = [
        'purchase_id',
        'product_id',
        'showroom_id',
        'date',
        'quantity',
        'quantity_in_unit',
        'purchase_price',
        'line_total',
    ];

    protected $casts = ['quantity_in_unit' => 'array'];
//    protected $appends = ['total_quantity_in_format'];

    /**
     * get purchase data
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function purchase()
    {
        return $this->belongsTo('\App\Models\Purchase');
    }

    /**
     * Product
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * calculate quantity in unit format
     * @return mixed
     */
    public function getTotalQuantityInFormatAttribute()
    {
        return Converter::convert($this->quantity, $this->product->unit_code, 'd')['display'];
    }

}
