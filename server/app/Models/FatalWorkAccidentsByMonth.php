<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FatalWorkAccidentsByMonth extends Model
{
    protected $fillable = [
        'year',
        'month_id',
        'gender',
        'work_accident_fatalities',
        'occupational_disease_fatalities'
    ];

    /**
     * Get the month that owns the fatal work accident data.
     */
    public function month(): BelongsTo
    {
        return $this->belongsTo(Month::class, 'month_id', 'id');
    }
}
