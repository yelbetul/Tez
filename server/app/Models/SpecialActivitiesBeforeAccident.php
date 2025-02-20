<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SpecialActivitiesBeforeAccident extends Model
{
    protected $fillable = [
        'special_activity_code',
        'group_code',
        'group_name',
        'sub_group_code',
        'sub_group_name',
    ];
    /**
     * Get all accidents and fatalities related to the special activity before accident.
     */
    public function accidentsAndFatalitiesBySpecialActivityBeforeAccident(): HasMany
    {
        return $this->hasMany(AccidentsAndFatalitiesBySpecialActivityBeforeAccident::class, 'group_id', 'id');
    }
}
