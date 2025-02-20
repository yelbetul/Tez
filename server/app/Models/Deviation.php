<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Deviation extends Model
{
    protected $fillable = [
        'deviation_code',
        'group_code',
        'group_name',
        'sub_group_code',
        'sub_group_name',
    ];
    /**
     * Get all accidents and fatalities related to the deviation.
     */
    public function accidentsAndFatalitiesByDeviation(): HasMany
    {
        return $this->hasMany(AccidentsAndFatalitiesByDeviation::class, 'group_id', 'id');
    }
}
