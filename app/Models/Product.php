<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Product extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'name',
        'existence',
        'order',
        'sale_price',
        'size',
        'colors',
        'has_embroidery',
        'embroidery',
        'description'
    ];

    protected $casts = [
        'colors' => 'array',
        'has_embroidery' => 'boolean',
    ];



    /**
     * Get the color for the product.
     */
    public function type()
    {
        return $this->belongsTo(ProductType::class);
    }

    /**
     * The products that belong to the Product.
     */
    public function orders()
    {
        return $this->belongsToMany(Order::class, 'orders_products', 'order_id', 'product_id')->withPivot('quantity');
    }

    public function registerMediaConversions(Media $media = null): void
{
    $this
        ->addMediaConversion('preview')
        ->fit(Manipulations::FIT_CROP, 300, 300)
        ->nonQueued();
}
}
