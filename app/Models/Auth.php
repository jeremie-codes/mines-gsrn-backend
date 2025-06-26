<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class Auth extends Model
{
    use HasFactory;

    protected $table = 'tb_auths';
    
    protected $fillable = [
        'active',
        'code',
        'password',
        'token',
        'username',
        'merchant_id'
    ];

    protected $hidden = [
        'password',
        'token'
    ];

    protected $casts = [
        'active' => 'boolean',
        'created_at' => 'datetime',
        'modified_at' => 'datetime',
    ];

    const UPDATED_AT = 'modified_at';

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }

    public function authTokens()
    {
        return $this->hasMany(AuthToken::class, 'auth_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'auth_id');
    }

    public function setPasswordAttribute($value)
    {
        if ($value) {
            $this->attributes['password'] = Hash::make($value);
        }
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}