<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    // use HasFactory;
    protected $fillable = [
        'assessment_id',
        'student_id',
        'date_submitted',
        'score',
        'group_num'
    ];

    function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }
    function student()
    {
        return $this->belongsTo(User::class);
    }
    function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
