<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OccDiseaseFatalitiesByEmployee extends Model
{
    protected $fillable = [
        'year',
        'group_id',
        'gender',
        'occ_disease_cases',
        'occ_disease_fatalities',
    ];

    /**
     * Get the employee group associated with the occupational disease fatality record.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employeeGroup(): BelongsTo
    {
        return $this->belongsTo(EmployeeGroup::class, 'group_id', 'id');
    }
}
