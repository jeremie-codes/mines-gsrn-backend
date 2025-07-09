<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pool extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'name',
        'description',
        'is_active',
        'membership_counter'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function members()
    {
        return $this->hasMany(Member::class);
    }

        public function chefDePool()
    {
        return $this->hasMany(Member::class)->whereHas('fonction', function ($query) {
            $query->where('name', 'Chef de Pool');
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

}
