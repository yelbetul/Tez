<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccidentsAndFatalitiesByEmployee extends Model
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
     * Get the employee group associated with the accident and fatality record.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employeeGroup(): BelongsTo
    {
        return $this->belongsTo(EmployeeGroup::class, 'group_id', 'id');
    }
}
