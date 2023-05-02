<?php

namespace App\Models;

use App\Scopes\ShowroomScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Showroom extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'address',
        'comment',
    ];

    /**
     * add showroom scope for user
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new ShowroomScope);
    }

    /**
     * Get products with quantity
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function products()
    {
        return $this->belongsToMany('\App\Models\Product', 'stocks')
            ->withPivot('quantity', 'stocks.id', 'average_purchase_price')
            ->withTimestamps()
            ->as('stock');
    }

    /*=== Relationship end ===*/
}
