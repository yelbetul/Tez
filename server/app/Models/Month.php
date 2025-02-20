<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Month extends Model
{
    protected $fillable = ['month_name'];

    /**
     * Get all work accidents for the month.
     */
    public function workAccidentsByMonth(): HasMany
    {
        return $this->hasMany(WorkAccidentsByMonth::class, 'month_id', 'id');
    }
    /**
     * Get all temporary disability days records for the month.
     */
    public function temporaryDisabilityDaysByMonth(): HasMany
    {
        return $this->hasMany(TemporaryDisabilityDaysByMonth::class, 'month_id', 'id');
    }
    /**
     * Get all fatal work accident records for the month.
     */
    public function fatalWorkAccidentsByMonth(): HasMany
    {
        return $this->hasMany(FatalWorkAccidentsByMonth::class, 'month_id', 'id');
    }
}
