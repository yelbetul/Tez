<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $fillable = [
        'material_code',
        'group_code',
        'group_name',
        'sub_group_code',
        'sub_group_name',
    ];
}
