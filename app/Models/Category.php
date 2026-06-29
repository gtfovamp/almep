<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $table = 'categories';
    public $timestamps = false;

    protected $fillable = [
        'name', 'name_en', 'name_az', 'order_index',
        'description', 'description_en', 'description_az', 'icon_url',
    ];

    protected $casts = ['order_index' => 'integer'];

    public function subcategories(): HasMany
    {
        return $this->hasMany(Subcategory::class, 'category_id')->orderBy('order_index');
    }
}
