<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InjuryLocation extends Model
{
    protected $fillable = [
        'injury_location_code',
        'group_code',
        'group_name',
        'sub_group_code',
        'sub_group_name',
    ];

    /**
     * Get all accidents and fatalities for the injury location.
     */
    public function accidentsAndFatalitiesByInjuryLocation(): HasMany
    {
        return $this->hasMany(AccidentsAndFatalitiesByInjuryLocation::class, 'group_id', 'id');
    }
}
