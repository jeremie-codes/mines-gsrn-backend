<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Message extends Model
{
    use HasFactory;

    protected $table = 'tb_messages';
    
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'closed',
        'content',
        'delivered',
        'nb_trial_check',
        'notification',
        'phone_number',
        'reference',
        'response',
        'sent',
        'sms_from',
        'sms_login',
        'auth_id',
        'merchant_id'
    ];

    protected $casts = [
        'closed' => 'boolean',
        'delivered' => 'boolean',
        'sent' => 'boolean',
        'nb_trial_check' => 'integer',
        'created_at' => 'datetime',
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

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }

    public function scopeSent($query)
    {
        return $query->where('sent', true);
    }

    public function scopeDelivered($query)
    {
        return $query->where('delivered', true);
    }

    public function scopeFailed($query)
    {
        return $query->where('sent', false)->orWhere(function($q) {
            $q->where('sent', true)->where('delivered', false);
        });
    }

    public function getStatusAttribute()
    {
        if ($this->delivered) return 'delivered';
        if ($this->sent) return 'sent';
        return 'failed';
    }
}