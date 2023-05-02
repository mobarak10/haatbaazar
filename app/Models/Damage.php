<?php

namespace App\Models;

use App\Scopes\ShowroomScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Damage extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'showroom_id',
        'damage_no',
        'date',
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
     * get damage product details
     * @return HasMany
     */
    public function details(): HasMany
    {
        return $this->hasMany(DamageDetails::class);
    }

    /**
     * get showroom details
     * @return BelongsTo
     */
    public function showroom(): BelongsTo
    {
        return $this->belongsTo(Showroom::class);
    }
}
