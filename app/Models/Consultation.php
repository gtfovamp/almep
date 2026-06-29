<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consultation extends Model
{
    protected $table = 'consultations';
    public $timestamps = false; // only created_at present

    protected $fillable = ['name', 'email', 'phone', 'created_at'];

    protected $casts = ['created_at' => 'datetime'];
}
