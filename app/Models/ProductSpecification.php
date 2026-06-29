<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductSpecification extends Model
{
    protected $table = 'product_specifications';
    public $timestamps = false;

    protected $fillable = [
        'product_id', 'key', 'key_en', 'key_az',
        'value', 'value_en', 'value_az', 'order_index',
    ];

    protected $casts = [
        'product_id'  => 'integer',
        'order_index' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
