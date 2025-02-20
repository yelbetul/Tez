<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccidentsAndFatalitiesByEmployerDuration extends Model
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
     * Get the employee employment duration associated with the accident and fatality by employer duration record.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employeeEmploymentDuration(): BelongsTo
    {
        return $this->belongsTo(EmployeeEmploymentDuration::class, 'group_id', 'id');
    }
}
