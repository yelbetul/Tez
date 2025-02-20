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
     * Get all of the workAccidentsBySector for the SectorCode
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function workAccidentsBySector(): HasMany
    {
        return $this->hasMany(WorkAccidentsBySector::class, 'group_id', 'id');
    }
    /**
     * Get all of the temporaryDisabilityDaysBySector for the SectorCode
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function temporaryDisabilityDaysBySector(): HasMany
    {
        return $this->hasMany(TemporaryDisabilityDaysBySector::class, 'group_id', 'id');
    }
    /**
     * Get all of the fatalWorkAccidentsBySector for the SectorCode
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function fatalWorkAccidentsBySector(): HasMany
    {
        return $this->hasMany(FatalWorkAccidentsBySector::class, 'group_id', 'id ');
    }
}
