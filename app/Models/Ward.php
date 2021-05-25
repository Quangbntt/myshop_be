<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ward extends Model
{
    protected $visible = [
        'id',
        'name',
        'gso_id',
        'district_id'
    ];
    protected $fillable = [
        'id',
        'name',
        'gso_id',
        'district_id',
        'created_at',
        'updateted_at',
    ];
    public function district()
    {
        return $this->belongsTo(District::class, 'id', 'district_id');
    }
}
