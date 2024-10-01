<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    // use HasFactory;
    protected $fillable = ['course_code', 'course_name'];
    function enrolments()
    {
        return $this->hasMany(Enrolment::class);
    }
    function assessments()
    {
        return $this->hasMany(Assessment::class);
    }
    function users()
    {
        return $this->belongsToMany(User::class, 'enrolments')->withPivot('workshop_id');
    }
    function workshops()
    {
        return $this->belongsToMany(Workshop::class, 'enrolments')->withPivot('workshop_id', 'user_id');
    }
}
