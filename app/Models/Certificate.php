<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $table = 'certificates';
    public $timestamps = true;

    protected $fillable = [
        'title', 'title_en', 'title_az', 'image_url', 'order_index',
    ];

    protected $casts = [
        'order_index' => 'integer',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];
}
