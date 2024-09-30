<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workshop extends Model
{
    // use HasFactory;
    protected $fillable = [
        'day',
        'location',
    ];

    function enrolments()
    {
        return $this->hasMany(Enrolment::class);
    }
}
