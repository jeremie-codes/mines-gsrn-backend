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
        'collecteur',
        'qte',
        'mesure',
    ];

    public function rapports()
    {
        return $this->belongsToMany(Rapport::class, 'rapport_stocks')
                ->withPivot('qte')
                ->withTimestamps();
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

}
