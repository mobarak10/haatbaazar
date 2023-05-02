<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CostSubcategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'cost_category_id',
        'name',
        'slug',
        'description',
    ];

    /* ==== Relationship Start ==== */

    /**
     * Get associated cost category
     *
     * @return BelongsTo
     */
    public function costCategory(): BelongsTo
    {
        return $this->belongsTo(CostCategory::class);
    }


    /**
     * Get related expenses
     *
     * @return HasMany
     */
    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    /* ==== Relationship End ==== */
}
