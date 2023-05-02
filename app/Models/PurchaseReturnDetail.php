<?php

namespace App\Models;

use App\Helpers\Converter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseReturnDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'purchase_return_id',
        'product_id',
        'showroom_id',
        'date',
        'quantity',
        'quantity_in_unit',
        'return_price',
        'line_total',
    ];
    protected $casts = ['quantity_in_unit' => 'array'];
//    protected $appends = ['total_quantity_in_format'];

    /**
     * get product details
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * get purchase return data
     * @return BelongsTo
     */
    public function purchaseReturn(): BelongsTo
    {
        return $this->belongsTo(PurchaseReturn::class);
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
