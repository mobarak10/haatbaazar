<?php
namespace App\Scopes;

use App\Models\Showroom;
use App\Models\UserShowroom;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class ShowroomScope implements Scope
{

    /**
     * Apply the scope to a given Eloquent query builder.
     * @param Builder $builder
     * @param Model $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $user_showroom_id = UserShowroom::where('user_id', Auth::id())->pluck('showroom_id');
        if ($model instanceof Showroom){
            $builder->whereIn('showrooms.id', $user_showroom_id);
        }else{
            $builder->whereIn('showroom_id', $user_showroom_id);
        }
    }
}
