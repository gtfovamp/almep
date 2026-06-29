<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Services\ImageUploadService;

class ProductImage extends Model
{
    protected $table = 'product_images';
    public $timestamps = false;

    protected $fillable = [
        'product_id', 'url', 'alt', 'alt_en', 'alt_az',
        'is_primary', 'order_index', 'image_url',
    ];

    protected $casts = [
        'product_id'  => 'integer',
        'is_primary'  => 'boolean',
        'order_index' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    protected static function booted(): void
    {
        static::deleting(function (ProductImage $image) {
            app(ImageUploadService::class)->deleteByRelativePath($image->url);
            app(ImageUploadService::class)->deleteByRelativePath($image->image_url);
        });
    }
}
