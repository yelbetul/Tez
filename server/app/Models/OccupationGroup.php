<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OccupationGroup extends Model
{
    protected $fillable = [
        'code',
        'occupation_code',
        'occupation_name',
        'group_code',
        'group_name',
        'sub_group_code',
        'sub_group_name',
        'pure_code',
        'pure_name'
    ];

    /**
     * Get all occupation-related disease fatalities for this occupation group.
     */
    public function occDiseaseFatalitiesByOccupations(): HasMany
    {
        return $this->hasMany(OccDiseaseFatalitiesByOccupation::class, 'group_id', 'id');
    }

    /**
     * Get all accidents and fatalities for this occupation group.
     */
    public function accidentsAndFatalitiesByOccupations(): HasMany
    {
        return $this->hasMany(AccidentsAndFatalitiesByOccupation::class, 'group_id', 'id');
    }
}
