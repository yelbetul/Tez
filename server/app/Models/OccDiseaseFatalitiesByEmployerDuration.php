<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OccDiseaseFatalitiesByEmployerDuration extends Model
{
    protected $fillable = [
        'year',
        'group_id',
        'gender',
        'occ_disease_cases',
        'occ_disease_fatalities',
    ];

    /**
     * Get the employee employment duration associated with the occupational disease fatality by employer duration record.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employeeEmploymentDuration(): BelongsTo
    {
        return $this->belongsTo(EmployeeEmploymentDuration::class, 'group_id', 'id');
    }
}
