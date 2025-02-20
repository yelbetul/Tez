<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InjuryType extends Model
{
    protected $filable = [
        'injury_code',
        'group_code',
        'group_name',
        'sub_group_code',
        'sub_group_name',
    ];
    /**
     * Get all accidents and fatalities for the injury type.
     */
    public function accidentsAndFatalitiesByInjury(): HasMany
    {
        return $this->hasMany(AccidentsAndFatalitiesByInjury::class, 'group_id', 'id');
    }
}
