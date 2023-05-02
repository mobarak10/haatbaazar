<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, HasPermissions, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'salary',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * get user metas
     * @return MorphMany
     */
    public function metas(): MorphMany
    {
        return $this->morphMany(Meta::class, 'metaable');
    }

    public function showroom()
    {
        return $this->belongsToMany(Showroom::class, 'user_showrooms');
    }

    /**
     * get user all salaries
     * @return HasMany
     */
    public function salaries(): HasMany
    {
        return $this->hasMany(Salary::class);
    }

    /** scope start */
    /**
     * get user total advanced amount
     * @param Builder $query
     * @return Builder
     */
    public function scopeAddTotalAdvancedPaidAmount(Builder $query): Builder
    {
        return $query->addSelect([
            'total_advanced_paid' => AdvancedSalary::where('amount', '<', 0)
                ->selectRaw("IF(ISNULL(SUM(amount)), 0, SUM(amount))")
                ->whereColumn('user_id', 'users.id')
        ]);
    }

    /**
     * get total advanced receive
     * @param Builder $query
     * @return Builder
     */
    public function scopeAddTotalAdvancedReceiveAmount(Builder $query): Builder
    {
        return $query->addSelect([
            'total_advanced_receive' => AdvancedSalary::where('amount', '>', 0)
                ->selectRaw("IF(ISNULL(SUM(amount)), 0, SUM(amount))")
                ->whereColumn('user_id', 'users.id')
        ]);
    }
    /** scope end */
}
