<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait UseFilterAble {

    /**
     * Scope query for filtering data
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $showBy
     * @return $query
     * @krismonsemanas
    */
    public function scopeFilterable(Builder $query, string $showBy = 'year') : Builder
    {
        $now = now();
        $startOfWeek =  $now->copy()->startOfWeek()->format('Y-m-d');
        $endOfWeek =  $now->copy()->endOfWeek()->format('Y-m-d');

        switch ($showBy) {
            case 'year':
                $query->whereYear('date', $now->year);
                break;
            case 'month':
                $query->whereYear('date', $now->year)
                    ->whereMonth('date', $now->month);
                break;
            case 'week':
                $query->whereBetween('date',[$startOfWeek, $endOfWeek]);
                break;
            case 'day':
                $query->whereDate('date', $now->format('Y-m-d'));
                break;

            default:
                $query->whereYear('date', $now->year);
                break;
        }

        return $query;
    }
}
