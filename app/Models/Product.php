<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivityTrait;
use App\Models\ProductImage;
class Product extends Model
{
    use SoftDeletes, LogsActivityTrait;

    protected $fillable = [
        'name',
        'slug',
        'sku',
        'stock',
        'color',
        'size',
        'original_price',
        'sale_price',
        'discount_percent',
        'status',
        'main_image',
        'features',
        'description',
        'ingredients',
        'usage_instructions',
        'shipping_note',
        'support_policy',
        'stt',
        'trangthai',
        'category_id'
    ];

    protected $casts = [
        'features' => 'array',
        'support_policy' => 'array',
        'trangthai' => 'boolean'
    ];

    public function galleryImages()
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'id');
    }
}
