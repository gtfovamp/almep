<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $table = 'testimonials';
    public $timestamps = true;

    protected $fillable = [
        'name', 'name_en', 'name_az',
        'text', 'text_en', 'text_az',
        'order_index', 'approved',
    ];

    protected $casts = [
        'order_index' => 'integer',
        'approved'    => 'boolean',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];
}
