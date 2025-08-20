<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'cotisation_id',
        'month',
        'currency',
        'phone',
        'amount',
        'callback_response'
    ];

    public function cotisation()
    {
        return $this->belongsTo(Cotisation::class);
    }
}
