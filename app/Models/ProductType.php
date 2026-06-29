<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductType extends Model
{
    protected $table = 'product_types';
    public $timestamps = false;

    protected $fillable = ['name', 'name_en', 'name_az'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'product_type_id');
    }
}
