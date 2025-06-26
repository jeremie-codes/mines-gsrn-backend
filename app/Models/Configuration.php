<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
    use HasFactory;

    protected $table = 'tb_configurations';
    
    protected $fillable = [
        'active',
        'code',
        'schedule_date_format',
        'schedule_date_value',
        'sms_from',
        'sms_login',
        'sms_url',
        'sms_url_check'
    ];

    protected $casts = [
        'active' => 'boolean',
        'created_at' => 'datetime',
        'modified_at' => 'datetime',
    ];

    const UPDATED_AT = 'modified_at';

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}