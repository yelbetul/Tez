<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InjuryCause extends Model
{
    protected $fillable = [
        'injury_cause_code',
        'group_code',
        'group_name',
        'sub_group_code',
        'sub_group_name',
    ];
}
