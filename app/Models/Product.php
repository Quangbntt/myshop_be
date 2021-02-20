<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'size_id',
        'name',
        'code',
        'metatitle',
        'description',
        'image',
        'promotion',
        'includedvat',
        'price',
        'quantity',
        'categoryid',
        'detail',
        'createby',
        'modifydate',
        'modifyby',
        'status',
        'viewcount',
        'material',
        'size'
    ];
}
