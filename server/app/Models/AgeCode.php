<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AgeCode extends Model
{
    protected $fillable = [
        'age'
    ];
    /**
     * Get all of the work_accidents_by_age for the AgeCode
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function work_accidents_by_age(): HasMany
    {
        return $this->hasMany(WorkAccidentsByAge::class, 'age_id', 'id');
    }
    /**
     * Get all of the fatal_work_accidents_by_age for the AgeCode
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function fatal_work_accidents_by_age(): HasMany
    {
        return $this->hasMany(FatalWorkAccidentsByAge::class, 'age_id', 'id ');
    }
}
