<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    public $timestamps = false;
    use HasFactory;
    protected $fillable = ['name', 'slug'];

    public function products(){
        return $this->hasMany(Product::class);
    }

    /*=== scope start ===*/
    public function scopeAddProductName(Builder $query) :Builder
    {
        return $query->addSelect([
            'product_name' => Product::select('products.name')
                ->whereColumn('categories.id', 'products.category_id')
                ->limit(1)
        ]);
    }
    /*=== scope end ===*/
}
