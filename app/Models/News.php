<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $table = 'news';
    public $timestamps = true; // created_at & updated_at present

    protected $fillable = [
        'title', 'title_en', 'title_az',
        'cover_image_url', 'blocks',
        'published_at', 'order_index',
    ];

    protected $casts = [
        'blocks'       => 'array',   // JSON stored as TEXT
        'published_at' => 'datetime',
        'order_index'  => 'integer',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];
}
