<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Merchant extends Model
{
    use HasFactory;

    protected $table = 'tb_merchants';
    
    protected $fillable = [
        'active',
        'code',
        'name',
        'own_config',
        'sms_from',
        'sms_login'
    ];

    protected $casts = [
        'active' => 'boolean',
        'own_config' => 'boolean',
        'created_at' => 'datetime',
        'modified_at' => 'datetime',
    ];

    const UPDATED_AT = 'modified_at';

    public function auths()
    {
        return $this->hasMany(Auth::class, 'merchant_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'merchant_id');
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}