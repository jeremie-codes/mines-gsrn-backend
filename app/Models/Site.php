<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function pools()
    {
        return $this->hasMany(Pool::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function members()
    {
        return $this->hasMany(Member::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Générer le prochain numéro de membre pour ce site
     */
    public function generateMembershipNumber($cityProvinceCode = null)
    {
        // Incrémenter le compteur
        $this->increment('membership_counter');

        // Récupérer le nouveau compteur
        $counter = $this->fresh()->membership_counter;

        // Format: PROVINCE_CODE + SITE_CODE + 5_DIGITS + YEAR
        $provinceCode = $cityProvinceCode ?? 'XXX'; // Code par défaut si pas de ville
        $siteCode = $this->code;
        $counterFormatted = str_pad($counter, 5, '0', STR_PAD_LEFT);
        $year = date('y'); // Année sur 2 chiffres

        return $provinceCode . $siteCode . $counterFormatted . $year;
    }
}
