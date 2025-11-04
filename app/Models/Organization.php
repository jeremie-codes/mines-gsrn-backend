<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $appends = ['members_count', 'sites_count'];

    public function getSitesCountAttribute()
    {
        return $this->sites()->count() ?? 0;
    }

    public function getMembersCountAttribute()
    {
        return $this->members()->count() ?? 0;
    }

    public function sites()
    {
        return $this->hasMany(Site::class);
    }

    public function members()
    {
        return $this->hasMany(Member::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

}
