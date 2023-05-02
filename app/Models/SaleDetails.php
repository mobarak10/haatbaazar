<?php

namespace App\Models;

use App\Helpers\Converter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleDetails extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $casts = ['quantity_in_unit' => 'array'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sale()
    {
        return $this->belongsTo(Sale::class);
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
     * get quantity in formatted
     * @return mixed
     */
    public function getTotalQuantityAttribute()
    {
        return Converter::convert($this->quantity, $this->product->unit_code, 'd')['display'];
    }
}
