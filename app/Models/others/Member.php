<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom', 'prenom', 'email', 'telephone',
        'site_id', 'pool_id', 'libelle_pool', 'fonction_id', 'face_path'
    ];

    public function site() {
        return $this->belongsTo(Site::class);
    }

    public function pool() {
        return $this->belongsTo(Pool::class);
    }

    public function fonction() {
        return $this->belongsTo(Fonction::class);
    }

    public function user() {
        return $this->hasOne(User::class);
    }
}
