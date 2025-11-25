<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rapport extends Model
{
    use HasFactory;

    protected $fillable = [
        "substance",
        "date_debut",
        "date_fin",
        "mesure"
    ];

    public function stocks()
    {
        return $this->belongsToMany(Stock::class, 'rapport_stocks')
                ->withPivot('qte')
                ->withTimestamps();
    }

    public function getQteTotalAttribute()
    {
        return $this->stocks->sum(function ($stock) {
            return $stock->pivot->qte;
        });
    }

}
