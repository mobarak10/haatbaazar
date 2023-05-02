<?php

namespace App\QueryFilter\FromToDateFilter;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Closure;

class FromToDateQuery
{
    public function handle(Builder $builder, Closure $next)
    {
        $today_date = Carbon::today();
        if (request()->from_date || request()->to_date) {
            if (\request()->from_date) {
                if (\request()->to_date){
                    return $next($builder)->whereBetween('date', [\request()->from_date, \request()->to_date]);
                }else{
                    return $next($builder)->whereBetween('date', [\request()->from_date, $today_date]);
                }
            }
            if (\request()->to_date) {
                if (\request()->from_date){
                    return $next($builder)->whereBetween('date', [\request()->from_date, \request()->to_date]);
                }else{
                    return $next($builder)->whereBetween('date', [$today_date, \request()->to_date]);
                }
            }
        }else{
            return $next($builder);
        }
    }
}
