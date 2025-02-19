<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpecialActivitiesBeforeAccident extends Model
{
    protected $fillable = [
        'special_activity_code',
        'group_code',
        'group_name',
        'sub_group_code',
        'sub_group_name',
    ];
}
