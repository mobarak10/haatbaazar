<?php

namespace App\QueryFilter\FromToDateFilter;

use Illuminate\Database\Eloquent\Builder;
use Closure;

class PartyIdQuery
{
    public function handle(Builder $builder, Closure $next)
    {
        if (!request()->party_id) {
            return $next($builder);
        }
        return $next($builder)->where('party_id', request()->party_id);
    }
}
