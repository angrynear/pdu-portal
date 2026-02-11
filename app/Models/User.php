<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'contact_number',
        'password',
        'role',
        'account_status',
        'photo',
        'profession',
        'designation',
        'employment_status',
        'employment_started',
        'deactivated_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'deactivated_at' => 'datetime',

    ];

    /* =====================
       ROLE HELPERS
    ====================== */

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    public function isActive(): bool
    {
        return $this->account_status === 'active';

        return is_null($this->deactivated_at);
    }

    public function tasks()
    {
        return $this->hasMany(\App\Models\Task::class, 'assigned_user_id');
    }
}
