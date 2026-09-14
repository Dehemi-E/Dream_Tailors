<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // 🟢 මේක අලුතින් ආවේ
use Illuminate\Notifications\Notifiable;

class Customer extends Authenticatable // 🟢 Model වෙනුවට Authenticatable දැම්මා
{
    use HasFactory, Notifiable;

   
    protected $fillable = [
        'name',
        'mobile',
        'email',
        'gender',
        'tier',
        'address',
        'password',
    ];

    
    protected $hidden = [
        'password',
        'remember_token',
    ];
}