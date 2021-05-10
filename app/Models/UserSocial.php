<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSocial extends Model
{
    protected $table      = "user_socials";
    protected $fillable = [
        'id',
        'email',
        'familyName',
        'givenName',
        'uuId',
        'image',
        'name',
        'phone',
        'address',
        'sex',
        'created_at',
        'updated_at',
    ];
}
