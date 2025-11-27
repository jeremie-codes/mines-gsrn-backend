<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rapport extends Model
{
    use HasFactory;

    protected $fillable = [
        "organization_id",
        "date_debut",
        "date_fin",
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

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
