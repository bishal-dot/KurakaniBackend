<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'fullname',
        'username',
        'email',
        'password',
        'gender',
        'age',
        'verification_photo',
        'profile',
        'coverphoto', 
        'purpose', 
        'job', 
        'interests', 
        'education', 
        'about',
        'is_verified',
        'is_admin'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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
            'is_verified' => 'boolean',
            'interests' => 'array'
        ];
    }

    public function getVerificationPhotoUrlAttribute(){
        return $this->verification_photo ? asset('storage/verification_photos/' . $this->verification_photo) : null;
    }

    public function isAdmin(){
        return $this->role === 'admin';
    }

    public function photos(){
        return $this->hasMany(UserPhoto::class);
    }
     public function matches()
        {
            return $this->hasMany(MatchProfile::class, 'user_id'); // matches owned by this user
        }

        public function matchedUsers()
        {
            return $this->hasManyThrough(
                User::class,
                MatchProfile::class,
                'user_id', // Foreign key on matches table
                'id',      // Foreign key on users table
                'id',      // Local key on users table
                'matched_user_id' // Local key on matches table
            );
        }
}
