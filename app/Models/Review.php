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
        'date_submitted',
        'reviewee_verified'
    ];

    function submission()
    {
        return $this->belongsTo(Submission::class);
    }
    function user()
    {
        return $this->belongsTo(User::class);
    }
}
