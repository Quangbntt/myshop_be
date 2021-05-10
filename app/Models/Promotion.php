<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    protected $fillable = [
        'id',
        'product_id',
        'discount',
        'type',
        'body',
        'created_at',
        'updateted_at',
    ];
    public function promotionDetail()
    {
        return $this->hasMany(PromotionDetail::class, 'promotion_id', 'id');
    }
}
