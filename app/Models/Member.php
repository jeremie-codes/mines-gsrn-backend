<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'firstname',
        'lastname',
        'middlename',
        'membershipNumber',
        'phone',
        'gender',
        'birth_date',
        'address',
        'organization_id',
        'site_id',
        'city_id',
        'is_active',
        'face_path',
        'date_adhesion',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($member) {
            $prefix = '6009900705';

            // Récupère le dernier membre enregistré
            $last = self::orderBy('id', 'desc')->first();
            $nextId = $last ? $last->id + 1 : 1;

            // Génère le numéro avec padding à 8 chiffres
            $member->membershipNumber = $prefix . str_pad($nextId, 8, '0', STR_PAD_LEFT);
        });
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function organization()
    {
        return $this->belongsTo(organization::class);
    }

    public function getFullNameAttribute()
    {
        return trim($this->firstname . ' ' . $this->middlename . ' ' . $this->lastname);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function hasUser()
    {
        return $this->user()->exists();
    }
}
