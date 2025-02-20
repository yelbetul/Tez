<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProvinceCode extends Model
{
    protected $fillable = [
        'province_code',
        'province_name',
    ];
    /**
     * Get all of the workAccidentsByProvince for the ProvinceCode
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function workAccidentsByProvince(): HasMany
    {
        return $this->hasMany(WorkAccidentsByProvince::class, 'group_id', 'id');
    }
    /**
     * Get all of the temporaryDisabilityDaysByProvince for the ProvinceCode
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function temporaryDisabilityDaysByProvince(): HasMany
    {
        return $this->hasMany(TemporaryDisabilityDaysByProvince::class, 'province_id', 'id');
    }
    /**
     * Get all of the fatalWorkAccidentsByProvince for the ProvinceCode
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function fatalWorkAccidentsByProvince(): HasMany
    {
        return $this->hasMany(FatalWorkAccidentsByProvince::class, 'province_id', 'id ');
    }
    /**
     * Get all of the disabilityDaysOccupationalDiseasesByProvince for the ProvinceCode
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function disabilityDaysOccupationalDiseasesByProvince(): HasMany
    {
        return $this->hasMany(DisabilityDaysOccupationalDiseasesByProvince::class, 'province_id', 'id ');
    }
}
