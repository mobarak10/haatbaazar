<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CostCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description'
    ];

    /* ==== Cost Subcategories Start ==== */

    /**
     * Get related cost subcategories
     *
     * @return HasMany
     */
    public function costSubcategories(): HasMany
    {
        return $this->hasMany(CostSubcategory::class);
    }

    /* ==== Cost Subcategories End ==== */
}
