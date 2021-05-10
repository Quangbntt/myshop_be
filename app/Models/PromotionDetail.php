<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromotionDetail extends Model
{
    // protected $visible = ['id', 'product_id', 'promotion_id', 'discount', 'promotion_product.name'];
    protected $fillable = [
        'id',
        'product_id',
        'discount',
        'status',
        'promotion_id'
    ];
    public function promotion()
    {
        return $this->belongsTo(PromotionDetail::class, 'id', 'promotion_id');
    }
    public function promotionProduct()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}
