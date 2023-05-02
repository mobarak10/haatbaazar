<?php

namespace App\Models;

use App\Helpers\Converter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\Builder;

class ProductionDetails extends Model
{
    use HasFactory;
    protected $fillable = [
        'production_id',
        'product_id',
        'date',
        'showroom_id',
        'quantity',
        'quantity_in_unit',
        'type',
    ];
    protected $casts = ['quantity_in_unit' => 'array'];
//    protected $appends = ['formatted_quantity'];

    /**
     * Supplier type
     */
    const RAW_PRODUCT = 'raw_product';
    /**
     * Customer type
     */
    const FINISH_PRODUCT = 'finish_product';

    /**
     * get production data
     * @return BelongsTo
     */
    public function production(): BelongsTo
    {
        return $this->belongsTo(Production::class);
    }

    /**
     * get product details
     * @return BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /* === Scope Start === */
    /**
     * get raw product production details
     * @param Builder $query
     * @return mixed
     */
    public function scopeRawProducts(Builder $query)
    {
        return $query->whereType(self::RAW_PRODUCT);
    }

    /**
     * get finish product production details
     * @param Builder $query
     * @return mixed
     */
    public function scopeFinishProducts(Builder $query)
    {
        return $query->whereType(self::FINISH_PRODUCT);
    }
    /** === Scope End === */

    /**
     * get formatted quantity
     * @return array
     */
    public function getFormattedQuantityAttribute()
    {
        return Converter::convert($this->quantity, $this->product->unit_code, 'd');
    }
}
