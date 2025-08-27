<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MatchProfile extends Model
{
    protected $table = 'matches';
    protected $fillable = ['user_id', 'matched_user_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function matchedUser()
    {
        return $this->belongsTo(User::class, 'matched_user_id');
    }
}
