<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OccupationalDiseaseByDiagnosis extends Model
{
    protected $fillable = [
        'year',
        'diagnosis_code',
        'gender'
    ];

    /**
     * Get the diagnosis group that owns the OccupationalDiseaseByDiagnosis.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function diagnosisGroup(): BelongsTo
    {
        return $this->belongsTo(DiagnosisGroup::class, 'diagnosis_code', 'id');
    }
}
