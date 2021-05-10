<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeedbackProduct extends Model
{
    protected $fillable = [
        'id',
        'product_id',
        'customer_id',
        'comment',
        'images',
        'rate',
        'created_at',
        'updated_at',
    ];
}
