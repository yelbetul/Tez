<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InjuryType extends Model
{
    protected $filable = [
        'injury_code',
        'group_code',
        'group_name',
        'sub_group_code',
        'sub_group_name',
    ];
}
