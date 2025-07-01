<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pool extends Model
{
    use HasFactory;

    protected $fillable = ['site_id', 'nom', 'description'];

    public function site() {
        return $this->belongsTo(Site::class);
    }

    public function membres() {
        return $this->hasMany(Membre::class);
    }
}
