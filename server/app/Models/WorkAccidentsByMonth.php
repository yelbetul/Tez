<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkAccidentsByMonth extends Model
{
    protected $fillable = [
        'year',
        'month_id',
        'gender',
        'works_on_accident_day',
        'unfit_on_accident_day',
        'two_days_unfit',
        'three_days_unfit',
        'four_days_unfit',
        'five_or_more_days_unfit',
        'occupational_disease_cases'
    ];

    /**
     * Get the month that owns the work accident.
     */
    public function month(): BelongsTo
    {
        return $this->belongsTo(Month::class, 'month_id', 'id');
    }
}
