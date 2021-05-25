<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    protected $visible = [
        'id',
        'name',
        'gso_id',
        'provincet_id',
        'ward'
    ];
    protected $fillable = [
        'id',
        'name',
        'gso_id',
        'provincet_id',
        'created_at',
        'updateted_at',
    ];
    public function ward()
    {
        return $this->hasMany(Ward::class, 'district_id', 'id');
    }
}
