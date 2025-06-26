<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AuthToken extends Model
{
    use HasFactory;

    protected $table = 'tb_auth_tokens';
    
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'active',
        'code',
        'expires_at',
        'token',
        'auth_id'
    ];

    protected $hidden = [
        'token'
    ];

    protected $casts = [
        'active' => 'boolean',
        'created_at' => 'datetime',
        'expires_at' => 'datetime',
        'modified_at' => 'datetime',
    ];

    const UPDATED_AT = 'modified_at';

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = Str::uuid();
            }
        });
    }

    public function auth()
    {
        return $this->belongsTo(Auth::class, 'auth_id');
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeNotExpired($query)
    {
        return $query->where(function($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }
}