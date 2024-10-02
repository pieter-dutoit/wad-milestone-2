<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    // use HasFactory;
    protected $fillable = [
        'reviewee_id',
        'submission_id',
        'text',
        'complete',
        'unavailable',
        'reported'
    ];

    function submission()
    {
        return $this->belongsTo(Submission::class);
    }
    function reviewee()
    {
        return $this->belongsTo(User::class);
    }
}
