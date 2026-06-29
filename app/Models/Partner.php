<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    protected $table = 'partners';
    public $timestamps = true;

    protected $fillable = [
        'name', 'name_en', 'name_az',
        'description', 'description_en', 'description_az',
        'image_url', 'order_index',
    ];

    protected $casts = [
        'order_index' => 'integer',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];
}
