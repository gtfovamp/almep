<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $table = 'products';
    public $timestamps = false; // no created_at / updated_at columns

    protected $fillable = [
        'subcategory_id', 'product_type_id', 'article',
        'name', 'name_en', 'name_az',
        'in_stock', 'order_index',
        'description', 'description_en', 'description_az',
    ];

    protected $casts = [
        'subcategory_id'  => 'integer',
        'product_type_id' => 'integer',
        'in_stock'        => 'boolean',
        'order_index'     => 'integer',
    ];

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class, 'subcategory_id');
    }

    public function productType(): BelongsTo
    {
        return $this->belongsTo(ProductType::class, 'product_type_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class, 'product_id')->orderBy('order_index');
    }

    public function specifications(): HasMany
    {
        return $this->hasMany(ProductSpecification::class, 'product_id')->orderBy('order_index');
    }

    protected static function booted(): void
    {
        // Cascade is at DB level too, but clean physical files on delete.
        static::deleting(function (Product $product) {
            foreach ($product->images as $image) {
                $image->delete();
            }
        });
    }
}
