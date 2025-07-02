<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Presence extends Model
{
    protected $fillable = ['agent_id'];

    public $timestamps = false; // si tu veux gérer created_at manuellement
}
