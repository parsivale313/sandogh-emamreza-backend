<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable ,HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'password',
        'is_admin',
        'active',
        'approved_by',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_admin' => 'boolean',
        'active' => 'boolean',
    ];

    // رابطه برای ادمینی که تایید کرده
    public function approvedByAdmin()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}

