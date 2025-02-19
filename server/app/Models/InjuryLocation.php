<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InjuryLocation extends Model
{
    protected $fillable = [
        'injury_location_code',
        'group_code',
        'group_name',
        'sub_group_code',
        'sub_group_name',
    ];
}
