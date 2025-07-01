<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = ['member_id', 'username', 'email', 'mot_de_passe', 'role_id', 'is_active'];

    public function Member() {
        return $this->belongsTo(Member::class);
    }

    public function role() {
        return $this->belongsTo(Role::class);
    }

    public function permissions() {
        return $this->belongsToMany(Permission::class, 'user_permission');
    }

}
