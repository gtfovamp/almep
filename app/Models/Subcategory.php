<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subcategory extends Model
{
    protected $table = 'subcategories';
    public $timestamps = false;

    protected $fillable = [
        'category_id', 'name', 'name_en', 'name_az', 'order_index',
        'image_url', 'description', 'description_en', 'description_az',
    ];

    protected $casts = [
        'category_id' => 'integer',
        'order_index' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'subcategory_id')->orderBy('order_index');
    }
}
