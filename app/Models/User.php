<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'member_id',
        'assigned_organization_id',
        'plain_password',
        'username',
        'password',
        'role_id',
        'is_active'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        // 'password' => 'hidden',
        'is_active' => 'boolean',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function assignedOrganization()
    {
        return $this->belongsTo(Organization::class, 'assigned_organization_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function profiles()
    {
        return $this->hasMany(Profile::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
