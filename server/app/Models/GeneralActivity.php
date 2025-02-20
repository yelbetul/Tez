<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GeneralActivity extends Model
{
    protected $fillable = [
        'general_activity_code',
        'group_code',
        'group_name',
        'sub_group_code',
        'sub_group_name',
    ];
    /**
     * Get all accidents and fatalities related to the general activity.
     */
    public function accidentsAndFatalitiesByGeneralActivity(): HasMany
    {
        return $this->hasMany(AccidentsAndFatalitiesByGeneralActivity::class, 'group_id', 'id');
    }
}
