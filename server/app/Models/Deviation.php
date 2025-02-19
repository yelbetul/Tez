<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deviation extends Model
{
    protected $fillable = [
        'deviation_code',
        'group_code',
        'group_name',
        'sub_group_code',
        'sub_group_name',
    ];
}
