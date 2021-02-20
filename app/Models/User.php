<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $fillable = [
        'id',
        'username',
        'password',
        'groupid',
        'name',
        'address',
        'email',
        'hash',
        'phone',
        'token',
        'status',
    ];

    public static function boot() {
        parent::boot();
        static::created(function ($user) {
            $user->token = md5(base64_encode($user->name).'.'.base64_encode($user->password).'.'.base64_encode($user->id));
            $user->save();
        });
    }
}
