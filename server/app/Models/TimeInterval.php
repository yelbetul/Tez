<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TimeInterval extends Model
{
    protected $fillable = [
        'code',
        'time_interval'
    ];
    /**
     * Get all work accidents by time interval.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function workAccidentsByHours(): HasMany
    {
        return $this->hasMany(WorkAccidentByHour::class, 'group_id', 'id');
    }
}
