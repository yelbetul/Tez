<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmployeeEmploymentDuration extends Model
{
    protected $fillable = [
        'code',
        'employment_duration'
    ];
    /**
     * Get all accidents and fatalities by employer duration for the employee employment duration.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function accidentsAndFatalitiesByEmployerDurations(): HasMany
    {
        return $this->hasMany(AccidentsAndFatalitiesByEmployerDuration::class, 'group_id', 'id');
    }

    /**
     * Get all occupational disease fatalities by employer duration for the employee employment duration.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function occDiseaseFatalitiesByEmployerDurations(): HasMany
    {
        return $this->hasMany(OccDiseaseFatalitiesByEmployerDuration::class, 'group_id', 'id');
    }
}
