<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rapport extends Model
{
    use HasFactory;

    protected $fillable = [
        "reference",
        "organization_id",
        "date_debut",
        "date_fin",
    ];

    public static function generateReference()
    {
        return 'RPT-' . strtoupper(uniqid());
    }


    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function stocks()
    {
        return $this->belongsToMany(Stock::class, 'rapport_stocks')
                    ->as('converted')              // 👈 renomme 'pivot'
                    ->withPivot(['qte', 'metric']) // 👈 champs du pivot
                    ->withTimestamps();
    }

}
