<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Member extends Authenticatable implements JWTSubject
{
    protected $fillable = [
        'name',
        'full_name',
        'email',
        'phone',
        'active',
        'password',
        'end_password',
    ];

    protected $hidden = [
        'password', 'end_password',
    ];

    // Bắt buộc cho JWT
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
