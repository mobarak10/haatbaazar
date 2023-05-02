<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductTransfer extends Model
{
    use HasFactory;
    protected $fillable = [
        'date',
        'transfer_no',
        'from_showroom_id',
        'to_showroom_id',
        'comment',
        'user_id',
    ];


    /**
     * get from showroom details
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

    /**
     * get user details
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * get transfer product details
     * @return HasMany
     */
    public function productTransferDetails(): HasMany
    {
        return $this->hasMany(ProductTransferDetails::class);
    }

    /*=== Scope End === */
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
                ->whereColumn('id', '=', 'product_transfers.from_showroom_id')
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
                ->whereColumn('id', '=', 'product_transfers.to_showroom_id')
        ]);
    }

    /**
     * get production from showroom name
     * @param Builder $query
     * @return Builder
     */
    public function scopeUserName(Builder $query) :Builder
    {
        return $query->addSelect([
            'user_name' => User::select('name')
                ->whereColumn('id', '=', 'product_transfers.user_id')
        ]);
    }
    /*=== Scope End === */
}
