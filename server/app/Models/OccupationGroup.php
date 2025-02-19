<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OccupationGroup extends Model
{
    protected $fillable = [
        'code',
        'occupation_code',
        'occupation_name',
        'group_code',
        'group_name',
        'sub_group_code',
        'sub_group_name',
        'pure_code',
        'pure_name',
    ];
}
