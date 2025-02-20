<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OccDiseaseFatalitiesByOccupation extends Model
{
    protected $fillable = [
        'year',
        'group_id',
        'gender',
        'occ_disease_cases',
        'occ_disease_fatalities'
    ];

    /**
     * Get the occupation group that this record belongs to.
     */
    public function occupationGroup(): BelongsTo
    {
        return $this->belongsTo(OccupationGroup::class, 'group_id', 'id');
    }
}
