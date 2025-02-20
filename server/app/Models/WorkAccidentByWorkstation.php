<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkAccidentByWorkstation extends Model
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
    ];

    /**
     * Get the related workstation type.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function workstationType(): BelongsTo
    {
        return $this->belongsTo(WorkstationType::class, 'group_id', 'id');
    }
}
