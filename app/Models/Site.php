<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Site extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'location',
        'is_active',
        'city_id',
        'membership_counter'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $appends = ['pools_count'];


    public function pools()
    {
        return $this->hasMany(Pool::class);
    }

    public function getPoolsCountAttribute()
    {
        return $this->pools()->count();
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function members()
    {
        return $this->hasMany(Member::class);
    }

    public function coordonateur()
    {
        return $this->hasOne(Member::class)->whereHas('fonction', function ($query) {
            $query->where('name', 'Coordonateur');
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Générer le prochain numéro de membre pour ce site
     */
    public function generateMembershipNumber($cityProvinceCode = null): string
    {
        $provinceCode = $cityProvinceCode ?? 'XXX';
        $siteCode     = $this->code;
        $year         = date('y'); // ex. "25"
        $prefix       = $provinceCode . $siteCode . $year; // ex. "KINDPN25"

        // Récupérer la séquence max après ce préfixe
        $maxSeq = Member::query()
            ->selectRaw(
                "MAX(CAST(SUBSTRING(membershipNumber, ?, 2) AS UNSIGNED)) as max_seq",
                [strlen($prefix) + 1]
            )
            ->where('membershipNumber', 'like', $prefix . '%')
            ->value('max_seq');

        $nextSeq = $maxSeq ? $maxSeq + 1 : 1;
        $sequence = str_pad($nextSeq, 2, '0', STR_PAD_LEFT); // ex. "01"

        return $prefix . $sequence; // ex. "KINDPN2501"
    }

}
