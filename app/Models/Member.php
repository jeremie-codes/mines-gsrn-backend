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
        'street',
        'chef_id',
        'site_id',
        'city_id',
        'township_id',
        'pool_id',
        'libelle_pool',
        'is_active',
        'face_path',
        'fonction_id',
        'qrcode_url',
        'date_adhesion',
        'category_id',
        'cotisation_id',
        'first_payment',
        'next_payment',
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

    public function cotisations()
    {
        return $this->hasMany(Cotisation::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function chef()
    {
        return $this->belongsTo($this::class, 'chef_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function township()
    {
        return $this->belongsTo(Township::class);
    }

    public function pool()
    {
        return $this->belongsTo(Pool::class);
    }

    public function fonction()
    {
        return $this->belongsTo(Fonction::class);
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function getFullNameAttribute()
    {
        return trim($this->firstname . ' ' . $this->middlename . ' ' . $this->lastname);
    }

    public function getFullAddressAttribute()
    {
        $address = [];

        if ($this->township) {
            $address[] = $this->township->name;
        }

        if ($this->city) {
            $address[] = $this->city->name;
        }

        if ($this->site) {
            $address[] = $this->site->name;
        }

        return implode(', ', $address);
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
