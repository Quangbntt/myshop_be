<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = [
        'id',
        'user_id',
        'product_id',
        'quantity',
        'status',
    ];
}
