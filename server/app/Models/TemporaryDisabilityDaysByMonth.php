<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TemporaryDisabilityDaysByMonth extends Model
{
    protected $fillable = [
        'year',
        'month_id',
        'gender',
        'is_outpatient',
        'is_inpatient',
        'one_day_unfit',
        'two_days_unfit',
        'three_days_unfit',
        'four_days_unfit',
        'five_or_more_days_unfit'
    ];

    /**
     * Get the month that owns the temporary disability data.
     */
    public function month(): BelongsTo
    {
        return $this->belongsTo(Month::class, 'month_id', 'id');
    }
}
