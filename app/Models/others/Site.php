<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'localisation'];

    public function pools() {
        return $this->hasMany(Pool::class);
    }

    public function membres() {
        return $this->hasMany(Membre::class);
    }

}
