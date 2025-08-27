<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User; // <--- import User model

class Interest extends Model
{
    protected $fillable = ['name'];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
