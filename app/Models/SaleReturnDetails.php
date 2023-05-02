<?php

namespace App\Models;

use App\Helpers\Converter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleReturnDetails extends Model
{
    use HasFactory;
//    protected $appends = ['total_quantity_in_format'];
    protected $fillable = [
        'sale_return_id',
        'product_id',
        'showroom_id',
        'date',
        'purchase_price',
        'return_price',
        'quantity',
        'quantity_in_unit',
        'quantity_for_price',
        'line_total',
    ];
    protected $casts = ['quantity_in_unit' => 'array'];

    /**
     * @return BelongsTo
     */
    public function saleReturn(): BelongsTo
    {
        return $this->belongsTo(SaleReturn::class);
    }
    /**
     * Product
     * @return BelongsTo
     */
    public function product(): BelongsTo
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
