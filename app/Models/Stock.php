<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stock extends Model
{
    use HasFactory;
    protected $fillable = [
        "product_id",
        "showroom_id",
        "quantity",
        'average_purchase_price'
    ];

    /**
     * @return BelongsTo
     */
    public function showroom(){
        return $this->belongsTo(Showroom::class);
    }

    /**
     * get product details
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
