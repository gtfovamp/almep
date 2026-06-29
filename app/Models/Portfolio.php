<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Portfolio extends Model
{
    protected $table = 'portfolio';
    public $timestamps = true;

    protected $fillable = [
        'title', 'title_en', 'title_az', 'year', 'image_url',
        'description', 'description_en', 'description_az', 'order_index',
    ];

    protected $casts = [
        'order_index' => 'integer',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];
}
