<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SectorCode extends Model
{
    protected $fillable = [
        'sector_code',
        'group_code',
        'group_name',
        'sub_group_code',
        'sub_group_name',
        'pure_code',
        'pure_name',
    ];
    /**
     * Get all of the temporary_disability_days_by_sector for the SectorCode
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function temporary_disability_days_by_sector(): HasMany
    {
        return $this->hasMany(TemporaryDisabilityDaysBySector::class, 'group_id', 'id');
    }
    /**
     * Get all of the fatal_work_accidents_by_sector for the SectorCode
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function fatal_work_accidents_by_sector(): HasMany
    {
        return $this->hasMany(FatalWorkAccidentsBySector::class, 'group_id', 'id ');
    }
}
