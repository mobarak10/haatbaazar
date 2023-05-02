<?php

namespace App\Models;

use App\Scopes\ShowroomScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Party extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * add showroom scope for user
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new ShowroomScope);
    }

    /**
     * Supplier type
     */
    const GENUS_SUPPLIER = 'supplier';
    /**
     * Customer type
     */
    const GENUS_CUSTOMER = 'customer';

    protected $fillable = [
        'genus',
        'name',
        'company_name',
        'description',
        'phone',
        'email',
        'address',
        'mokam_id',
        'showroom_id',
        'photo',
        'balance',
        'active',
    ];
//    protected $appends = ['custom_name'];

    /* === Scope start === */
    /**
     * Customer
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeCustomer(Builder $query): Builder
    {
        return $query->whereGenus(self::GENUS_CUSTOMER);
    }

    /**
     * Supplier
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeSupplier(Builder $query): Builder
    {
        return $query->whereGenus(self::GENUS_SUPPLIER);
    }

    public function scopeAddCustomName(Builder $query) :Builder
    {
        return $query->addSelect([
            'custom_name' => Mokam::selectRaw('CONCAT(parties.name, " (", mokams.name, ")")')
                    ->whereColumn('id', 'parties.mokam_id')
                    ->limit(1)
        ]);
    }

    /*=== Scope end ===*/

    /**
     * Get all of the ledgers for the party.
     */

    /**
     * get mokam details
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function mokam()
    {
        return $this->belongsTo(Mokam::class);
    }

    /*=== accessor start === */
    /*=== accessor end === */
}
