<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkAccidentsBySector extends Model
{
    protected $fillable = [
        'year',
        'group_id',
        'gender',
        'works_on_accident_day',
        'unfit_on_accident_day',
        'two_days_unfit',
        'three_days_unfit',
        'four_days_unfit',
        'five_or_more_days_unfit',
        'occupational_disease_cases',
    ];

    /**
     * Get the sector that owns the WorkAccidentsBySector.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sector(): BelongsTo
    {
        return $this->belongsTo(SectorCode::class, 'group_id', 'id');
    }
}
