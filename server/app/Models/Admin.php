<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class Admin extends Model
{
    use HasFactory;

    protected $table = 'admins';

    protected $fillable = [
        'name_surname',
        'username',
        'password',
        'api_key',
        'secret_key',
    ];

    protected $hidden = [
        'password'
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    // Şifreyi otomatik hash'leyen mutator
    public function setPasswordAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['password'] = Hash::make($value);
        }
    }

    // Model oluşturulurken rastgele API ve Secret Key üretme
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($admin) {
            $admin->api_key = $admin->api_key ?? Str::random(20);
            $admin->secret_key = $admin->secret_key ?? Str::random(20);
        });
    }
}