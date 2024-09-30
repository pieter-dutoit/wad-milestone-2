<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrolment extends Model
{
    // use HasFactory;
    protected $fillable = ['user_id', 'course_id', 'workshop_id'];

    function workshop()
    {
        return $this->belongsTo(Workshop::class);
    }
    function user()
    {
        return $this->belongsTo(User::class);
    }
    function course()
    {
        return $this->belongsTo(Course::class);
    }
}
