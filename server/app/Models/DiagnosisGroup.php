<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiagnosisGroup extends Model
{
    protected $fillable = [
        'diagnosis_code',
        'group_code',
        'group_name',
        'sub_group_code',
        'sub_group_name',
    ];
}
