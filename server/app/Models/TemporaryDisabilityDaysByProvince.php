<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TemporaryDisabilityDaysByProvince extends Model
{
    protected $fillable = [
        'year',
        'province_id',
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
     * Get the province that owns the TemporaryDisabilityDaysByProvince.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function province(): BelongsTo
    {
        return $this->belongsTo(ProvinceCode::class, 'province_id', 'id');
    }
}
