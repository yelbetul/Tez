<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccidentsAndFatalitiesByInjuryCause extends Model
{
    protected $fillable = [
        'year',
        'group_id',
        'gender',
        'works_on_accident_day',
        'unfit_on_accident_day',
        'two_days_unfit',
        'three_days_unfit',
        'four_days_unfit',
        'five_or_more_days_unfit',
        'fatalities',
    ];

    /**
     * Get the injury cause associated with the accident.
     */
    public function injuryCause(): BelongsTo
    {
        return $this->belongsTo(InjuryCause::class, 'group_id', 'id');
    }
}
