<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Production extends Model
{
    use HasFactory;
    protected $guarded = [];

    /**
     * get production details
     * @return HasMany
     */
    public function productionDetails(): HasMany
    {
        return $this->hasMany(ProductionDetails::class);
    }

    /**
     * get from showroom group details
     * @return BelongsTo
     */
    public function fromShowroom(): BelongsTo
    {
        return $this->belongsTo(Showroom::class, 'from_showroom_id', 'id');
    }

    /**
     * get to showroom details
     * @return BelongsTo
     */
    public function toShowroom(): BelongsTo
    {
        return $this->belongsTo(Showroom::class, 'to_showroom_id', 'id');
    }

    /*=== Scope Start === */
    /**
     * Scope a query for user showroom access.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeHasUserShowroom(Builder $query): Builder
    {
        $user_showroom_id = UserShowroom::where('user_id', Auth::id())->pluck('showroom_id');
        return $query->whereIn('from_showroom_id', $user_showroom_id)->orWhereIn('to_showroom_id', $user_showroom_id);
    }

    /**
     * get production from showroom name
     * @param Builder $query
     * @return Builder
     */
    public function scopeFromShowroomName(Builder $query) :Builder
    {
        return $query->addSelect([
            'from_showroom_name' => DB::table('showrooms')->select('name')
                ->whereColumn('id', '=', 'productions.from_showroom_id')
        ]);
    }

    /**
     * get production from showroom name
     * @param Builder $query
     * @return Builder
     */
    public function scopeToShowroomName(Builder $query) :Builder
    {
        return $query->addSelect([
            'to_showroom_name' => DB::table('showrooms')->select('name')
                ->whereColumn('id', '=', 'productions.to_showroom_id')
        ]);
    }
    /*=== Scope End === */
}
