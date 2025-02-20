<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FatalWorkAccidentsByProvince extends Model
{
    protected $fillable = [
        'year',
        'province_id',
        'gender',
        'work_accident_fatalities',
        'occupational_disease_fatalities',
    ];

    /**
     * Get the province that owns the FatalWorkAccidentsByProvince.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function province(): BelongsTo
    {
        return $this->belongsTo(ProvinceCode::class, 'province_id', 'id');
    }
}
