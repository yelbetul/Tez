<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkAccidentsByAge extends Model
{
    protected $fillable = [
        'year',
        'age_id',
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
     * Get the age that owns the WorkAccidentsByAge.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function age(): BelongsTo
    {
        return $this->belongsTo(AgeCode::class, 'age_id', 'id');
    }
}
