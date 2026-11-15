<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'otp',
        'otp_expire_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
