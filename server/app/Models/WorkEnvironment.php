<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkEnvironment extends Model
{
    protected $fillable = [
        'environment_code',
        'group_code',
        'group_name',
        'sub_group_code',
        'sub_group_name',
    ];
    /**
     * Get all accidents and fatalities by environment.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function accidentsAndFatalitiesByEnvironment() : HasMany
    {
        return $this->hasMany(AccidentsAndFatalitiesByEnvironment::class, 'group_id', 'id');
    }
}
