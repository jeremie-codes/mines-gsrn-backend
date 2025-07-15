<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cotisation extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'type',
        'amount',
        'currency',
        'status',
        'reference',
        'description',
        'created_at'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($cotisation) {
            $member = Member::find($cotisation->member_id);

            if ($member && $member->first_payment) {
                $member->first_payment = null;
                $member->save();
            }
        });
    }


}
