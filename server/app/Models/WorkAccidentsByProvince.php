<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkAccidentsByProvince extends Model
{
    protected $fillable = [
        'year',
        'province_id',
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
     * Get the province that owns the WorkAccidentsByProvince.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function province(): BelongsTo
    {
        return $this->belongsTo(ProvinceCode::class, 'province_id', 'id');
    }
}
