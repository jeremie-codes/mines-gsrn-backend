<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'date_collecte',
        'substance_code',
        'substance_name',
        'collecteur',
        'qte',
        'mesure',
    ];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    // Relation entre collecteur et membre
    public function membre()
    {
        return $this->belongsTo(Member::class, 'collecteur', 'membershipNumber');
    }

}
