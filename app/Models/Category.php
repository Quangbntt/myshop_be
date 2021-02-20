<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'metatitle',
        'parentid',
        'displayorder',
        'showonhome',
        'status',
    ];
}
