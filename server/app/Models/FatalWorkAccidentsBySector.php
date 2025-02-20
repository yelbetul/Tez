<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FatalWorkAccidentsBySector extends Model
{
    protected $fillable = [
        'year',
        'group_id',
        'gender',
        'work_accident_fatalities',
        'occupational_disease_fatalities',
    ];

    /**
     * Get the sector that owns the FatalWorkAccidentsBySector.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sector(): BelongsTo
    {
        return $this->belongsTo(SectorCode::class, 'group_id', 'id');
    }
}
