<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FatalWorkAccidentsByAge extends Model
{
    protected $fillable = [
        'year',
        'age_id',
        'gender',
        'work_accident_fatalities',
        'occupational_disease_fatalities',
    ];

    /**
     * Get the age that owns the FatalWorkAccidentsByAge.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function age(): BelongsTo
    {
        return $this->belongsTo(AgeCode::class, 'age_id', 'id');
    }
}
