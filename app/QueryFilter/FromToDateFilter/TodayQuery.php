<?php

namespace App\QueryFilter\FromToDateFilter;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Closure;

class TodayQuery
{
    public function handle(Builder $builder, Closure $next)
    {
        $today_date = Carbon::today();
        if (!request()->search){
            return $next($builder)->where('date', $today_date);
        }
        return $next($builder);
    }
}
