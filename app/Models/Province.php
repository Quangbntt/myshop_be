<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    protected $visible = [
        'id',
        'name',
        'gso_id',
        'district'
    ];
    protected $fillable = [
        'id',
        'name',
        'gso_id',
        'created_at',
        'updateted_at',
    ];
    public function district()
    {
        return $this->hasMany(District::class, 'province_id', 'id');
    }
}
