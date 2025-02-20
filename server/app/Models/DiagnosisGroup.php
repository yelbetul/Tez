<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DiagnosisGroup extends Model
{
    protected $fillable = [
        'diagnosis_code',
        'group_code',
        'group_name',
        'sub_group_code',
        'sub_group_name'
    ];

    /**
     * Get all of the occupational disease by diagnoses for the DiagnosisGroup.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function occupationalDiseasesByDiagnosis(): HasMany
    {
        return $this->hasMany(OccupationalDiseaseByDiagnosis::class, 'diagnosis_code', 'id');
    }
}
