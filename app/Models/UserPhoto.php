<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPhoto extends Model
{
    protected $table = 'user_photos';
    protected $fillable = ['user_id','photo_path'];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
