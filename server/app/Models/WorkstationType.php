<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkstationType extends Model
{
    protected $fillable = [
        'workstation_code',
        'workstation_name',
    ];
    /**
     * Get all work accidents by workstation type.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function workAccidentsByWorkstation(): HasMany
    {
        return $this->hasMany(WorkAccidentByWorkstation::class, 'group_id', 'id');
    }
}
