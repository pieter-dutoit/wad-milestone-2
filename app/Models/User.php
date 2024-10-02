<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        's_number',
        'role_id',
        'is_activated'
    ];

    function role()
    {
        return $this->belongsTo(Role::class);
    }

    function enrolments()
    {
        return $this->hasMany(Enrolment::class);
    }

    function submissions()
    {
        return $this->hasMany(Submission::class, 'student_id');
    }

    function reviews()
    {
        return $this->hasMany(Review::class, 'reviewee_id');
    }

    function courses()
    {
        return $this->belongsToMany(Course::class, 'enrolments')->withPivot('workshop_id', 'user_id');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
