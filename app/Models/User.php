<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table      = "users";
    protected $fillable = [
        'id',
        'uuid',
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
        'province_id',
        'district_id',
        'ward_id',
    ];

    public static function boot() {
        parent::boot();
        static::created(function ($user) {
            $user->token = md5(base64_encode($user->name).'.'.base64_encode($user->password).'.'.base64_encode($user->id));
            $user->save();
        });
    }
    public function shipPlace()
    {
        return $this->hasMany(ShipPlace::class, 'user_id', 'id');
    }
}
