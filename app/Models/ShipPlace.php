<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShipPlace extends Model
{
    protected $table = 'shipplace';
    protected $visible = ['id', 'address', 'default', 'created_at', 'updated_at'];
    protected $fillable = [
        'id',
        'user_id',
        'address',
        'default',
        'created_at',
        'updated_at'
    ];
    public function shipUser()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
