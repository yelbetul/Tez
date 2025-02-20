<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TemporaryDisabilityDaysBySector extends Model
{
    protected $fillable = [
        'year',
        'group_id',
        'gender',
        'is_outpatient',
        'is_inpatient',
        'one_day_unfit',
        'two_days_unfit',
        'three_days_unfit',
        'four_days_unfit',
        'five_or_more_days_unfit',
    ];

    /**
     * Get the sector that owns the TemporaryDisabilityDaysBySector.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sector(): BelongsTo
    {
        return $this->belongsTo(SectorCode::class, 'group_id', 'id');
    }
}
