<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkstationType extends Model
{
    protected $fillable = [
        'workstation_code',
        'workstation_name',
    ];
}
