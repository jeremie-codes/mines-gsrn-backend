<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_sms',
        'reference_flexpaie',
        'order_number',
        'cotisation_id',
        'month',
        'currency',
        'status',
        'phone',
        'amount',
        'callback_response'
    ];

    public function cotisation()
    {
        return $this->belongsTo(Cotisation::class);
    }
}
