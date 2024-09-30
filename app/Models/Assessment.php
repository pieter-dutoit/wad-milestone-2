<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    // use HasFactory;
    protected $fillable = ['title', 'instruction', 'due_date', 'num_reviews', 'max_score', 'course_id', 'type_id'];

    function type()
    {
        return $this->belongsTo(ReviewType::class);
    }
    function course()
    {
        return $this->belongsTo(Course::class);
    }
    function submissions()
    {
        return $this->hasMany(Submission::class);
    }
}
