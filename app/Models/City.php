<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'province_code',
        'country_id'
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function townships()
    {
        return $this->hasMany(Township::class);
    }

    public function members()
    {
        return $this->hasMany(Member::class);
    }
}