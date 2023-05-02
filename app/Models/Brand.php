<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'slug'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /*=== scope start ===*/
    public function scopeAddProductName(Builder $query) :Builder
    {
        return $query->addSelect([
            'product_name' => Product::select('products.name')
                    ->whereColumn('brands.id', 'products.brand_id')
                    ->limit(1)
        ]);
    }
    /*=== scope end ===*/
}
