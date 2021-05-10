<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $fillable = [
        'id',
        'title',
        'review',
        'body',
        'image',
        'created_at',
        'updated_at',
    ];
}
