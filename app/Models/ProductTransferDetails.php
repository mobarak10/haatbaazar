<?php

namespace App\Models;

use App\Helpers\Converter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductTransferDetails extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_transfer_id',
        'product_id',
        'date',
        'quantity',
        'quantity_in_unit',
    ];
    protected $casts = [
        'quantity_in_unit' => 'array'
    ];

    /**
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * get product transfer data
     * @return BelongsTo
     */
    public function productTransfer(): BelongsTo
    {
        return $this->belongsTo(ProductTransfer::class);
    }

    /**
     * get formatted quantity
     * @return array
     */
    public function getFormattedQuantityAttribute()
    {
        return Converter::convert($this->quantity, $this->product->unit_code, 'd');
    }
}
