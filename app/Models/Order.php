<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'orders_id',
        'product_size_id',
        'user_id',
        'shipplace_id',
        'orders_status',
        'orders_quantity',
        'product_price',
        'created_at',
        'updated_at',
        'orders_type',
        'product_cost'
    ];
}
