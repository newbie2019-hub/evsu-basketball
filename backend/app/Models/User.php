<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'year',
        'program',
        'section',
        'address',
        'photo',
        'gender',
        'date_of_birth',
        'height',
        'weight',
        'position',
        'contact',
        'email',
        'password',
        'user_type',
        'email_verified_at',
        'approved_at',
        'declined_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('F j, Y h:i A');
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'declined_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function team()
    {
        return $this->hasOne(TeamPlayer::class, 'user_id', 'id');
    }

    protected function password(): Attribute
    {
        return Attribute::make(
            set: fn($value) => Hash::make($value)
        );
    }

    public function players()
    {
        return $this->hasMany(AthleteCoachAssignee::class, 'coach_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'athlete_id', 'id');
    }

    public function drills()
    {
        return $this->belongsToMany(GameDrill::class);
    }
}
