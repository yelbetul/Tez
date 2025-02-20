<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmployeeGroup extends Model
{
    protected $fillable = [
        'code',
        'employee_count'
    ];
    /**
     * Get all accidents and fatalities by employees for the employee group.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function accidentsAndFatalitiesByEmployees(): HasMany
    {
        return $this->hasMany(AccidentsAndFatalitiesByEmployee::class, 'group_id', 'id');
    }

    /**
     * Get all occupational disease fatalities by employees for the employee group.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function occDiseaseFatalitiesByEmployees(): HasMany
    {
        return $this->hasMany(OccDiseaseFatalitiesByEmployee::class, 'group_id', 'id');
    }
}
