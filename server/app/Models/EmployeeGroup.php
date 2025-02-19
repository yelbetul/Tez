<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeGroup extends Model
{
    protected $fillable = [
        'code',
        'employee_count'
    ];
}
