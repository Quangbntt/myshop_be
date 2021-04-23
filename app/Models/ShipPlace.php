<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShipPlace extends Model
{
    protected $table = 'shipplace';
    protected $fillable = [
        'id',
        'user_id',
        'address',
        'default',
        'created_at',
        'updated_at'
    ];
}
