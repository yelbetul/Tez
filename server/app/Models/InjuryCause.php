<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InjuryCause extends Model
{
    protected $fillable = [
        'injury_cause_code',
        'group_code',
        'group_name',
        'sub_group_code',
        'sub_group_name',
    ];
    /**
     * Get all accidents and fatalities caused by the injury cause.
     */
    public function accidentsAndFatalitiesByInjuryCause(): HasMany
    {
        return $this->hasMany(AccidentsAndFatalitiesByInjuryCause::class, 'group_id', 'id');
    }
}
