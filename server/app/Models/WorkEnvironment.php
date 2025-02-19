<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkEnvironment extends Model
{
    protected $fillable = [
        'environment_code',
        'group_code',
        'group_name',
        'sub_group_code',
        'sub_group_name',
    ];
}
